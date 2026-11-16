
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once("../database.php");

$error = "";

if (isset($_POST['save'])) {
    $name = trim($_POST['name']);
    $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
    $tutor = filter_input(INPUT_POST, 'tutor', FILTER_VALIDATE_INT);
    $user_id = (int) $_SESSION["user_id"];

    if ($name === "" || $age === false || $age === null ||
        $age < 1 || !$tutor || $tutor < 1) {
        $error = "Please enter a valid name, age, and tutor.";
    } else {
        $check = mysqli_prepare(
            $conn,
            "SELECT tutor_id FROM tutors WHERE tutor_id = ?"
        );
        mysqli_stmt_bind_param($check, "i", $tutor);
        mysqli_stmt_execute($check);
        $check_result = mysqli_stmt_get_result($check);
        $tutor_exists = mysqli_fetch_assoc($check_result);
        mysqli_stmt_close($check);

        if (!$tutor_exists) {
            $error = "Please select an existing tutor.";
        } else {
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO students
                 (student_name, age, tutor_id, added_by,
                  last_updated_by, last_updated)
                 VALUES (?, ?, ?, ?, ?, NOW())"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "siiii",
                $name,
                $age,
                $tutor,
                $user_id,
                $user_id
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: view_students.php");
                exit;
            } else {
                $error = "Failed to save student: " .
                         mysqli_stmt_error($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }
}

$tutors = mysqli_query(
    $conn,
    "SELECT tutor_id, tutor_name FROM tutors ORDER BY tutor_name"
);

if (!$tutors) {
    die("Unable to load tutors: " . htmlspecialchars(mysqli_error($conn)));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2>Add Student</h2>

<?php if ($error !== "") { ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php } ?>

<form method="POST">
    Student Name:<br>
    <input
        type="text"
        name="name"
        required
        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
    >
    <br><br>

    Age:<br>
    <input
        type="number"
        name="age"
        min="1"
        required
        value="<?= htmlspecialchars($_POST['age'] ?? '') ?>"
    >
    <br><br>

    Tutor:<br>
    <select name="tutor" required>
        <option value="">-- Select a tutor --</option>

        <?php while ($t = mysqli_fetch_assoc($tutors)) { ?>
            <option
                value="<?= (int)$t['tutor_id'] ?>"
                <?= (($_POST['tutor'] ?? '') == $t['tutor_id']) ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($t['tutor_name']) ?>
            </option>
        <?php } ?>
    </select>

    <br><br>

    <button type="submit" name="save">Save</button>
</form>

<br>
<a href="view_students.php">Back to Students</a>

</body>
</html>
