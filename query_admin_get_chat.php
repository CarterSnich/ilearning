<?php
require_once 'connection.php';
require 'utils.php';


if (!isset($_SESSION['ADMIN_LOGIN'])) redirectToPage('./admin_login.php');
$username = $_SESSION['ADMIN_LOGIN']['username'];

try {

    $sql =
        "SELECT 
            *,
            CONCAT(Firstname, ' ', Lastname) AS StudentName
        FROM
            usertable
        WHERE
            Username = :1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':1', $_POST['studentUsername']);

    if ($stmt->execute()) {
        $studentData = $stmt->fetch();

        $sql =
            "SELECT 
                * 
            FROM 
                chats 
            WHERE 
                Sender = :1 AND Recipient = :2
                OR 
                Sender = :2 AND Recipient = :1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':1', $username);
        $stmt->bindParam(':2', $_POST['studentUsername']);

        if ($stmt->execute()) {
            $data = array(
                "studentData" => $studentData,
                "messages" => $stmt->fetchAll()
            );
            $report_msg = array(
                'code' => 0,
                'msg' => '',
                'body' => $data
            );
        } else {
            $report_msg = array(
                'code' => 3,
                'msg' => 'Query failed',
                'body' => 'Failed to fetch messages.'
            );
        }
    } else {
        $report_msg = array(
            'code' => 3,
            'msg' => 'Query failed',
            'body' => 'Failed to fetch messages.'
        );
    }
} catch (\Throwable $th) {
    $report_msg = array(
        'code' => 3,
        'msg' => 'Server error',
        'body' => $th->getMessage()
    );
}

echo json_encode($report_msg);
