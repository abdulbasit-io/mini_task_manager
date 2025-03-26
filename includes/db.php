<?php
# Database Connection Logic
$host = "localhost";
$db_name = "task_manager";
$username = "root";
$password = "Athena_db1";

try {
  $conn =  new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}

?>