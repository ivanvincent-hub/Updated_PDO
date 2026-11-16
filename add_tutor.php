
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once("../database.php");

if (isset($_POST['save'])) {
    $name = trim($_POST['name']);
    $specialization = trim($_POST['specialization']);
    $email = trim($_POST['email']);

    $user_id = (int) $_SESSION["user_id"];

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO tutors
        (tutor_name, specialization, email, added_by,
         last_updated_by, last_updated)
        VALUES (?, ?, ?, ?, ?, NOW())"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sssii",
        $name,
        $specialization,
        $email,
        $user_id,
        $user_id
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: view_tutors.php");
        exit;
    } else {
        $error = "Failed to save tutor: " . mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Tutor</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2>Add Tutor</h2>

<?php if (isset($error)) { ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php } ?>

<form method="POST">
    Name:<br>
    <input type="text" name="name" required><br><br>

    Specialization:<br>
    <input type="text" name="specialization"><br><br>

    Email:<br>
    <input type="email" name="email"><br><br>

    <button type="submit" name="save">Save</button>
</form>

<br>
<a href="view_tutors.php">Back to Tutors</a>

</body>
</html>
