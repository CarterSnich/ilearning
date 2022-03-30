<?php
require_once 'connection.php';
require 'utils.php';

if (!isset($_SESSION['ADMIN_LOGIN'])) redirectToPage('./admin_login.php');

$username = $_SESSION['ADMIN_LOGIN']['username'];

// get counts from required tables
try {
    $sql =
        "SELECT 
            (SELECT COUNT(Username) FROM usertable) AS NumberOfStudents, 
            (
                (SELECT COUNT(Id) FROM css) +
                (SELECT COUNT(Id) FROM english) +
                (SELECT COUNT(Id) FROM filipino) +
                (SELECT COUNT(Id) FROM homeroomguidance) +
                (SELECT COUNT(Id) FROM pe) +
                (SELECT COUNT(Id) FROM philosophy) +
                (SELECT COUNT(Id) FROM practicalresearch)
            ) AS NumberOfActivities,
            (
                (SELECT COUNT(Id) FROM css WHERE Open = 1) +
                (SELECT COUNT(Id) FROM english WHERE Open = 1) +
                (SELECT COUNT(Id) FROM filipino WHERE Open = 1) +
                (SELECT COUNT(Id) FROM homeroomguidance WHERE Open = 1) +
                (SELECT COUNT(Id) FROM pe WHERE Open = 1) +
                (SELECT COUNT(Id) FROM philosophy WHERE Open = 1) +
                (SELECT COUNT(Id) FROM practicalresearch WHERE Open = 1)
            ) AS NumberOfOpenActivities,
            (SELECT COUNT(Id) FROM submissions) AS NumberOfSubmissions";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $dataCounts = $stmt->fetch();
} catch (\Throwable $th) {
    throw $th;
    exit();
}

// fetch pending submissions
try {
    $sql =
        "SELECT 
            s.*, 
            CONCAT(u.Firstname, ' ', u.Lastname) AS StudentName,
            u.Avatar
        FROM 
            submissions AS s
        LEFT JOIN
            usertable AS u ON s.StudentUsername = u.Username 
        WHERE 
            Score IS NULL";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute()) {
        $pendingSubmissions = $stmt->fetchAll();
    } else {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Query failed',
            'body' => 'Failed to fetch pending submissions.'
        );
    }
} catch (\Throwable $th) {
    $_SESSION['REPORT_MSG'] = array(
        'code' => 3,
        'msg' => 'Connection error',
        'body' => $th->getMessage()
    );
}


$activityNames = array();
foreach (array_keys($subjects) as $subj) {
    try {
        $sql = "SELECT Id, Activity_Title FROM $subj";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute()) {
            $result = $stmt->fetchAll();
            foreach ($result as $row) {
                $activityNames[$subj][$row['Id']] = $row['Activity_Title'];
            }
        } else {
            $_SESSION['REPORT_MSG'] = array(
                'code' => 3,
                'msg' => 'Query failed',
                'body' => 'Failed to fetch activity titles.'
            );
        }
    } catch (\Throwable $th) {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Connection error',
            'body' => $th->getMessage()
        );
    }
}

