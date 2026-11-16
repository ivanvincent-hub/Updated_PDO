
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
    "DELETE FROM tutors WHERE tutor_id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: view_tutors.php");
    exit;
}

$error = mysqli_stmt_error($stmt);
mysqli_stmt_close($stmt);

die("Unable to delete tutor: " . htmlspecialchars($error));
?>
