<?php
require_once 'connection.php';
require 'utils.php';

if (!isset($_SESSION['ADMIN_LOGIN'])) redirectToPage('./');

$username = $_SESSION['ADMIN_LOGIN']['username'];


if (isset($_POST['delete-file'])) {
    try {
        $file = './activities/' . $_POST['delete-file'];

        if (file_exists($file)) {
            if (!unlink($file)) {
                $_SESSION['REPORT_MSG'] = array(
                    'code' => 1,
                    'msg' => 'Delete file',
                    'body' => 'Failed to delete file.'
                );
            }
            $_SESSION['REPORT_MSG'] = array(
                'code' => 0,
                'msg' => 'Delete file',
                'body' => $_POST['delete-file'] . ' deleted successfully!'
            );
        } else {
            $_SESSION['REPORT_MSG'] = array(
                'code' => 0,
                'msg' => 'Delete file',
                'body' => 'File does not exist.'
            );
        }
    } catch (\Throwable $th) {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 2,
            'msg' => 'Delete file',
            'body' => $th->getMessage()
        );
    }

    redirectToPage('./files.php');
}

$files = [];

foreach (scandir('./activities') as $file) {
    if (!is_dir($file)) {
        $files[] = array(
            'name' => $file,
            'date_modified' => date("d/m/Y G:i A", filemtime('./activities/' . $file)),
            'ext' => pathinfo('./activities/' . $file)['extension'],
            'size' => human_filesize(filesize('./activities/' . $file))
        );
    }
}

?>
<!DOCTYPE html>
<html lang="en">


<!-- schedule23:20-->

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
                        <li>
                            <a href="students.php"><i class="fa fa-user"></i> <span>Students</span></a>
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
                        <li class="active">
                            <a href="files.php"><i class="fa fa-folder"></i> <span>Files</span></a>
                        </li>
                        
                    </ul>
                </div>
            </div>
        </div>
        <div class="page-wrapper">
            <div class="content">

                <!-- alert -->
                <?php if (isset($_SESSION['REPORT_MSG'])) : ?>
                    <?php
                    $report = $_SESSION['REPORT_MSG'];
                    switch ($report['code']) {
                        case 0:
                            $type = 'success';
                            break;
                        case 1:
                            $type = 'warning';
                            break;
                        default:
                            $type = 'danger';
                            break;
                    }

                    ?>
                    <div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert">
                        <?= $report['body'] ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif ?>


                <div class="row">
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title">Files</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <!-- <a href="add-schedule.html" class="btn btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Add file</a> -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-border table-striped custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>File name</th>
                                        <th>Date modified</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($files as $index => $file) : ?>
                                        <tr>
                                            <td><a href="#" data-toggle="modal" data-target="#pdf-view-<?= $index ?>"><?= $file['name'] ?></a></td>
                                            <td><a href="#" data-toggle="modal" data-target="#pdf-view-<?= $index ?>"><?= $file['date_modified'] ?></a></td>
                                            <td><a href="#" data-toggle="modal" data-target="#pdf-view-<?= $index ?>"><?= strtoupper($file['ext']) ?></a></td>
                                            <td><a href="#" data-toggle="modal" data-target="#pdf-view-<?= $index ?>"><?= $file['size'] ?></a></td>
                                            <td class="text-right">
                                                <a class="btn btn-danger" href="#" data-toggle="modal" data-target="#delete_file_<?= $index ?>"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php foreach ($files as $index => $file) : ?>
        <div id="delete_file_<?= $index ?>" class="modal fade delete-modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <img src="assets/img/sent.png" alt="" width="50" height="46">
                        <h3>Delete <?= $file['name'] ?>?</h3>
                        <div class="m-t-20"> <a href="#" class="btn btn-white" data-dismiss="modal">Close</a>
                            <button type="submit" class="btn btn-danger" value="<?= $file['name'] ?>" name="delete-file" form="delete-file-form">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="pdf-view-<?= $index ?>" tabindex="-1" aria-labelledby="modal-pdf-viewer" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div style="height: 88vh;">
                        <iframe src="./view-module-pdf.php?module_pdf=<?= $file['name'] ?>" frameborder="0" width="100%" height="100%"></iframe>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach ?>

    <form action="./files.php" method="POST" id="delete-file-form"></form>


    <div class="sidebar-overlay" data-reff=""></div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>


<!-- schedule23:21-->

</html>

<?php unset($_SESSION['REPORT_MSG']);
