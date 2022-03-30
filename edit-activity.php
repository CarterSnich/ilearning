<?php
require_once 'connection.php';
require 'utils.php';

if (!isset($_SESSION['ADMIN_LOGIN'])) redirectToPage('./');
if (!(isset($_GET['subj'], $_GET['id']) && array_key_exists($_GET['subj'], $subjects) && strlen($_GET['id']))) redirectToPage('./admin_dashboard.php');

$username = $_SESSION['ADMIN_LOGIN']['username'];

if (isset($_POST['update-activity-button'])) {
    try {
        if (empty($_FILES['modulefile']['name'])) {
            $sql =
                "UPDATE 
                    $_GET[subj]
                SET
                    Activity_Title = :1,
                    Instructions = :2,
                    Deadline = :3,
                    Open = :4
                WHERE 
                    Id = :5";

            $instructionsEncoded = json_encode($_POST['instructions']);

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':1', $_POST['activitytitle']);
            $stmt->bindParam(':2', $instructionsEncoded);
            $stmt->bindParam(':3', $_POST['deadline']);
            $stmt->bindParam(':4', $_POST['status']);
            $stmt->bindParam(':5', $_POST['update-activity-button']);

            if ($stmt->execute()) {
                $_SESSION['REPORT_MSG'] = array(
                    'code' => 0,
                    'msg' => 'Update activity',
                    'body' => 'Activity updated successfully.'
                );
                redirectToPage("view-activity.php?subj={$_GET['subj']}&id={$_GET['id']}");
            } else {
                $_SESSION['REPORT_MSG'] = array(
                    'code' => 2,
                    'msg' => 'Update activity',
                    'body' => 'Failed to update activity.'
                );
            }
        } else {
            $conn->beginTransaction();

            $sql =
                "UPDATE 
                    $_GET[subj]
                SET
                    Activity_Title = :1,
                    Instructions = :2,
                    ModuleFile = :3,
                    Deadline = :4,
                    Open = :5
                WHERE 
                    Id = :6";

            $instructionsEncoded = json_encode($_POST['instructions']);

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':1', $_POST['activitytitle']);
            $stmt->bindParam(':2', $instructionsEncoded);
            $stmt->bindParam(':3', $_FILES['modulefile']['name']);
            $stmt->bindParam(':4', $_POST['deadline']);
            $stmt->bindParam(':5', $_POST['status']);
            $stmt->bindParam(':6', $_POST['update-activity-button']);

            if ($stmt->execute()) {
                $uploadResult = uploadModule($_FILES['modulefile']);
                
                if ($uploadResult['success']) {
                    $conn->commit();
                    $_SESSION['REPORT_MSG'] = array(
                        'code' => 0,
                        'msg' => 'Update activity',
                        'body' => 'Activity updated successfully.'
                    );
                    redirectToPage("view-activity.php?subj={$_GET['subj']}&id={$_GET['id']}");
                } else {
                    $conn->rollBack();
                    $_SESSION['REPORT_MSG'] = array(
                        'code' => 3,
                        'msg' => 'File upload failde',
                        'body' =>  $uploadResult['msg']
                    );
                }
            } else {
                $conn->rollBack();
                $_SESSION['REPORT_MSG'] = array(
                    'code' => 3,
                    'msg' => 'File upload failde',
                    'body' =>  $uploadResult['msg']
                );
            }
        }
    } catch (\Throwable $th) {
        if ($conn->inTransaction()) $conn->rollBack();
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Query failed.',
            'body' => $th->getMessage()
        );
    }
}

