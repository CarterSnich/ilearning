<?php
require 'connection.php';

unset($_SESSION['ADMIN_LOGIN']);
$isReportSet = isset($_SESSION['REPORT_MSG']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator login</title>
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="toaster.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100vw;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(rgba(0, 0, 50, 0.5), rgba(0, 0, 50, 0.5)), url(back.jpg);
            background-size: cover;
            display: flex;
        }

        #login-form {
            align-self: center;
            margin: auto;
            width: fit-content;
            height: fit-content;
            padding: 1.25rem;
            background-color: rgba(255, 255, 255, 0.4);
            border-radius: 0.25rem;
        }

        #login-form h2 {
            width: fit-content;
            margin: 1.25rem auto;
        }

        #form {
            width: fit-content;
            margin: auto;
            flex-flow: column;
        }

        .form-input {
            display: inline-grid;
            margin-left: auto;
            margin-right: auto;
            width: fit-content;
            padding: .25rem 0;
        }

        .form-input>* {
            padding: 0.25rem;
        }

        #form button {
            display: block;
            margin: auto;
            width: 100%;
            font-size: 24px;
        }

        a {
            display: flex;
            margin-top: 1.25rem !important;
        }
    </style>

</head>

<body>

    <div id="login-form">

        <h2>Administrator login</h2>

        <form method="post" action="verify_admin_login.php" id="form">

            <div class="form-input">
                <label for="username">Username</label>
                <label for="password">Password</label>
            </div>

            <div class="form-input">
                <input type="text" id="username" name="username">
                <input type="password" id="password" name="password">
            </div>

            <button type="submit">Login</button>

        </form>

        <a href="login.php">Student login</a>

    </div>

    <?php if ($isReportSet) : ?>
        <?php
        $report = $_SESSION['REPORT_MSG'];
        unset($_SESSION['REPORT_MSG']);
        ?>

        <div id="toaster-wrapper">
            <div class="toaster">
                <div class="toaster-head">
                    <h4><?= $report['msg'] ?></h4>
                    <button class="toaster-close fa fa-close"></button>
                </div>

                <div class="toaster-body">
                    <p><?= $report['body'] ?></p>
                </div>
            </div>
        </div>

    <?php endif ?>

    <script src="./assets/js/jquery-3.2.1.min.js"></script>
    <script src="./toaster.js"></script>

</body>

</html>