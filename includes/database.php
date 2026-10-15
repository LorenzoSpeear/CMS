<?php  
$host = "mysql_db";
$database = "CMS";
$gebruikersnaam = "root";
$wachtwoord =  "root";
$dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
$pdo = new PDO($dsn,$gebruikersnaam,$wachtwoord);

// $sql = "SELECT COUNT(*) FROM genres";
// $resultaat = $pdo->query($sql);
// $aantal = $resultaat->fetchColumn();

// echo $aantal;