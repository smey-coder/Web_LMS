<?php
$host = "localhost";
$user = "SmeyKh";
$password = "hello123(*)";
$dbName = "library_management_system";

$conn = new mysqli($host, $user,$password, $dbName);

if ($conn->connect_error){
    die("Connection failed: ". $conn->connect_error);
}
?>