// get students with messages
try {
    $sql =
        "SELECT 
            DISTINCT c1.Sender,
            c1.Recipient, 
            (
                SELECT 
                    CONCAT(u.Firstname, ' ', u.Lastname) 
                FROM 
                    usertable AS u 
                WHERE 
                    u.Username = c1.Recipient
            ) AS StudentName,
            (
                SELECT 
                    u.Avatar
                FROM 
                    usertable AS u 
                WHERE 
                    u.Username = c1.Recipient
            ) AS StudentAvatar,
            (
                SELECT 
                    COUNT(c2.Id) 
                FROM 
                    chats AS c2 
                WHERE 
                    c2.Recipient = :1 
                    AND
                    c2.Seen = 0
            ) as UnreadMessageCount
        FROM 
            chats AS c1
        WHERE 
            c1.Recipient != :1
        ORDER BY
            c1.DateSent DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':1', $username);

    if ($stmt->execute()) {
        $studentMessages = $stmt->fetchAll();
    } else {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Query failed.',
            'body' => 'Failed to retrieve messages.'
        );
    }
} catch (\Throwable $th) {
    $_SESSION['REPORT_MSG'] = array(
        'code' => 3,
        'msg' => 'Server error.',
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
    <title>Administrator | Dashboard</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
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
                        <span class="user-img">
                            <img class="rounded-circle" src="assets/img/user.jpg" width="24" alt="Admin">
                            <span class="status online"></span>
                        </span>
                        <span><?= $username ?></span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="change_admin_password.php">Settings</a>
                        <a class="dropdown-item" href="admin_login.php">Logout</a>
                    </div>
                </li>
            </ul>
            <div class="dropdown mobile-user-menu float-right">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
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
                        <li class="active">
                            <a href="javascript:void(0);"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
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
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
                            <span class="dash-widget-bg2"><i class="fa fa-user-o"></i></span>
                            <div class="dash-widget-info text-right">
                                <h3><?= $dataCounts['NumberOfStudents'] ?></h3>
                                <span class="widget-title2">Students<i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
                            <span class="dash-widget-bg1"><i class="fa fa-book" aria-hidden="true"></i></span>
                            <div class="dash-widget-info text-right">
                                <h3><?= $dataCounts['NumberOfActivities'] ?></h3>
                                <span class="widget-title1">Activities <i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
                            <span class="dash-widget-bg3"><i class="fa fa-bookmark" aria-hidden="true"></i></span>
                            <div class="dash-widget-info text-right">
                                <h3><?= $dataCounts['NumberOfOpenActivities'] ?></h3>
                                <span class="widget-title3">Open activities <i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
                            <span class="dash-widget-bg4"><i class="fa fa-reply" aria-hidden="true"></i></span>
                            <div class="dash-widget-info text-right">
                                <h3><?= $dataCounts['NumberOfSubmissions'] ?></h3>
                                <span class="widget-title4">Submissions <i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-8 col-xl-8">
                        <div class="card member-panel">
                            <div class="card-header bg-white">
                                <h4 class="card-title mb-0">Pending submissions</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead class="d-none">
                                            <tr>
                                                <th>Teacher's Name</th>
                                                <th>Subject's Name</th>
                                                <th>Time</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pendingSubmissions as $pending) : ?>
                                                <tr>
                                                    <td style="min-width: 200px;">
                                                        <div class="avatar">
                                                            <img src="student_avatars/<?= $pending['Avatar'] ?>" alt="">
                                                        </div>
                                                        <h2>
                                                            <?= $pending['StudentName'] ?>
                                                            <span>Student name</span>
                                                        </h2>
                                                    </td>
                                                    <td>
                                                        <h5 class="time-title p-0"><?= $activityNames[$pending['Subject']][$pending['ActivityId']] ?></h5>
                                                        <span style="color: #9e9e9e; display: block;font-size: 12px; margin-top: 3px;"><?= $subjects[$pending['Subject']] ?></span>
                                                    </td>
                                                    <td>
                                                        <h5 class="time-title p-0"><?= formatDate($pending['DateSubmitted'], 'F j, Y') ?></h5>
                                                        <span style="color: #9e9e9e; display: block;font-size: 12px; margin-top: 3px;">Date submitted</span>
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-outline-primary take-btn my-auto" href="view-activity.php?subj=<?= $pending['Subject'] ?>&id=<?= $pending['ActivityId'] ?>&show=<?= $pending['StudentUsername'] ?>">View</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                        <div class="card member-panel">
                            <div class="card-header bg-white">
                                <h4 class="card-title mb-0">Chats</h4>
                            </div>
                            <div class="card-body">
                                <ul class="contact-list">
                                    <?php foreach ($studentMessages as $message) : ?>
                                        <li>
                                            <a href="chat.php?view=<?= $message['Recipient'] ?>" class="contact-cont">
                                                <div class="float-left user-img m-r-10">
                                                    <img src="student_avatars/<?= $message['StudentAvatar'] ?>" alt="" class="w-40 rounded-circle" width="40" height="40">
                                                </div>
                                                <div class="contact-info">
                                                    <span class="contact-name text-ellipsis my-2"><?= $message['StudentName'] ?></span>
                                                </div>
                                            </a>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                            <div class="card-footer text-center bg-white">
                                <a href="chat.php" class="text-muted">View all conversations</a>
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
    <script src="assets/js/Chart.bundle.js"></script>
    <script src="assets/js/chart.js"></script>
    <script src="assets/js/app.js"></script>

</body>



</html>