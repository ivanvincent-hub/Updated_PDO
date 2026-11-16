
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once("../database.php");

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: view_tutors.php");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM tutors WHERE tutor_id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: view_tutors.php");
    exit;
}

if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $specialization = trim($_POST['specialization']);
    $email = trim($_POST['email']);
    $user_id = (int) $_SESSION["user_id"];

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE tutors
         SET tutor_name = ?,
             specialization = ?,
             email = ?,
             last_updated_by = ?,
             last_updated = NOW()
         WHERE tutor_id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sssii",
        $name,
        $specialization,
        $email,
        $user_id,
        $id
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: view_tutors.php");
        exit;
    } else {
        $error = "Update failed: " . mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Tutor</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2>Edit Tutor</h2>

<?php if (isset($error)) { ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php } ?>

<form method="POST">
    Name:<br>
    <input
        type="text"
        name="name"
        value="<?= htmlspecialchars($data['tutor_name'] ?? '') ?>"
        required
    >
    <br><br>

    Specialization:<br>
    <input
        type="text"
        name="specialization"
        value="<?= htmlspecialchars($data['specialization'] ?? '') ?>"
    >
    <br><br>

    Email:<br>
    <input
        type="email"
        name="email"
        value="<?= htmlspecialchars($data['email'] ?? '') ?>"
    >
    <br><br>

    <button type="submit" name="update">Update</button>
</form>

<br>
<a href="view_tutors.php">Back to Tutors</a>

</body>
</html>
