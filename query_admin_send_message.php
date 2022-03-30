<?php

require_once 'connection.php';
require 'utils.php';


if (!isset($_SESSION['ADMIN_LOGIN'])) redirectToPage('./admin_login.php');
$username = $_SESSION['ADMIN_LOGIN']['username'];

if (isset($_POST['send-message'])) {
    try {
        $sql = "INSERT INTO chats (Sender, Recipient, Message) VALUES (:1, :2, :3)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':1', $username);
        $stmt->bindParam(':2', $_POST['studentusername']);
        $stmt->bindParam(':3', $_POST['messagecontent']);

        if ($stmt->execute()) {
            redirectToPage("chat.php?view=$_POST[studentusername]");
        } else {
            $_SESSION['REPORT_MSG'] = array(
                'code' => 3,
                'msg' => 'Query failed.',
                'body' => 'Failed to send message.'
            );
        }
    } catch (\Throwable $th) {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Server error.',
            'body' => $th->getMessage()
        );
    }
} else {
    redirectToPage('admin_dashboard.php');
}
