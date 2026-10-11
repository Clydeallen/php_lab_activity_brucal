<?php
$host = "localhost";
$username = "root";
$password = "";
$db_name = "php";

$conn = new mysqli("localhost", "root", "", "php");

if ($conn->connect_error) {
    die("Connection failed: " . 
$conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>