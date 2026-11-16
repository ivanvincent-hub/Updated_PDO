
<?php
session_start();
require_once "database.php";

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Please enter your username and password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT user_id, first_name, last_name,
                    username, password
             FROM users
             WHERE username = ?
             LIMIT 1"
        );

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (
            $user &&
            password_verify($password, $user["password"])
        ) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = (int) $user["user_id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["full_name"] =
                $user["first_name"] . " " . $user["last_name"];

            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }

        $stmt->close();
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
    <title>User Login</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .auth-box {
            max-width: 400px;
            margin: 60px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 12px #0002;
        }

        .auth-box label {
            display: block;
            margin-top: 14px;
            font-weight: bold;
        }

        .auth-box input {
            box-sizing: border-box;
            width: 100%;
            padding: 11px;
            margin-top: 5px;
        }

        .auth-box button {
            width: 100%;
            margin-top: 18px;
            padding: 11px;
            cursor: pointer;
        }

        .error { color: #b91c1c; }
    </style>
</head>

<body>
    <div class="auth-box">
        <h1>Online Tutoring System</h1>
        <h2>Login</h2>

        <?php if ($error !== ""): ?>
            <p class="error"><?= e($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label>Username</label>
            <input type="text" name="username"
                required autocomplete="username"
                value="<?= e($_POST["username"] ?? "") ?>">

            <label>Password</label>
            <input type="password" name="password"
                required autocomplete="current-password">

            <button type="submit">Login</button>
        </form>

        <p>Don't have an account?
            <a href="register.php">Register here</a>
        </p>
    </div>
</body>
</html>
