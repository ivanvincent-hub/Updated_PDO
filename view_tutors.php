
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once("../database.php");

$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM tutors";

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $sql .= " WHERE tutor_name LIKE '%$search%'
              OR specialization LIKE '%$search%'
              OR email LIKE '%$search%'";
}

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tutors</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2>Tutors</h2>

<p>
    Logged in as:
    <?= htmlspecialchars($_SESSION["full_name"] ?? $_SESSION["username"]); ?>
</p>

<a href="add_tutor.php">Add Tutor</a> |
<a href="../index.php">Home</a> |
<a href="../logout.php">Logout</a>

<br><br>

<form method="GET">
    <input type="text"
           name="search"
           placeholder="Search Tutor"
           value="<?= htmlspecialchars($search); ?>">

    <input type="submit" value="Search">
</form>

<br>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Specialization</th>
    <th>Email</th>
    <th>Added By</th>
    <th>Last Updated By</th>
    <th>Last Updated</th>
    <th>Action</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= htmlspecialchars($row['tutor_id']); ?></td>
    <td><?= htmlspecialchars($row['tutor_name']); ?></td>
    <td><?= htmlspecialchars($row['specialization']); ?></td>
    <td><?= htmlspecialchars($row['email']); ?></td>
    <td><?= htmlspecialchars($row['added_by'] ?? 'N/A'); ?></td>
    <td><?= htmlspecialchars($row['last_updated_by'] ?? 'N/A'); ?></td>
    <td><?= htmlspecialchars($row['last_updated'] ?? 'Never'); ?></td>
    <td>
        <a href="edit_tutor.php?id=<?= (int)$row['tutor_id'] ?>">Edit</a>
        |
        <a href="delete_tutor.php?id=<?= (int)$row['tutor_id'] ?>">Delete</a>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>
