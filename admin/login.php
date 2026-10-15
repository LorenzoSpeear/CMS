<?php
session_start();
require_once("../includes/database.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $wachtwoord = $_POST['wachtwoord'];
    $sql = "SELECT * FROM beheerders WHERE gebruikersnaam = :gebruikersnaam";
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':gebruikersnaam', $gebruikersnaam);
    $statement->execute();
    $beheerder = $statement->fetch(PDO::FETCH_ASSOC);

    if ($beheerder && password_verify($wachtwoord, $beheerder['wachtwoord_hash'])) {
        session_regenerate_id(true);
        $_SESSION['beheerder_id'] = $beheerder['id'];
        header('location: index.php');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
</head>

<body>
    <form method="post">
        <label for="gebruikersnaam">gebruikersnaam<input type="text" id="gebruikersnaam" name="gebruikersnaam"></label>
        <label for="wachtwoord">wachtwoord<input type="password" id="wachtwoord" name="wachtwoord"></label>
        <button type="submit">inloggen</button>
    </form>
</body>

</html>