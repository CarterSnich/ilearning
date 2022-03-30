<?php
require_once 'connection.php';
require 'utils.php';

if (isset($_POST['delete-student-btn'])) {
    try {
        $sql = "SELECT * FROM usertable WHERE Username = :1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':1', $_POST['delete-student-btn']);

        if ($stmt->execute()) {
            $studentData = $stmt->fetch();
            $conn->beginTransaction();

            $sql = "DELETE FROM usertable WHERE Username = :1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':1', $_POST['delete-student-btn']);

            if ($stmt->execute()) {
                if (file_exists("student_avatars/{$studentData['Avatar']}")) {
                    if (!unlink("student_avatars/{$studentData['Avatar']}")) {
                        $_SESSION['REPORT_MSG'] = array(
                            'code' => 3,
                            'msg' => 'Delete file',
                            'body' => 'Failde to delete file.'
                        );
                        $conn->rollBack();
                        redirectToPage('students.php');
                    }
                }

                $sql = "DELETE FROM usertable WHERE Username = :1";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':1', $_POST['delete-student-btn']);
                if ($stmt->execute()) {
                    $_SESSION['REPORT_MSG'] = array(
                        'code' => 0,
                        'msg' => 'Delete student',
                        'body' => "Successfully removed $studentData[Firstname] $studentData[Lastname]."
                    );
                    $conn->commit();
                } else {
                    $_SESSION['REPORT_MSG'] = array(
                        'code' => 3,
                        'msg' => 'Query error',
                        'body' => 'Failde to delete data.'
                    );
                    $conn->rollBack();
                }
            } else {
                $_SESSION['REPORT_MSG'] = array(
                    'code' => 3,
                    'msg' => 'Query error',
                    'body' => 'Failde to fetch user data.'
                );
                $conn->rollBack();
            }
        } else {
            $_SESSION['REPORT_MSG'] = array(
                'code' => 3,
                'msg' => 'Query error',
                'body' => 'Failde to fetch user data.'
            );
            if ($conn->lastInsertId()) $conn->rollBack();
        }
    } catch (\Throwable $th) {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Server error',
            'body' => $th->getMessage()
        );
        if ($conn->lastInsertId()) $conn->rollBack();
    }
    redirectToPage('students.php');
}
redirectToPage('admin_login.php');