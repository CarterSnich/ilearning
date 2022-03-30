<?php
require_once './connection.php';
require 'utils.php';



// get activities
try {
    $sql = "SELECT * FROM $_GET[subj] WHERE Id = :1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':1', $_GET['id']);
    $stmt->execute();
    $activity = $stmt->fetch();
} catch (\Throwable $th) {
    $_SESSION['REPORT_MSG'] = array(
        'code' => 1,
        'msg' => 'Query failed.',
        'body' => $th->getMessage()
    );
}
