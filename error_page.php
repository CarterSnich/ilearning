<?php
require 'connection.php';
require 'utils.php';

if (!isset($_SESSION['REPORT_MSG']) && $_SESSION['REPORT_MSG']['code'] != 3) {
    redirectToPage('login.php');
}

$report = $_SESSION['REPORT_MSG'];
unset($_SESSION['REPORT_MSG']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
</head>
<body>
    <pre>
        Code: <?= $report['code'] ?>
        Message: <?= $report['msg'] ?>
        Body: <?= $report['body'] ?>
    </pre>
</body>
</html>