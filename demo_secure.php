<?php
if(isset($_POST['comment']))
{
    $comment = htmlspecialchars(
        $_POST['comment'],
        ENT_QUOTES,
        'UTF-8'
    );

    echo $comment;
}
?>

<form method="POST">
    <input type="text" name="comment">
    <button type="submit">Submit</button>
</form>