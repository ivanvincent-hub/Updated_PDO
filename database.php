<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "tutor"
);

if(!$conn){
    die("Connection Failed: " . mysqli_connect_error());
}

?>