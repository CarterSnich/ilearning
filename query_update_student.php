<?php
require_once 'connection.php';
require 'utils.php';

if (isset($_POST['update-student'])) {
    $areEmpty =
        empty($_POST['firstname']) ||
        empty($_POST['lastname']) ||
        empty($_POST['email']) ||
        empty($_POST['dateofbirth']) ||
        empty($_POST['gender']) ||
        empty($_POST['address']) ||
        empty($_POST['phonenumber']);

    if ($areEmpty) {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 1,
            'msg' => 'Add student',
            'body' => 'Please, fill-up required fields.'
        );
        redirectToPage("edit-student.php?student={$_GET['student']}");
    }

    try {
        $conn->beginTransaction();

        if (!empty($_FILES['profilepicture']['name'])) {
            $sql = 'UPDATE `usertable` SET `Avatar` = :1 WHERE Username = :2';
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':1', $_FILES['profilepicture']['name']);
            $stmt->bindParam(':2', $_GET['student']);

            if (!$stmt->execute()) {
                $conn->rollBack();
                $_SESSION['REPORT_MSG'] = array(
                    'code' => 1,
                    'msg' => 'Upload image',
                    'body' => 'Failed to upload image'
                );
                redirectToPage("edit-student.php?student={$_GET['student']}");
            }
        }

        $sql =
            'UPDATE 
                `usertable`
            SET
                `Firstname` = :1, 
                `Lastname` = :2, 
                `Email` = :3, 
                `DateOfBirth` = :4, 
                `Gender` = :5, 
                `Address` = :6, 
                `PhoneNumber` = :7
            WHERE
                Username = :8
            ';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':1', $_POST['email']);
        $stmt->bindParam(':2', $_POST['firstname']);
        $stmt->bindParam(':3', $_POST['lastname']);
        $stmt->bindParam(':4', $_POST['dateofbirth']);
        $stmt->bindParam(':5', $_POST['gender']);
        $stmt->bindParam(':6', $_POST['address']);
        $stmt->bindParam(':7', $_POST['phonenumber']);

        if ($stmt->execute()) {
            if (!empty($_FILES['profilepicture']['name'])) {
                $uploadResult = uploadImage($_FILES['profilepicture'], 'student_avatars/');
                if (!$uploadResult['success']) {
                    $conn->rollBack();
                    $_SESSION['REPORT_MSG'] = array(
                        'code' => 2,
                        'msg' => 'Upload image',
                        'body' => $uploadResult['msg']
                    );
                    redirectToPage("edit-student.php?student={$_GET['student']}");
                }
            }
            $conn->commit();
            $_SESSION['REPORT_MSG'] = array(
                'code' => 0,
                'msg' => 'Student update',
                'body' => 'Updated successfully!'
            );
            redirectToPage('./students.php');
        } else {
            $conn->rollBack();
            $_SESSION['REPORT_MSG'] = array(
                'code' => 2,
                'msg' => 'Upload image',
                'body' => $uploadResult['msg']
            );
            redirectToPage("edit-student.php?student={$_GET['student']}");
        }
    } catch (\Throwable $th) {
        if ($conn->inTransaction()) $conn->rollBack();
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Query failed.',
            'body' => $th->getMessage()
        );
        redirectToPage("edit-student.php?student={$_GET['student']}");
    }
}
