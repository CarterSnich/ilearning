<?php
require_once 'connection.php';
require 'utils.php';

if (!isset($_SESSION['ADMIN_LOGIN'])) redirectToPage('./admin_login.php');
$username = $_SESSION['ADMIN_LOGIN']['username'];


if (isset($_POST['updatePasswordBtn'])) {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];


    if ($newPassword === $confirmPassword) {
        try {
            $sql = "SELECT * FROM admins WHERE username = :1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':1', $username);

            if ($stmt->execute() && $stmt->rowCount()) {
                $fetch = $stmt->fetch();

                if (password_verify($oldPassword, $fetch['password'])) {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    $sql = "UPDATE admins SET password = :1 WHERE username = :2";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':1', $hashedPassword);
                    $stmt->bindParam(':2', $username);

                    if ($stmt->execute()) {
                        $_SESSION['REPORT_MSG'] = array(
                            'code' => 0,
                            'msg' => 'Update password',
                            'body' => 'Password updated successfully.'
                        );
                        redirectToPage('admin_login.php');
                    } else {
                        $_SESSION['REPORT_MSG'] = array(
                            'code' => 2,
                            'msg' => 'Query failed',
                            'body' => 'Failed to update password.'
                        );
                    }
                } else {
                    $_SESSION['REPORT_MSG'] = array(
                        'code' => 1,
                        'msg' => 'Update password',
                        'body' => 'Incorrect password.'
                    );
                }
            } else {
                $_SESSION['REPORT_MSG'] = array(
                    'code' => 2,
                    'msg' => 'Query failed',
                    'body' => 'Failed to fetch user data.'
                );
            }
        } catch (\Throwable $th) {
            $_SESSION['REPORT_MSG'] = array(
                'code' => 3,
                'msg' => 'Server error',
                'body' => $th->getMessage()
            );
        }
    } else {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 1,
            'msg' => 'Change password',
            'body' => 'Password does not match.'
        );
    }
}
?>

<!DOCTYPE html>
<html lang="en">


<!-- change-password24:06-->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Preclinic - Medical & Hospital - Bootstrap 4 Admin Template</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!--[if lt IE 9]>
		<script src="assets/js/html5shiv.min.js"></script>
		<script src="assets/js/respond.min.js"></script>
	<![endif]-->
</head>

<body>
    <div class="main-wrapper">
        <div class="header">
            <div class="header-left">
                <a href="admin_dashboard.php" class="logo">
                    <img src="assets/img/logo.png" width="35" height="35" alt=""> <span>EVSU Dulag</span>
                </a>
            </div>
            <a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
            <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>
            <ul class="nav user-menu float-right">
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                        <span class="user-img"><img class="rounded-circle" src="assets/img/user.jpg" width="40" alt="Admin">
                            <span class="status online"></span></span>
                        <span><?= $username ?></span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="change_admin_password.php">Settings</a>
                        <a class="dropdown-item" href="admin_login.php">Logout</a>
                    </div>
                </li>
            </ul>
            <div class="dropdown mobile-user-menu float-right">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="change_admin_password.php">Settings</a>
                    <a class="dropdown-item" href="admin_login.php">Logout</a>
                </div>
            </div>
        </div>
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div class="sidebar-menu">
                    <ul>
                        <li class="active">
                            <a href="change_admin_password.php"><i class="fa fa-lock"></i> <span>Change Password</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <h4 class="page-title">Change Password</h4>
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Old password</label>
                                        <input type="password" class="form-control" name="oldPassword" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>New password</label>
                                        <input type="password" class="form-control" name="newPassword" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Confirm password</label>
                                        <input type="password" class="form-control" name="confirmPassword" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <?php if (isset($_SESSION['REPORT_MSG'])) : ?>
                                        <div class="alert alert-<?= $errorCSS[$_SESSION['REPORT_MSG']['code']] ?> alert-dismissible fade show" role="alert">
                                            <?= $_SESSION['REPORT_MSG']['body'] ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 text-center m-t-20">
                                    <button type="submit" name="updatePasswordBtn" class="btn btn-primary submit-btn">Update Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/app.js"></script>

    <script>
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>

</body>


<!-- change-password24:06-->

</html>

<?php
unset($_SESSION['REPORT_MSG']);
?>