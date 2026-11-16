
<?php
session_start();

// Prevent access without logging in.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Tutoring System</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <h1>Online Tutoring System</h1>

    <p>
        Welcome,
        <strong>
            <?= e($_SESSION["full_name"] ?? $_SESSION["username"]) ?>
        </strong>!
    </p>

    <ul>
        <li>
            <a href="tutors/view_tutors.php">
                Manage Tutors
            </a>
        </li>

        <li>
            <a href="students/view_students.php">
                Manage Students
            </a>
        </li>

        <li>
            <a href="logout.php">
                Logout
            </a>
        </li>
    </ul>
</body>
</html>
