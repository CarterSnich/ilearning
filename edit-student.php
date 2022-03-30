<?php
require_once 'connection.php';
require 'utils.php';

if (!isset($_SESSION['ADMIN_LOGIN'])) redirectToPage('./');

$username = $_SESSION['ADMIN_LOGIN']['username'];

if (!isset($_GET['student']) || empty($_GET['student'])) redirectToPage('./add-student.php');

// get students
try {
    $sql = 'SELECT * FROM usertable WHERE Username = :1';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':1', $_GET['student']);
    $stmt->execute();
    $student = $stmt->fetch();
} catch (\PDOException $th) {
    throw $th;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Preclinic - Medical & Hospital - Bootstrap 4 Admin Template</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-datetimepicker.min.css">
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
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="change_admin_password.php">Settings</a>
                    <a class="dropdown-item" href="admin_login.php">Logout</a>
                </div>
            </div>
        </div>
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">Main</li>
                        <li>
                            <a href="admin_dashboard.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
                        </li>
                        <li class="active">
                            <a href="students.php"><i class="fa fa-wheelchair"></i> <span>Students</span></a>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fa fa-list-ul"></i> <span> Activities </span> <span class="menu-arrow"></span></a>
                            <ul style="display: none;">
                                <li><a href="activities.php?subj=philosophy">Philosohpy</a></li>
                                <li><a href="activities.php?subj=english">English</a></li>
                                <li><a href="activities.php?subj=pe">Physical Education</a></li>
                                <li><a href="activities.php?subj=filipino">Filipino</a></li>
                                <li><a href="activities.php?subj=practicalresearch">Practical Research 2</a></li>
                                <li><a href="activities.php?subj=homeroomguidance">Homeroom Guidance</a></li>
                                <li><a href="activities.php?subj=css">Computer System Servicing</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="files.php"><i class="fa fa-folder"></i> <span>Files</span></a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <h4 class="page-title">Edit student</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form class="needs-validation" method="post" action="query_update_student.php?student=<?= $_GET['student'] ?>" novalidate>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>First Name <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="firstname" value="<?= $student['Firstname'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Last Name <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="lastname" value="<?= $student['Lastname'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input class="form-control" type="email" name="email" value="<?= $student['Email'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Date of Birth <span class="text-danger">*</span></label>
                                        <div class="cal-icon">
                                            <input type="text" name="dateofbirth" class="form-control datetimepicker" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group gender-select">
                                        <label class="gen-label">Gender: <span class="text-danger">*</span></label>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="gender" class="form-check-input" required <?= $student['Gender'] == 0 ? 'checked' : '' ?>>Male
                                            </label>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="gender" class="form-check-input" required <?= $student['Gender'] == 1 ? 'checked' : '' ?>>Female
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Address <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="address" value="<?= $student['Address'] ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Phone <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="phonenumber" value="<?= $student['PhoneNumber'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Avatar</label>
                                        <div class="profile-upload">
                                            <div class="upload-img">
                                                <img alt="" src="student_avatars/<?= $student['Avatar'] ?: 'user.jpg' ?>">
                                            </div>
                                            <div class="upload-input">
                                                <input type="file" class="form-control" name="profilepicture">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="invalid-feedbacks">
                                <?php if (isset($_SESSION['REPORT_MSG'])) : ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?= $_SESSION['REPORT_MSG']['body'] ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                <?php endif ?>
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" name="update-student">Save</button>
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
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        $('#invalid-feedbacks').html('');

                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                            $('#invalid-feedbacks').append(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    Fill up required fields.
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `);
                        }

                        if ($('#password-field').val() != $('#confirm-password-field').val()) {
                            $('#invalid-feedbacks').append(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    Password does not match.
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `);
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        $(document).ready(function() {
            $('.datetimepicker').datetimepicker().data("DateTimePicker").date(new Date("<?= $student['DateOfBirth'] ?>"));
        });
    </script>

</body>


<!-- edit-patient24:07-->

</html>

<?php

unset($_SESSION['REPORT_MSG']);