try {
    $sql = "SELECT * FROM $_GET[subj] WHERE Id = :1";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':1', $_GET['id']);

    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            $activity = $stmt->fetch();
        } else {
            redirectToPage('admin_dashboard.php');
        }
    } else {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 1,
            'msg' => 'Query failed.',
            'body' => $th->getMessage()
        );
    }
} catch (\Throwable $th) {
    $_SESSION['REPORT_MSG'] = array(
        'code' => 1,
        'msg' => 'Query failed.',
        'body' => $th->getMessage()
    );
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

    <!-- quill.js -->
    <link rel="stylesheet" href="./assets/quill/quill.snow.css">

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
                        <li>
                            <a href="students.php"><i class="fa fa-wheelchair"></i> <span>Students</span></a>
                        </li>
                        <li class="submenu">
                            <a href="#" class="subdrop"><i class="fa fa-list-ul"></i> <span> Activities </span> <span class="menu-arrow"></span></a>
                            <ul style="display: block;">
                                <li class="<?= $_GET['subj'] == 'philosophy' ? 'active' : '' ?>"><a href="activities.php?subj=philosophy">Philosohpy</a></li>
                                <li class="<?= $_GET['subj'] == 'english' ? 'active' : '' ?>"><a href="activities.php?subj=english">English</a></li>
                                <li class="<?= $_GET['subj'] == 'pe' ? 'active' : '' ?>"><a href="activities.php?subj=pe">Physical Education</a></li>
                                <li class="<?= $_GET['subj'] == 'filipino' ? 'active' : '' ?>"><a href="activities.php?subj=filipino">Filipino</a></li>
                                <li class="<?= $_GET['subj'] == 'practicalresearch' ? 'active' : '' ?>"><a href="activities.php?subj=practicalresearch">Practical Research 2</a></li>
                                <li class="<?= $_GET['subj'] == 'homeroomguidance' ? 'active' : '' ?>"><a href="activities.php?subj=homeroomguidance">Homeroom Guidance</a></li>
                                <li class="<?= $_GET['subj'] == 'css' ? 'active' : '' ?>"><a href="activities.php?subj=css">Computer System Servicing</a></li>
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
                        <h4 class="page-title">Edit activity</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Subject <span class="text-danger">*</span></label>
                                        <select class="select" name="subject" required disabled>
                                            <option value="">Select subject</option>
                                            <?php foreach ($subjects as $key => $subject) : ?>
                                                <option value="<?= $key ?>" <?= $_GET['subj'] == $key ? ' selected' : '' ?>><?= $subject ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Activity title <span class="text-danger">*</span></label>
                                        <input class="form-control" name="activitytitle" type="text" value="<?= isset($_POST['activitytitle']) ? $_POST['activitytitle'] : $activity['Activity_Title'] ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Instructions/Directions</label>
                                <div id="snow-container"></div>
                                <textarea cols="30" rows="4" class="form-control d-none" id="dummy-delta-container" name="instructions"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Module</label>
                                        <div class="custom-file" id="upload-module-file">
                                            <input type="file" class="custom-file-input" id="customFile" name="modulefile">
                                            <label class="custom-file-label rounded-0" for="customFile"><?= $activity['ModuleFile'] ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Deadline <span class="text-danger">*</span></label>
                                        <div class="cal-icon">
                                            <input type="text" class="form-control datetimepicker" name="deadline" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="display-block">Status <span class="text-danger">*</span></label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="product_active" value="1" checked required>
                                            <label class="form-check-label" for="product_active">Open</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="product_inactive" value="0" <?= !$activity['Open'] ? 'checked' : '' ?> required>
                                            <label class="form-check-label" for="product_inactive">Closed</label>
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
                                <button class="btn btn-primary submit-btn" type="submit" value="<?= $activity['Id'] ?>" name="update-activity-button">Update activity</button>
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

    <!-- quill.js -->
    <script src="./assets/quill/quill.min.js"></script>

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

                            $('select').each(function(index, element) {
                                if (element.value == '') {
                                    $(element).next('span').find('.select2-selection__rendered').css({
                                        'border': '1px solid #dc3545',
                                        'background-image': `url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23dc3545' viewBox='-2 -2 7 7'%3e%3cpath stroke='%23d9534f' d='M0 0l3 3m0-3L0 3'/%3e%3ccircle r='.5'/%3e%3ccircle cx='3' r='.5'/%3e%3ccircle cy='3' r='.5'/%3e%3ccircle cx='3' cy='3' r='.5'/%3e%3c/svg%3E")`,
                                        'background-repeat': 'no-repeat',
                                        'background-position': 'center right calc(2.25rem / 4)',
                                        'background-size': 'calc(2.25rem / 2) calc(2.25rem / 2)'
                                    })
                                } else {
                                    $(element).next('span').find('.select2-selection__rendered').css({
                                        'border': '1px solid #28a745',
                                        'background-image': `url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e")`,
                                        'background-repeat': 'no-repeat',
                                        'background-position': 'center right calc(2.25rem / 4)',
                                        'background-size': 'calc(2.25rem / 2) calc(2.25rem / 2)'
                                    })
                                }
                            });

                            $('#invalid-feedbacks').append(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    Fill up required fields.
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
            $('select').each(function(index, element) {
                $(element).on('change', function() {
                    if (element.form.classList.contains('was-validated')) {
                        if (element.value == '') {
                            $(element).next('span').find('.select2-selection__rendered').css({
                                'border': '1px solid #dc3545',
                                'background-image': `url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23dc3545' viewBox='-2 -2 7 7'%3e%3cpath stroke='%23d9534f' d='M0 0l3 3m0-3L0 3'/%3e%3ccircle r='.5'/%3e%3ccircle cx='3' r='.5'/%3e%3ccircle cy='3' r='.5'/%3e%3ccircle cx='3' cy='3' r='.5'/%3e%3c/svg%3E")`,
                                'background-repeat': 'no-repeat',
                                'background-position': 'center right calc(2.25rem / 4)',
                                'background-size': 'calc(2.25rem / 2) calc(2.25rem / 2)'
                            })
                        } else {
                            $(element).next('span').find('.select2-selection__rendered').css({
                                'border': '1px solid #28a745',
                                'background-image': `url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e")`,
                                'background-repeat': 'no-repeat',
                                'background-position': 'center right calc(2.25rem / 4)',
                                'background-size': 'calc(2.25rem / 2) calc(2.25rem / 2)'
                            })
                        }
                    }
                })
            });

            $('.datetimepicker').data("DateTimePicker").date(new Date('<?= isset($_POST['deadline']) ? $_POST['deadline'] : $activity['Deadline'] ?>'));

            var quill = new Quill('#snow-container', {
                placeholder: 'Type something...',
                theme: 'snow'
            });

            quill.on('text-change', function(delta, oldDelta, source) {
                $('#dummy-delta-container').text(JSON.stringify(quill.getContents()));
            });

            quill.setContents(JSON.parse(<?= isset($_POST['instructions']) ? $_POST['instructions'] : $activity['Instructions'] ?>));

            $('#upload-module-file').on('change', 'input[type=file]', function() {
                let _this = this;
                console.dir(this.files);
                if (this.files.length > 0 && this.files[0].name.length > 0 && this.files[0].type === 'application/pdf') {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#upload-module-file>label').text(_this.files[0].name);
                    }
                    reader.readAsDataURL(this.files[0]);
                } else {
                    $('#upload-module-file>label').text('');
                    alert('Please select a PDF file.');
                }
            })

        });
    </script>
</body>

</html>
<?php

unset($_SESSION['REPORT_MSG']);
