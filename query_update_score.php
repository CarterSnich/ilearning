<?php

require_once 'connection.php';
require 'utils.php';

if (isset($_POST['save-score-btn'])) {
    $subject = $_GET['subject'];
    $activityId = $_GET['id'];
    $username = $_POST['studentusername'];
    $score = $_POST['score'];

    try {
        $sql = "UPDATE submissions SET Score = :1 WHERE Subject = :2 AND ActivityId = :3 AND StudentUsername = :4";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':1', $score);
        $stmt->bindParam(':2', $subject);
        $stmt->bindParam(':3', $activityId);
        $stmt->bindParam(':4', $username);

        if ($stmt->execute()) {
            $_SESSION['REPORT_MSG'] = array(
                'code' => 0,
                'msg' => 'Update score',
                'body' => 'Score updated successfully.'
            );
        } else {
            $_SESSION['REPORT_MSG'] = array(
                'code' => 3,
                'msg' => 'Server error',
                'body' => 'Failed to save score.'
            );
        }
    } catch (\Throwable $th) {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Query failed',
            'body' => $th->getMessage()
        );
    }
    redirectToPage("view-activity.php?subj=$subject&id=$activityId");
} else {
    redirectToPage('admin_dashboard.php');
}
