<?php
require_once 'connection.php';
require 'utils.php';

if (!isset($_SESSION['ADMIN_LOGIN'])) redirectToPage('./admin_login.php');

$username = $_SESSION['ADMIN_LOGIN']['username'];

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

// get students
try {
    $sql = "SELECT Username, CONCAT(Firstname, ' ', Lastname) AS StudentName FROM usertable";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute()) {
        $studentContacts = $stmt->fetchAll();
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

<!-- chat23:28-->

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

    <style>
        #toast-wrapper {
            position: fixed;
            left: 1.5rem;
            bottom: 1.5rem;
            z-index: 2000;
        }

        #loading-div-blocker {
            background-color: #000000a2;
            position: absolute;
            z-index: 200;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <div class="header">
            <div class="header-left">
                <a href="admin_dashboard.php" class="logo">
                    <img src="assets/img/logo.png" width="35" height="35" alt=""> <span>EVSU Dulag</span>
                </a>
            </div>
            <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>
            <ul class="nav user-menu float-right">
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                        <span class="user-img"><img class="rounded-circle" src="assets/img/user.jpg" width="40" alt="Admin"><span class="status online"></span></span>
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
                        <li class="menu-title">Direct Chats <a href="#" class="add-user-icon" data-toggle="modal" data-target="#add_chat_user"><i class="fa fa-plus"></i></a></li>
                    </ul>
                    <ul id="conversation-sidebar">
                        <?php foreach ($studentMessages as $message) : ?>
                            <li>
                                <a id="sidebar-chats-view-<?= $message['Recipient'] ?>" href="javascript:void(0);" data-username="<?= $message['Recipient'] ?>">
                                    <span class="chat-avatar-sm user-img">
                                        <img src="student_avatars/<?= $message['StudentAvatar'] ?>" alt="" class="rounded-circle" width="42" height="24">
                                    </span>
                                    <?= $message['StudentName'] ?>
                                    <?php if ($message['UnreadMessageCount'] == 'adhd') : ?>
                                        <span class="badge badge-pill bg-danger float-right"><?= $message['UnreadMessageCount'] ?></span>
                                    <?php endif ?>
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="page-wrapper">
            <div class="chat-main-row">
                <div class="chat-main-wrapper">
                    <div class="col-lg-9 message-view chat-view">
                        <div id="loading-div-blocker">
                            <div class="d-flex w-100 h-100">
                                <div class="spinner-border text-primary m-auto" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="chat-window">
                            <div class="fixed-header">
                                <div class="navbar">
                                    <div class="d-flex mr-auto">
                                        <div class="float-left user-img m-r-10">
                                            <img id="topbar-student-avatar" src="student_avatars/user.jpg" alt="" height="40" width="40" class="w-40 rounded-circle">
                                        </div>
                                        <div class="user-info my-auto float-left">
                                            <h3 class="m-0" id="topbar-student-name"></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-contents">
                                <div class="chat-content-wrap">
                                    <div class="chat-wrap-inner">
                                        <div class="chat-box pt-3">
                                            <?php foreach ($studentMessages as $message) : ?>
                                                <div id="chats-wrapper-<?= $message['Recipient'] ?>" class="chats d-none py-0" onload></div>
                                            <?php endforeach ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-footer">
                                <div class="message-bar">
                                    <div class="message-inner">
                                        <div class="message-area">
                                            <form id="dummy-form-send-message" class="needs-validation" method="POST" action="query_admin_send_message.php" novalidate>
                                                <input type="text" class="d-none" name="studentusername">
                                                <div class="input-group">
                                                    <textarea class="form-control" placeholder="Type message..." name="messagecontent" form="dummy-form-send-message" required></textarea>
                                                    <span class="input-group-append">
                                                        <button class="btn btn-primary" type="submit" name="send-message" form="dummy-form-send-message"><i class="fa fa-send"></i></button>
                                                    </span>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 message-view chat-profile-view chat-sidebar" id="chat_sidebar">
                        <div class="chat-window video-window">
                            <div class="tab-content chat-contents">
                                <div class="content-full tab-pane show active" id="profile_tab">
                                    <div class="display-table">
                                        <div class="table-row">
                                            <div class="table-body">
                                                <div class="table-content">
                                                    <div class="chat-profile-img">
                                                        <div class="edit-profile-img">
                                                            <img id="sidebar-student-avatar" src="student_avatars/user.jpg" alt="" style="width: 120px; height: 120px;">
                                                        </div>
                                                        <h3 id="sidebar-student-name" class="user-name m-t-10 mb-0"></h3>
                                                    </div>
                                                    <div class="chat-profile-info">
                                                        <ul class="user-det-list">
                                                            <li>
                                                                <span>Username:</span>
                                                                <span class="float-right text-muted" id="profile-student-username"></span>
                                                            </li>
                                                            <li>
                                                                <span>Birthdate:</span>
                                                                <span class="float-right text-muted" id="profile-student-birthdate"></span>
                                                            </li>
                                                            <li>
                                                                <span>Email:</span>
                                                                <span class="float-right text-muted" id="profile-student-email"></span>
                                                            </li>
                                                            <li>
                                                                <span>Phone:</span>
                                                                <span class="float-right text-muted" id="profile-student-phone"></span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="add_chat_user" class="modal fade " role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">New message</h3>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="query_admin_send_message.php" class="needs-validation" novalidate>
                                <div class="input-group m-b-30">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">To: </div>
                                    </div>
                                    <select name="studentusername" class="custom-select" required>
                                        <option value="" disabled selected>Select student</option>
                                        <?php foreach ($studentContacts as $contact) :  ?>
                                            <option value="<?= $contact['Username'] ?>"><?= $contact['StudentName'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div>
                                    <textarea name="messagecontent" cols="30" rows="10" class="form-control" placeholder="Type message here...." required></textarea>
                                </div>
                                <div class="mt-3 text-center">
                                    <button type="submit" name="send-message" class="btn btn-primary submit-btn">Send</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="toast-wrapper">
        <?php if (isset($_SESSION['REPORT_MSG'])) : ?>
            <div class="toast border border-<?= $_SESSION['REPORT_MSG'] ? 'danger' : 'primary' ?>" role="alert" aria-live="assertive" aria-atomic="true" data-delay="10000">
                <div class="toast-header bg-<?= $_SESSION['REPORT_MSG'] ? 'danger' : 'primary' ?>">
                    <i class="fa fa-warning pr-1"></i>
                    <strong class="mr-auto"><?= $_SESSION['REPORT_MSG']['msg'] ?></strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    <?= $_SESSION['REPORT_MSG']['body'] ?>
                </div>
            </div>
        <?php endif ?>
    </div>

    <div class="sidebar-overlay" data-reff=""></div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/moment.js"></script>
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

        $(document).ready(function() {
            $('.toast').toast('show');

            $('.toast').on('hidden.bs.toast', () => $(this).toast('dispose'));

            $('#conversation-sidebar>li').on('click', 'a', function() {
                $('#loading-div-blocker').removeClass('d-none');
                $('.chats').addClass('d-none');
                $('#conversation-sidebar>li').removeClass('active');
                $(this).parent().addClass('active');

                const studentUsername = this.dataset['username'];

                let body = new FormData();
                body.append('studentUsername', studentUsername);

                fetch('query_admin_get_chat.php', {
                        method: 'POST',
                        body: body
                    })
                    .then((res) => res.json())
                    .then((data) => {
                        if (data.code == 0) {
                            const studentData = data.body['studentData'];

                            $(`#chats-wrapper-${studentUsername}`).html('');
                            $(`#chats-wrapper-${studentUsername}`).removeClass('d-none');
                            $('#topbar-student-avatar').attr("src", `student_avatars/${studentData['Avatar']}`);
                            $('#topbar-student-name').text(studentData['StudentName']);
                            $('#sidebar-student-avatar').attr("src", `student_avatars/${studentData['Avatar']}`);
                            $('#sidebar-student-name').text(studentData['StudentName']);
                            $('#dummy-form-send-message>input[type=text]').val(studentData['Username']);

                            $('#profile-student-username').text(`@${studentData['Username']}`);
                            $('#profile-student-birthdate').text(moment(studentData['DateOfBirth']).format('MMM D, YYYY'));
                            $('#profile-student-email').text(studentData['Email']);
                            $('#profile-student-phone').text(formatPhoneNumber(studentData['PhoneNumber']));


                            $('#loading-div-blocker').addClass('d-none');

                            $.each(data.body.messages, function(index, message) {
                                if (message['Sender'] == '<?= $username ?>') {
                                    $(`#chats-wrapper-${studentUsername}`).append(`
                                        <div class="chat chat-right">
                                            <div class="chat-body">
                                                <div class="chat-bubble">
                                                    <div class="chat-content">
                                                        <p>${message['Message']}</p>
                                                        <span class="chat-time">${moment(message['DateSent']).format('MMM D, YYYY LT')}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `);
                                } else {
                                    $(`#chats-wrapper-${studentUsername}`).append(`
                                        <div class="chat chat-left">
                                            <div class="chat-avatar">
                                                <div class="avatar">
                                                    <img alt="Jennifer Robinson" src="student_avatars/${data.body.studentData.Avatar}" class="img-fluid rounded-circle">
                                                </div>
                                            </div>
                                            <div class="chat-body">
                                                <div class="chat-bubble">
                                                    <div class="chat-content">
                                                        <p>${message['Message']}</p>
                                                        <span class="chat-time">${moment(message['DateSent']).format('MMM D, YYYY LT')}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `);
                                }
                            });
                        } else {
                            $('#toast-wrapper').append(`
                                <div class="toast border border-danger" role="alert" aria-live="assertive" aria-atomic="true" data-delay="10000">
                                    <div class="toast-header bg-danger">
                                        <i class="fa fa-warning pr-1"></i>
                                        <strong class="mr-auto">${data.msg}</strong>
                                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="toast-body">${data.body}</div>
                                </div>
                            `);
                            $('.toast').toast('show');
                        }

                    })
            })

            var url = new URL(window.location.href);
            if (url.searchParams.get("view")) {
                $(`#sidebar-chats-view-${url.searchParams.get("view")}`).click();
            } else {
                $($('#conversation-sidebar>li>a')[0]).click();
            }
        })

        function formatPhoneNumber(phoneNumber) {
            const matches = phoneNumber.match(/^(\d{4})(\d{3})(\d{4})$/);
            return `${matches[1]}-${matches[2]}-${matches[3]}`;
        }
    </script>
</body>


<!-- chat23:29-->

</html>
<?php unset($_SESSION['REPORT_MSG']); ?>