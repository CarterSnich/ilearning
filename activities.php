<?php
require_once 'connection.php';
require 'utils.php';

if (!isset($_SESSION['ADMIN_LOGIN'])) redirectToPage('./');
if (!(isset($_GET['subj']) && array_key_exists($_GET['subj'], $subjects))) redirectToPage('./admin_dashboard.php');

$username = $_SESSION['ADMIN_LOGIN']['username'];

// get activities
try {
    $sql = "SELECT * FROM $_GET[subj]";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $activities = $stmt->fetchAll();
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
                    <div class="col-md-12">
                        <?php if (isset($_SESSION['REPORT_MSG'])) : ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= $_SESSION['REPORT_MSG']['body'] ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title"><?= $subjects[$_GET['subj']] ?></h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="add-activity.php?subj=<?= $_GET['subj'] ?>" class="btn btn btn-primary btn-rounded float-right">
                            <i class="fa fa-plus"></i> Add activity
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Activity Title</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($activities as $activity) : ?>
                                        <tr>
                                            <td><a href="view-activity.php?subj=<?= $_GET['subj'] ?>&id=<?= $activity['Id'] ?>">#<?= $activity['Id'] ?></a></td>
                                            <td><a href="view-activity.php?subj=<?= $_GET['subj'] ?>&id=<?= $activity['Id'] ?>"><?= $activity['Activity_Title'] ?></a></td>
                                            <td><a href="view-activity.php?subj=<?= $_GET['subj'] ?>&id=<?= $activity['Id'] ?>"><?= formatDate($activity['Deadline'], 'F j, Y') ?></a></td>
                                            <td><a href="view-activity.php?subj=<?= $_GET['subj'] ?>&id=<?= $activity['Id'] ?>"><?= $activity['Open'] ? 'Open' : 'Closed' ?></a></td>
                                            <td class="text-right">
                                                <div class="dropdown dropdown-action">
                                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="edit-activity.php?subj=<?= $_GET['subj'] ?>&id=<?= $activity['Id'] ?>"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete_appointment"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="delete_appointment" class="modal fade delete-modal" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <img src="assets/img/sent.png" alt="" width="50" height="46">
                            <h3>Are you sure want to delete this Appointment?</h3>
                            <div class="m-t-20"> <a href="#" class="btn btn-white" data-dismiss="modal">Close</a>
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </div>
                        </div>
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
    <script src="assets/js/app.js"></script>
    <script>
        $(function() {
        });
    </script>
</body>


</html>

<?php unset($_SESSION['REPORT_MSG']);
