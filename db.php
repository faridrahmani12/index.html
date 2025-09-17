<<?php
$host = "localhost"; 
$user = "root";      
$pass = "";          
$db   = "skole";     

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Feil ved tilkobling: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
