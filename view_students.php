
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once("../database.php");

$search = $_GET['search'] ?? '';

$stmt = mysqli_prepare(
    $conn,
    "SELECT students.*,
            added.username AS added_by_name,
            updated.username AS updated_by_name
     FROM students
     LEFT JOIN users AS added
        ON students.added_by = added.user_id
     LEFT JOIN users AS updated
        ON students.last_updated_by = updated.user_id
     WHERE student_name LIKE CONCAT('%', ?, '%')"
);

mysqli_stmt_bind_param($stmt, "s", $search);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Query failed: " . htmlspecialchars(mysqli_error($conn)));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Students</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2>Students</h2>

<p>
    Logged in as:
    <?= htmlspecialchars($_SESSION["full_name"] ?? $_SESSION["username"] ?? "User") ?>
</p>

<a href="add_student.php">Add Student</a> |
<a href="../index.php">Home</a> |
<a href="../logout.php">Logout</a>

<br><br>

<form method="GET">
    <input
        type="text"
        name="search"
        placeholder="Search Student"
        value="<?= htmlspecialchars($search); ?>"
    >

    <input type="submit" value="Search">
</form>

<br>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Age</th>
    <th>Tutor ID</th>
    <th>Added By</th>
    <th>Last Updated By</th>
    <th>Last Updated</th>
    <th>Action</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= (int)$row['student_id'] ?></td>
    <td><?= htmlspecialchars($row['student_name']) ?></td>
    <td><?= htmlspecialchars($row['age']) ?></td>
    <td><?= htmlspecialchars($row['tutor_id']) ?></td>
    <td><?= htmlspecialchars($row['added_by_name'] ?? 'N/A') ?></td>
    <td><?= htmlspecialchars($row['updated_by_name'] ?? 'N/A') ?></td>
    <td><?= htmlspecialchars($row['last_updated'] ?? 'Never') ?></td>
    <td>
        <a href="edit_student.php?id=<?= (int)$row['student_id'] ?>">Edit</a>
        |
        <a href="delete_student.php?id=<?= (int)$row['student_id'] ?>">Delete</a>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>
