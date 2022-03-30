<?php
require_once 'connection.php';
require 'utils.php';

$areSet = isset(
    $_POST['submit-student'],
    $_POST['firstname'],
    $_POST['lastname'],
    $_POST['username'],
    $_POST['email'],
    $_POST['password'],
    $_POST['dateofbirth'],
    $_POST['gender'],
    $_POST['address'],
    $_POST['phonenumber'],
    $_FILES['profilepicture']
);

if (!$areSet) redirectToPage('./');

$areEmpty =
    empty($_POST['firstname']) ||
    empty($_POST['lastname']) ||
    empty($_POST['username']) ||
    empty($_POST['email']) ||
    empty($_POST['password']) ||
    empty($_POST['dateofbirth']) ||
    empty($_POST['gender']) ||
    empty($_POST['address']) ||
    empty($_POST['phonenumber']) ||
    empty($_FILES['profilepicture']['name']);

if ($areEmpty) {
    $_SESSION['REPORT_MSG'] = array(
        'code' => 1,
        'msg' => 'Add student',
        'body' => 'Please, fill-up required fields.'
    );

    redirectToPage('./add-student.php');
}

// check if username already exist
try {
    $sql = 'SELECT Username FROM usertable WHERE Username = :1';

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':1', $_POST['username']);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 1,
            'msg' => 'Add student',
            'body' => 'Username already exist.'
        );

        redirectToPage('./add-student.php');
    }
} catch (\Throwable $th) {
    $_SESSION['REPORT_MSG'] = array(
        'code' => 3,
        'msg' => 'Query failed.',
        'body' => $th->getMessage()
    );

    redirectToPage('./add-student.php');
}


// upload image and handle error
$uploadResult = uploadImage($_FILES['profilepicture'], 'student_avatars/');
if (!$uploadResult['success']) {
    $_SESSION['REPORT_MSG'] = array(
        'code' => 1,
        'msg' => 'Upload image',
        'body' => $uploadResult['msg']
    );
    redirectToPage('./add-student.php');
}

try {
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql =
        'INSERT INTO `usertable`(
                `Username`, 
                `Password`, 
                `Firstname`, 
                `Lastname`, 
                `Email`, 
                `DateOfBirth`, 
                `Gender`, 
                `Address`, 
                `PhoneNumber`, 
                `Avatar`
            ) 
        VALUES (
            :1, :2, :3, :4, :5, :6, :7, :8, :9, :10
        )';

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':1', $_POST['username']);
    $stmt->bindParam(':2', $hashed_password);
    $stmt->bindParam(':3', $_POST['firstname']);
    $stmt->bindParam(':4', $_POST['lastname']);
    $stmt->bindParam(':5', $_POST['email']);
    $stmt->bindParam(':6', $_POST['dateofbirth']);
    $stmt->bindParam(':7', $_POST['gender']);
    $stmt->bindParam(':8', $_POST['address']);
    $stmt->bindParam(':9', $_POST['phonenumber']);
    $stmt->bindParam(':10', $_FILES['profilepicture']['name']);
    $stmt->execute();

    redirectToPage('./students.php');
} catch (\Throwable $th) {
    $_SESSION['REPORT_MSG'] = array(
        'code' => 3,
        'msg' => 'Query failed.',
        'body' => $th->getMessage()
    );

    redirectToPage('./add-student.php');
}
