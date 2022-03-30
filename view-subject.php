<?php

require 'connection.php';
require 'utils.php';

if (!isset($_SESSION['USER_LOGIN'])) redirectToPage('./login.php');

try {
    $sql = "SELECT * FROM usertable WHERE Username = :1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':1', $_SESSION['USER_LOGIN']['username']);

    if ($stmt->execute()) {
        $student = $stmt->fetch();
    } else {
        $_SESSION['REPORT_MSG'] = array();
    }
} catch (\Throwable $th) {
    throw $th;
    exit();
}

try {
    $sql = "SELECT * FROM $_GET[subject]";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute()) {
        $modules = $stmt->fetchAll();

        $currentWeek = (new DateTime())->format("W");
        foreach ($modules as $mod) {
            $deadline = new DateTime($mod['Deadline']);
            if ($deadline->format("W") === $currentWeek && $mod['Open']) $upcomingModules[] = $mod;
        }
    } else {
        $_SESSION['REPORT_MSG'] = array();
    }
} catch (\Throwable $th) {
    throw $th;
    exit();
}

try {
    $sql = "SELECT * FROM chats WHERE Recipient = :1 OR Sender = :1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':1', $student['Username']);

    if ($stmt->execute()) {
        $messages = $stmt->fetchAll();
    } else {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Query failed',
            'body' => 'Failed to fetch message chats.'
        );
    }
} catch (\Throwable $th) {
    $_SESSION['REPORT_MSG'] = array(
        'code' => 3,
        'msg' => 'Server error',
        'body' => $th->getMessage()
    );
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $subjects[$_GET['subject']] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="message-sidebar.css">

    <style>
        .header {
            list-style-type: none;
            margin: 0;
            padding: 0 0.5rem;
            background-color: #333;
            color: white;
        }

        .header ul li {
            float: left;
            list-style: none;
        }

        .header ul li a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .header ul li a:hover:not(.active),
        .dropdown>button:hover,
        .dropdown.show>button {
            background-color: #04AA6D !important;
            color: white !important;
        }

        hr {
            background-color: white;
        }

        .active {
            background-color: #04AA6D;
        }

        /* social media icon */

        .fa {
            font-size: 32px;
            text-align: center;
            width: 32px;
            height: 32px;
        }

        .facebook-wrapper {
            background: #3B5998;
        }

        .twitter-wrapper {
            background: #55ACEE;
        }

        .subject-banner {
            height: 240px;
            background-image: linear-gradient(to bottom, #ffffff00, #000000aa), url('<?= $subjectBackgroundImages[$_GET['subject']] ?>');
            background-repeat: no-repeat;
            -webkit-background-size: cover;
            background-size: cover;
        }
    </style>
</head>

<body>

    <div class="header d-flex sticky-top">
        <ul class="m-0 p-0 mr-auto">
            <li><a href="home.php" style="color: white">Home</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
        <a class="d-flex mx-3" href="#" id="view-chat-sidebar">
            <div class="m-auto">
                <i class="fa fa-comments"></i>
            </div>
            <div class="position-relative">
                <span class="position-absolute badge badge-danger mt-2" style="right: -0.5rem;"></span>
            </div>
        </a>
        <div class="dropdown m-0 align-self-stretch">
            <button class="bg-transparent border-0 h-100 text-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img class="rounded-circle mr-2" width="28" height="28" src="<?= "student_avatars/$student[Avatar]" ?>" alt="<?= $student['Avatar'] ?>"> <?= "$student[Firstname] $student[Lastname]" ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="login.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="main-content w-75 mx-auto pt-3">

        <div class="subject-banner d-flex p-3 mx-auto mb-3 rounded">
            <h1 class="mt-auto text-white m-3"><?= $subjects[$_GET['subject']] ?></h1>
        </div>


        <div class="row">

            <div class="col-4 pr-0">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Upcoming</h5>

                        <?php if (empty($upcomingModules)) :  ?>

                            <p class="card-text">Woohoo, no work due soon!</p>

                        <?php else : ?>

                            <ul class="list-group list-group-flush">

                                <?php foreach ($upcomingModules as $upcoming) : ?>

                                    <li class="list-group-item border-0 py-1 px-0">
                                        <a href="<?= "activity.php?subject=$_GET[subject]&id=$upcoming[Id]" ?>" class="text-decoration-none" style="min-width: 0;">
                                            <p class="m-0 text-truncate">
                                                <?= $upcoming['Activity_Title'] ?>
                                            </p>
                                        </a>
                                    </li>

                                <?php endforeach ?>

                            </ul>

                        <?php endif ?>

                    </div>
                </div>

            </div>

            <div class="col-8">

                <?php if (empty($modules)) : ?>

                    <div class="card mb-3 text-white bg-dark text-decoration-none">
                        <div class="card-body p-3">
                            <h5>No posted activities.</h5>
                        </div>
                    </div>

                <?php else : ?>

                    <?php foreach ($modules as $module) : ?>
                        <a href="<?= "activity.php?subject=$_GET[subject]&id=$module[Id]" ?>" class="card mb-3 text-white bg-dark text-decoration-none">
                            <div class="card-body p-3 d-flex">
                                <img class="rounded-circle bg-<?= $module['Open'] ? 'success' : 'secondary' ?> p-2 my-auto mr-2" src="assets/img/document-icon.svg">
                                <div class="flex-fill" style="min-width: 0">
                                    <p class="m-0 font-weight-bold text-truncate"><?= $module['Activity_Title'] ?></p>
                                    <small><?= $module['Open'] ? 'Open' : 'Closed' ?> | Deadline: <?= formatDate($module['Deadline'], 'F j, Y') ?></small>
                                </div>
                            </div>
                        </a>
                    <?php endforeach ?>

                <?php endif ?>
            </div>

        </div>
    </div>


    <div id="message-sidebar-wrapper">
        <div id="message-sidebar">
            <div class="sidebar-head">
                <p>Administrator</p>
                <a id="close-message-sidebar"><i class="fa fa-close" style="font-size: 1.5em; height: 1em; width: 1em;"></i></a>
            </div>
            <div class="conversation-wrapper">

                <?php foreach ($messages as $message) : ?>

                    <?php if ($message['Sender'] == $student['Username']) : ?>
                        <div class="message-right">
                            <p><?= $message['Message'] ?></p>
                            <small class="text-dark"><?= formatDate($message['DateSent'], 'F j, Y h:m A') ?></small>
                        </div>
                    <?php else : ?>
                        <div class="message-left">
                            <p><?= $message['Message'] ?></p>
                            <small class="text-dark"><?= formatDate($message['DateSent'], 'F j, Y h:m A') ?></small>
                        </div>
                    <?php endif ?>

                <?php endforeach ?>

            </div>
            <div class="reply-box-wrapper">
                <form id="chat-box-reply-form" method="post" class="reply-box" action="query_student_send_chat.php">
                    <textarea name="messagecontent" required></textarea>
                    <button type="submit" name="send-message">
                        <i class="fa fa-send"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>


    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <script src="message-sidebar.js"></script>
</body>

</html>