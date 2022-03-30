<?php
require_once 'connection.php';
require 'utils.php';

if (isset($_POST['submit-work-button'])) {
    $studentUsername = $_SESSION['USER_LOGIN']['username'];
    $subject = $_GET['subject'];
    $activityId = $_GET['id'];
    $uploadedFile = $_FILES['uploaded-pdf'];

    $uploadResult = uploadAnswer($uploadedFile);

    if ($uploadResult['success']) {
        try {
            $sql = "INSERT INTO submissions (Subject, ActivityId, StudentUsername, UploadedFile) VALUES (:1, :2, :3, :4)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':1', $subject);
            $stmt->bindParam(':2', $activityId);
            $stmt->bindParam(':3', $studentUsername);
            $stmt->bindParam(':4', $uploadedFile['name']);

            if (!$stmt->execute()) {
                $_SESSION['REPORT_MSG'] = array(
                    'code' => 3,
                    'msg' => 'Query failed.',
                    'body' => 'Server error.'
                );
            }
        } catch (\Throwable $th) {
            $_SESSION['REPORT_MSG'] = array(
                'code' => 3,
                'msg' => 'Query failed.',
                'body' => $th->getMessage()
            );
        }
    } else {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 2,
            'msg' => 'Upload failed.',
            'body' => $uploadResult['msg']
        );
    }
    redirectToPage("activity.php?subject=$subject&id=$activityId");
} else {
    $_SESSION['REPORT_MSG'] = array(
        'code' => 1,
        'msg' => 'Invalid access.',
        'body' => 'Request forbidden for invalid access.'
    );
    redirectToPage('home.php');
}
