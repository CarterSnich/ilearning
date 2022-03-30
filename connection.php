<?php
session_start();
date_default_timezone_set('Asia/Manila');

$server = 'localhost';
$db = 'registration';
$username = 'root';
$password = '';


try {
    $conn = new PDO("mysql:host=$server;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // echo 'Connected successfully!';
} catch (\PDOException $e) {
    echo 'Connection failed.<br>'.$e->getMessage();
    exit();
}