<?php
$servername = "162.55.215.244";
$dbname = "croupier-assistant";
$username_db = "croupier-as_usr";
$password_db = "Cj1Q79pYEn7T9ety";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>