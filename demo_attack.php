<?php
if(isset($_POST['comment']))
{
    echo $_POST['comment']; // Vulnerable
}
?>

<form method="POST">
    <input type="text" name="comment">
    <button type="submit">Submit</button>
</form>