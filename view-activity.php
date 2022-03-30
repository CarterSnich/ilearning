<?php
require_once 'connection.php';
require 'utils.php';

if (!isset($_SESSION['ADMIN_LOGIN'])) redirectToPage('./');
if (!(isset($_GET['subj']) && array_key_exists($_GET['subj'], $subjects))) redirectToPage('./admin_dashboard.php');
$username = $_SESSION['ADMIN_LOGIN']['username'];

// get activities
try {
    $sql = "SELECT * FROM $_GET[subj] WHERE Id = :1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':1', $_GET['id']);
    $stmt->execute();
    $activity = $stmt->fetch();
} catch (\Throwable $th) {
    $_SESSION['REPORT_MSG'] = array(
        'code' => 3,
        'msg' => 'Query failed.',
        'body' => $th->getMessage()
    );
}

// get submissions
try {
    $sql =
        "SELECT 
            submissions.*, 
            usertable.Username, 
            CONCAT(Firstname, ' ', Lastname) AS StudentName,
            Avatar
        FROM 
            `submissions`
        JOIN 
            usertable ON StudentUsername = Username
        WHERE 
            submissions.Subject = :1 AND submissions.ActivityId = :2";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':1', $_GET['subj']);
    $stmt->bindParam(':2', $_GET['id']);

    if ($stmt->execute()) {
        $submissions = $stmt->fetchAll();
    } else {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Failed query',
            'body' => 'Failed to get submissions for this activity.'
        );
    }
} catch (\Throwable $th) {
    $_SESSION['REPORT_MSG'] = array(
        'code' => 3,
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
    <!--[if lt IE 9]>
    <script src="assets/js/html5shiv.min.js"></script>
    < src="assets/js/respond.min.js"></script>
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
                        <div id="invalid-feedbacks">
                            <?php if (isset($_SESSION['REPORT_MSG'])) : ?>
                                <div class="alert alert-<?= $_SESSION['REPORT_MSG']['code'] == 0 ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                                    <?= $_SESSION['REPORT_MSG']['body'] ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="col-lg-8 offset-lg-2">
                        <div class="d-flex justify-content-between">
                            <h4 class="page-title mb-0"><?= $activity['Activity_Title'] ?></h4>
                            <div class="dropdown dropdown-action">
                                <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="edit-activity.php?subj=<?= $_GET['subj'] ?>&id=<?= $activity['Id'] ?>"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete_activity"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <p class="m-0"> <?= $activity['MaxScore'] ?> points &bullet; <?= $subjects[$_GET['subj']] ?></p>
                            <p class="m-0">Deadline: <?= formatDate($activity['Deadline'], 'F j, Y') ?> &bullet; <?= $activity['Open'] ? 'Open' : 'Closed' ?></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="instructions" class="border p-3 mb-3"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="list-group">
                                    <?php if ($activity['ModuleFile']) : ?>
                                        <div class="list-group-item d-flex justify-content-between">
                                            <p class="p-0 my-auto"><?= $activity['ModuleFile'] ?></p>
                                            <button class="btn btn-primary" data-toggle="modal" data-target="#modal-pdf-viewer">View</button>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="card mt-3">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block">Submissions</h4>
                            </div>
                            <div class="card-block">

                                <?php if (count($submissions) > 0) : ?>
                                    <div class="table-responsive">
                                        <table class="table mb-0 new-patient-table">
                                            <tbody>
                                                <?php foreach ($submissions as $submission) : ?>
                                                    <tr>
                                                        <td>
                                                            <img width="28" height="28" class="rounded-circle" src="student_avatars/<?= $submission['Avatar'] ?>" alt="">
                                                            <h2 class="ml-3"><?= $submission['StudentName'] ?></h2>
                                                        </td>
                                                        <td><?= formatDate($submission['DateSubmitted'], 'F j, Y') ?></td>
                                                        <td><button class="btn btn-primary btn-primary-two float-right" data-toggle="modal" data-target="#answer-<?= $submission['StudentUsername'] ?>">View</button></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else : ?>
                                    <div class="d-flex">
                                        <p class="mx-auto">No submissions yet.</p>
                                    </div>
                                <?php endif ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>

    <form id="delete-activity" method="POST" action="./query_delete_activity.php"></form>

    <!-- delete modal -->
    <div id="delete_activity" class="modal fade delete-modal" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="assets/img/sent.png" alt="" width="50" height="46">
                    <h3>Delete this <?= $activity['Activity_Title'] ?>?</h3>
                    <div class="m-t-20"> <a href="#" class="btn btn-white" data-dismiss="modal">Close</a>
                        <button type="submit" class="btn btn-danger" value="<?= $activity['Id'] ?>" name="delete-activity" form="delete-activity">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- modal pdf viewer -->
    <div class="modal fade" id="modal-pdf-viewer" tabindex="-1" aria-labelledby="modal-pdf-viewer" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div style="height: 88vh;">
                    <iframe src="./view-module-pdf.php?module_pdf=<?= $activity['ModuleFile'] ?>" frameborder="0" width="100%" height="100%"></iframe>
                </div>
            </div>
        </div>
    </div>


    <?php foreach ($submissions as $submission) : ?>
        <div class="modal fade" id="answer-<?= $submission['StudentUsername'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="w-100 row">
                        <div class="col-10 pr-0">
                            <div class="d-flex" style="height: 88vh;">
                                <iframe class="m-auto" src="./view-answer-pdf.php?answerfile=<?= $submission['UploadedFile'] ?>" frameborder="0" width="96%" height="96%"></iframe>
                            </div>
                        </div>
                        <div class="col-2 my-3">
                            <form method="POST" action="query_update_score.php?subject=<?= $_GET['subj'] ?>&id=<?= $_GET['id'] ?>">
                                <div class="form-group">
                                    <label>Submitted by: </label>
                                    <p class="m-0 w-100 text-right"><?= $submission['StudentName'] ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Date submitted: </label>
                                    <p class="m-0 w-100 text-right"><?= formatDate($submission['DateSubmitted'], 'F j, Y') ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Score:</label>
                                    <div class="d-flex">
                                        <input type="text" class="d-none" value="<?= $submission['Username'] ?>" name="studentusername" disabled readonly>
                                        <input type="number" class="form-control" value="<?= $submission['Score'] ?>" name="score" required>
                                        <p class="ml-1 my-auto">/<?= $activity['MaxScore'] ?></p>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block" name="save-score-btn">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach ?>

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

    <div id="dummy-editor" class="d-none"></div>
    <script>
        $(function() {
            var quill = new Quill('#dummy-editor');
            quill.setContents(JSON.parse(<?= $activity['Instructions'] ?>));
            $('#instructions').html(quill.root.innerHTML);

            var url = new URL(window.location.href);
            var show = url.searchParams.get("show");
            if (show != null) {
                $(`#answer-${show}`).modal('show');
            }
        });
    </script>
</body>

</html>

<?php
unset($_SESSION['REPORT_MSG']);
?>