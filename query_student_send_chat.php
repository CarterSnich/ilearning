<?php

require_once 'connection.php';
require 'utils.php';


if (!isset($_SESSION['USER_LOGIN'])) redirectToPage('./login.php');
$studentUsername = $_SESSION['USER_LOGIN']['username'];

if (isset($_POST['send-message'])) {
    try {
        $conn->beginTransaction();

        $sql = "SELECT username FROM admins LIMIT 1";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute()) {
            $recipient = $stmt->fetch();

            $sql = "INSERT INTO chats (Sender, Recipient, Message) VALUES (:1, :2, :3)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':1', $studentUsername);
            $stmt->bindParam(':2', $recipient['username']);
            $stmt->bindParam(':3', $_POST['messagecontent']);

            if ($stmt->execute()) {
                $conn->commit();
            } else {
                $conn->rollBack();
                $_SESSION['REPORT_MSG'] = array(
                    'code' => 3,
                    'msg' => 'Query failed.',
                    'body' => 'Failed to send message.'
                );
            }
        } else {
            $conn->rollBack();
            $_SESSION['REPORT_MSG'] = array(
                'code' => 3,
                'msg' => 'Query failed.',
                'body' => 'Failed to send message.'
            );
        }
    } catch (\Throwable $th) {
        if ($conn->inTransaction()) $conn->rollBack();
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Server error.',
            'body' => $th->getMessage()
        );
    }
}

$parsedUrl = parse_url($_SERVER['HTTP_REFERER']);
if (strpos($parsedUrl, 'chat=1')) {
    redirectToPage($_SERVER['HTTP_REFERER']);
} else {
    redirectToPage($_SERVER['HTTP_REFERER']);    
}
