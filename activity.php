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
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Query failed.',
            'body' => 'Failed to fetch student data.'
        );
        redirectToPage('login.php');
    }
} catch (\Throwable $th) {
    $_SESSION['REPORT_MSG'] = array(
        'code' => 3,
        'msg' => 'Failed to fetch student data.',
        'body' => 'Connection error.\n' . $th->getMessage()
    );
    redirectToPage('login.php');
}


try {
    $sql = "SELECT * FROM $_GET[subject] WHERE Id = :1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':1', $_GET['id']);

    if ($stmt->execute()) {
        $module = $stmt->fetch();
    } else {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Query failed.',
            'body' => 'Failed to fetch activity data.'
        );
        redirectToPage('home.php');
    }
} catch (\Throwable $th) {
    $_SESSION['REPORT_MSG'] = array(
        'code' => 3,
        'msg' => 'Failed to fetch activity data.',
        'body' => 'Connection error.\n' . $th->getMessage()
    );
    redirectToPage('home.php');
}

try {
    $sql = "SELECT * FROM submissions WHERE Subject = :1 AND ActivityId = :2 AND StudentUsername = :3";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':1', $_GET['subject']);
    $stmt->bindParam(':2', $_GET['id']);
    $stmt->bindParam(':3', $student['Username']);

    if ($stmt->execute()) {
        if ($stmt->rowCount()) $uploadedAnswer = $stmt->fetch();
    } else {
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Query failed.',
            'body' => 'Failed to fetch uploaded answer.'
        );
    }
} catch (\Throwable $th) {
    $_SESSION['REPORT_MSG'] = array(
        'code' => 3,
        'msg' => 'Failed to fetch uploaded answer.',
        'body' => 'Connection error.\n' . $th->getMessage()
    );
}

