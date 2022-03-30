<?php
require 'connection.php';
require 'utils.php';

unset($_SESSION['admin_login']);

$areSet = isset($_POST['username'], $_POST['password']);

if ($areSet) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $sql = "SELECT username, password FROM admins WHERE username = :1";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':1', $username);
        $stmt->execute();

        $result = $stmt->fetch();

        if ($result) {
            if (password_verify($password, $result['password'])) {
                $_SESSION['ADMIN_LOGIN'] = array(
                    'username' => $username,
                    'password' => $password
                );
                redirectToPage('admin_dashboard.php');
            } else {
                $_SESSION['REPORT_MSG'] = array(
                    'code' => 1,
                    'msg' => 'Admin login',
                    'body' => 'Incorrect password.'
                );
                redirectToPage('admin_login.php');
            }
        } else {
            $_SESSION['REPORT_MSG'] = array(
                'code' => 2,
                'msg' => 'Admin login',
                'body' => 'Username not found.'
            );
            redirectToPage('admin_login.php');
        }
    } catch (\PDOException $e) {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Query error.',
            'body' => $e->getMessage()
        );
        redirectToPage('error_page.php');
    }
}

?>