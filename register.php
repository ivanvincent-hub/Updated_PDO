
<?php
session_start();
require_once "database.php";

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST["first_name"] ?? "");
    $last_name = trim($_POST["last_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if (
        $first_name === "" ||
        $last_name === "" ||
        $email === "" ||
        $username === "" ||
        $password === "" ||
        $confirm_password === ""
    ) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($username) < 4) {
        $error = "Username must be at least 4 characters.";
    } elseif (strlen($password) < 8) {
    $error = "Password must be at least 8 characters.";
} elseif (!preg_match('/[A-Z]/', $password)) {
    $error = "Password must contain at least one uppercase letter.";
} elseif (!preg_match('/[a-z]/', $password)) {
    $error = "Password must contain at least one lowercase letter.";
} elseif (!preg_match('/[0-9]/', $password)) {
    $error = "Password must contain at least one number.";
} elseif ($password !== $confirm_password) {
    $error = "Passwords do not match.";
} else {
        $check = $conn->prepare(
            "SELECT user_id FROM users
             WHERE username = ? OR email = ? LIMIT 1"
        );

        $check->bind_param("ss", $username, $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Username or email is already registered.";
        } else {
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users
                (first_name, last_name, email, username, password, address)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssssss",
                $first_name,
                $last_name,
                $email,
                $username,
                $hashed_password,
                $address
            );

            if ($stmt->execute()) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Registration failed. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
    }
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
    <title>User Registration</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .auth-box {
            max-width: 450px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 12px #0002;
        }

        .auth-box label {
            display: block;
            margin-top: 12px;
            font-weight: bold;
        }

        .auth-box input {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }

        .auth-box button {
            margin-top: 18px;
            padding: 10px 20px;
            cursor: pointer;
        }

        .error { color: #b91c1c; }
        .success { color: #166534; }
    </style>
</head>

<body>
    <div class="auth-box">
        <h1>Create Account</h1>

        <?php if ($error !== ""): ?>
            <p class="error"><?= e($error) ?></p>
        <?php endif; ?>

        <?php if ($success !== ""): ?>
            <p class="success"><?= e($success) ?></p>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <label>First Name *</label>
            <input type="text" name="first_name" required
                value="<?= e($_POST["first_name"] ?? "") ?>">

            <label>Last Name *</label>
            <input type="text" name="last_name" required
                value="<?= e($_POST["last_name"] ?? "") ?>">

            <label>Email *</label>
            <input type="email" name="email" required
                value="<?= e($_POST["email"] ?? "") ?>">

            <label>Username *</label>
            <input type="text" name="username"
                minlength="4" required
                autocomplete="username"
                value="<?= e($_POST["username"] ?? "") ?>">

            <label>Address</label>
            <input type="text" name="address"
                value="<?= e($_POST["address"] ?? "") ?>">

            <label>Password * (minimum 8 characters)</label>
            <input type="password" name="password"
                minlength="8" required
                autocomplete="new-password">

            <label>Confirm Password *</label>
            <input type="password" name="confirm_password"
                minlength="8" required
                autocomplete="new-password">

            <button type="submit">Register</button>
        </form>

        <p>Already have an account?
            <a href="login.php">Login here</a>
        </p>
    </div>
</body>
</html>
