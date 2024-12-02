<?php
$plainPassword = '123'; // Replace with your desired password
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

echo $hashedPassword;
?>