if (isset($_POST['unsubmit-work-button'])) {
    try {
        $conn->beginTransaction();

        $sql = "DELETE FROM submissions WHERE Subject = :1 AND ActivityId = :2 AND StudentUsername = :3";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':1', $_GET['subject']);
        $stmt->bindParam(':2', $_GET['id']);
        $stmt->bindParam(':3', $student['Username']);

        if ($stmt->execute()) {
            if (unlink("submissions/$uploadedAnswer[UploadedFile]")) {
                $conn->commit();
            } else {
                $conn->rollBack();
                $_SESSION['REPORT_MSG'] = array(
                    'code' => 3,
                    'msg' => 'Server error',
                    'body' => 'Failed to delete file.'
                );
            }
        } else {
            $conn->rollBack();
            $_SESSION['REPORT_MSG'] = array(
                'code' => 3,
                'msg' => 'Server error',
                'body' => $th->getMessage()
            );
        }
    } catch (\Throwable $th) {
        if ($conn->inTransaction()) $conn->rollBack();
        $_SESSION['REPORT_MSG'] = array(
            'code' => 3,
            'msg' => 'Server error',
            'body' => $th->getMessage()
        );
    }
    redirectToPage("activity.php?subject=$_GET[subject]&id=$_GET[id]");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $module['Activity_Title'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/quill/quill.snow.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="toaster.css">

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

        .hide-me {
            display: none !important;
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
        <div class="dropdown m-0 align-self-stretch">
            <button class="bg-transparent border-0 h-100 text-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img class="rounded-circle mr-2" width="28" height="28" src="<?= "student_avatars/$student[Avatar]" ?>" alt="<?= $student['Avatar'] ?>"> <?= "$student[Firstname] $student[Lastname]" ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="login.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="main-content w-50 mx-auto mt-3 mb-3 py-2 px-3 pb-3 border border-success rounded" style="background-color: #28282888;">

        <div class="text-light">
            <div class="d-flex mb-2">
                <img class="rounded-circle bg-<?= $module['Open'] ? 'success' : 'secondary' ?> p-2 mt-2 mb-auto mr-3" src="assets/img/document-icon.svg">
                <h2 class="my-auto"><?= $module['Activity_Title'] ?></h3>
            </div>
            <span class="d-flex justify-content-between">
                <p class="m-0">Deadline: <?= formatDate($module['Deadline'], 'F j, Y') ?> &bullet; <?= $module['Open'] ? 'Open' : 'Closed' ?></p>
                <?php if (isset($uploadedAnswer) && $uploadedAnswer['Score']) : ?>
                    <p class="m-0">Score: <?= "$uploadedAnswer[Score]/$module[MaxScore]" ?></p>
                <?php else : ?>
                    <p class="m-0"><?= $module['MaxScore'] ?> points</p>
                <?php endif ?>
            </span>
        </div>

        <hr style="background-color: #04AA6D; height: 0.1rem; width: 100%;">

        <div id="instructions" class="container text-white"></div>

        <?php if (!empty($module['ModuleFile'])) : ?>
            <a href="javascript:void(0);" class="d-flex mx-3 bg-dark border border-success rounded text-decoration-none" data-toggle="modal" data-target="#modal-pdf-viewer">
                <div class="border-right border-success p-3">
                    <i class="fa fa-file-pdf-o fa-2x text-danger"></i>
                </div>
                <p class="my-auto mx-3 text-light"><?= $module['ModuleFile'] ?></p>
            </a>
        <?php endif ?>

        <hr style="background-color: #04AA6D; height: 0.1rem; width: 100%;">

        <?php if (isset($uploadedAnswer)) : ?>
            <form method="POST" class="px-3 text-light">
                <h3>Your work</h3>
                <a href="javascript:void(0);" class="d-flex mb-3 bg-dark border border-success rounded text-decoration-none" data-toggle="modal" data-target="#preview-answer">
                    <div class="border-right border-success p-3">
                        <i class="fa fa-file-pdf-o fa-2x text-danger"></i>
                    </div>
                    <div class="d-flex flex-column my-auto mx-3">
                        <p class="m-0 text-light"><?= $uploadedAnswer['UploadedFile'] ?></p>
                        <small class="text-muted">Submitted: <?= formatDate($uploadedAnswer['DateSubmitted'], 'F j, Y') ?></small>
                    </div>
                </a>
                <?php if ($uploadedAnswer['Score']) : ?>
                    <button type="button" class="btn btn-success btn-block" disabled>Checked</button>
                <?php else : ?>
                    <button type="submit" id="unsubmit-work-button" class="btn btn-success btn-block" name="unsubmit-work-button" <?= !$module['Open'] ? 'disabled' : '' ?>>Unsubmit</button>
                <?php endif ?>
            </form>
        <?php elseif ($module['Open']) : ?>
            <form class="px-3 text-light" method="POST" action="query_upload_file.php?subject=<?= $_GET['subject'] ?>&id=<?= $_GET['id'] ?>" enctype="multipart/form-data">
                <h3>Your work</h3>
                <div id="uploaded-files" class="mb-3"></div>
                <button type="button" id="upload-file-button" class="btn btn-dark btn-block border-success"><i class="fa fa-plus"></i> Add work</button>
                <button type="submit" id="submit-work-button" class="btn btn-success btn-block" name="submit-work-button" disabled><?= dateCompare($module['Deadline'], 'Y-m-d') == 1 ? 'Missing' : 'Submit' ?></button>
            </form>
        <?php else : ?>
            <div class="px-3">
                <button type="button" class="btn btn-secondary btn-block disabled" disabled>Missing</button>
            </div>
        <?php endif ?>

    </div>

    <!-- modal pdf viewer -->
    <div class="modal fade" id="modal-pdf-viewer" tabindex="-1" aria-labelledby="modal-pdf-viewer" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div style="height: 88vh;">
                    <iframe src="./view-module-pdf.php?module_pdf=<?= $module['ModuleFile'] ?>" frameborder="0" width="100%" height="100%"></iframe>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($uploadedAnswer)) : ?>
        <div class="modal fade" id="preview-answer" tabindex="-1" aria-labelledby="modal-pdf-viewer" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div style="height: 88vh;">
                        <iframe src="./view-answer-pdf.php?answerfile=<?= $uploadedAnswer['UploadedFile'] ?>" frameborder="0" width="100%" height="100%"></iframe>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="modal fade" id="uploaded-file-modal" tabindex="-1" aria-labelledby="uploaded-file" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div style="height: 88vh;">
                        <embed width="100%" height="100%" name="plugin" id="embed-uploaded-file" />
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>

    <?php if (isset($_SESSION['REPORT_MSG'])) : ?>
        <div id="toaster-wrapper">
            <div class="toaster">
                <div class="toaster-head">
                    <p><?= $_SESSION['REPORT_MSG']['msg'] ?></p>
                    <button class="toaster-close fa fa-close"></button>
                </div>
                <div class="toaster-body">
                    <p><?= $_SESSION['REPORT_MSG']['body'] ?></p>
                </div>
            </div>
        </div>
    <?php endif ?>

    <div id="dummy-quill" class="d-none"></div>

    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/quill/quill.js"></script>
    <script src="toaster.js"></script>

    <script>
        $(document).ready(function() {

            var quill = new Quill('#dummy-quill');
            quill.setContents(JSON.parse(<?= $module['Instructions'] ?>));
            $('#instructions').html(quill.root.innerHTML);

            $('#uploaded-files').on('click', '.file-query', function() {});

            $('#upload-file-button').on('click', function() {
                const fileInputNode = `
                    <span id="uploaded-file" class="hide-me d-flex btn btn-dark border border-success p-0" data-toggle="modal" data-target="#uploaded-file-modal">
                        <input type="file" class="d-none" name="uploaded-pdf" accept="application/pdf">
                        <div class="border-right border-success p-3">
                            <i class="fa fa-file-pdf-o fa-2x text-danger"></i>
                        </div>
                        <label class="my-auto mx-3 flex-fill text-left text-light"></label>
                        <button id="remove-uploaded" type="button" class="btn bg-transparent border-0 my-auto mx-3">
                            <i class="fa fa-close text-light"></i>
                        </button>
                    </span>
                `;

                $('#uploaded-files').html(fileInputNode.trim());
                $('#uploaded-file input[type=file]').on('click', function(e) {
                    e.stopPropagation();
                })
                $('#uploaded-file input[type=file]').click();
            })

            $('#uploaded-files').on('change', '#uploaded-file>input[type=file]', function() {
                let _this = this;
                if (!(this.files && this.files[0] && this.files[0].name != "" && this.files[0].name === 'application/pdf')) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#embed-uploaded-file').attr('src', e.target.result);
                        $('#uploaded-file>label').text(_this.files[0].name);
                        $(_this.parentElement).removeClass('hide-me');
                        $('#upload-file-button').addClass('d-none');
                        $('#submit-work-button').attr('disabled', false);
                        $('#submit-work-button').text('Submit');
                    };
                    reader.readAsDataURL(this.files[0]);
                } else {
                    $('#uploaded-file').remove();
                    $('#upload-file-button').removeClass('d-none');
                    $('#submit-work-button').attr('disabled', true);
                }
            });

            $('#uploaded-files').on('click', '#uploaded-file #remove-uploaded', function(e) {
                e.stopPropagation();
                $('#uploaded-file').remove();
                $('#upload-file-button').removeClass('d-none');
                $('#submit-work-button').attr('disabled', true);
                $('#submit-work-button').text('Submit');
            })
        })
    </script>

</body>

</html>

<?php
unset($_SESSION['REPORT_MSG']);
