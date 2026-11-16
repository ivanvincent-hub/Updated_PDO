
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once("../database.php");

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: view_students.php");
    exit;
}

// Get the existing student
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM students WHERE student_id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: view_students.php");
    exit;
}

$error = "";

if (isset($_POST['update'])) {
    $name = trim($_POST['name'] ?? '');
    $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
    $tutor = filter_input(INPUT_POST, 'tutor', FILTER_VALIDATE_INT);
    $user_id = (int) $_SESSION["user_id"];

    if ($name === "" || $age === false || $age === null ||
        $age < 1 || !$tutor || $tutor < 1) {
        $error = "Enter a valid name, age, and tutor.";
    } else {
        // Check that the selected tutor exists
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
                "UPDATE students
                 SET student_name = ?,
                     age = ?,
                     tutor_id = ?,
                     last_updated_by = ?,
                     last_updated = NOW()
                 WHERE student_id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "siiii",
                $name,
                $age,
                $tutor,
                $user_id,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: view_students.php");
                exit;
            } else {
                $error = "Update failed: " . mysqli_stmt_error($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Reload tutor options for the form
$tutors = mysqli_query(
    $conn,
    "SELECT tutor_id, tutor_name FROM tutors ORDER BY tutor_name"
);

if (!$tutors) {
    die("Unable to load tutors.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2>Edit Student</h2>

<?php if ($error !== "") { ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php } ?>

<form method="POST">
    Name:<br>
    <input
        type="text"
        name="name"
        required
        value="<?= htmlspecialchars($_POST['name'] ?? $data['student_name']) ?>"
    >
    <br><br>

    Age:<br>
    <input
        type="number"
        name="age"
        min="1"
        required
        value="<?= htmlspecialchars($_POST['age'] ?? $data['age']) ?>"
    >
    <br><br>

    Tutor:<br>
    <select name="tutor" required>
        <option value="">-- Select a tutor --</option>

        <?php while ($t = mysqli_fetch_assoc($tutors)) { ?>
            <option
                value="<?= (int)$t['tutor_id'] ?>"
                <?= (($_POST['tutor'] ?? $data['tutor_id']) == $t['tutor_id']) ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($t['tutor_name']) ?>
            </option>
        <?php } ?>
    </select>

    <br><br>
    <button type="submit" name="update">Update</button>
</form>

<br>
<a href="view_students.php">Back to Students</a>

</body>
</html>
