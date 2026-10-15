<?php
session_start();
if (empty($_SESSION['beheerder_id'])){
    header('Location: /CMS/admin/login.php');
    exit;
}
