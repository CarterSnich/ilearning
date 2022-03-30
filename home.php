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
			'msg' => 'Query failed',
			'body' => 'Failed to fetch student data.'
		);
	}
} catch (\Throwable $th) {
	$_SESSION['REPORT_MSG'] = array(
		'code' => 3,
		'msg' => 'Server error',
		'body' => $th->getMessage()
	);
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

<html>

<head>
	<title>Home Page</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" href="message-sidebar.css">
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

		.active {
			background-color: #04AA6D;

		}

		.w3-card-4 {

			size: 50%;
		}

		h1 {

			color: white;
		}

		hr {
			background-color: white;
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
	</style>
</head>

<body>

	<div class="header d-flex sticky-top">
		<ul class="m-0 p-0 mr-auto">
			<li><a class="active" href="#home" style="color: white">Home</a></li>
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

	<br>
	<br>

	<center>

		<div class="w3-container">
			<br>
			<a href="view-subject.php?subject=philosophy" class="btn btn-info btn-lg">
				<img src="phil.jpg" alt="Alps" width="480">
			</a>
		</div>

		<hr>

		<div class="w3-container">
			<br>
			<a href="view-subject.php?subject=english" class="btn btn-info btn-lg">
				<img src="english.jpg" alt="Alps" width="480">
			</a>
		</div>


		<hr>
		<div class="w3-container">
			<br>
			<a href="view-subject.php?subject=pe" class="btn btn-info btn-lg">
				<img src="pe.png" alt="Alps" width="480">
			</a>
		</div>

		<hr>
		<div class="w3-container">
			<br>
			<a href="view-subject.php?subject=filipino" class="btn btn-info btn-lg">
				<img src="filipino.jpg" alt="Alps" width="480">
			</a>
		</div>

		<hr>
		<div class="w3-container">
			<br>
			<a href="view-subject.php?subject=practicalresearch" class="btn btn-info btn-lg">
				<img src="practicalresearch2.png" alt="Alps" width="480">
			</a>
		</div>

		<hr>
		<div class="w3-container">
			<br>
			<a href="view-subject.php?subject=homeroomguidance" class="btn btn-info btn-lg">
				<img src="homeroomguidance.png" alt="Alps" width="480">
			</a>
		</div>

		<hr>
		<div class="w3-container">
			<br>
			<a href="view-subject.php?subject=css" class="btn btn-info btn-lg">
				<img src="ict.jpg" alt="Alps" alt="Alps" width="480">
			</a>
		</div>

		<br>

	</center>


	<!--Footer-->
	<footer class="d-flex flex-column px-5 pt-3 pb-0 bg-success">
		<div class="mx-auto">
			<h4 class="h4">Follow our iLearning</h4>
			<div class="d-flex mb-3">
				<a class="facebook-wrapper rounded-circle text-light ml-auto mr-2 p-2" href="javascript:void(0);"><i class="m-1 fa fa-facebook"></i></a>
				<a class="twitter-wrapper rounded-circle text-light ml-2 mr-auto p-2" href="javascript:void(0);"><i class="m-1 fa fa-twitter"></i></a>
			</div>
		</div>
		<pre class="d-flex mx-auto text-light m-0" style="font-family: inherit;">
			<p>© Copyright <?= date('Y') ?> iLearning</p> | <a href="javascript:void(0);" class="text-decoration-none text-light">Privacy Policy</a> | <a href="javascript:void(0);" class="text-decoration-none text-light">Terms & Conditions</a>
		</pre>
	</footer>

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

	<?php if (isset($_SESSION['REPORT_MSG'])) : ?>
		<div id="toaster-wrapper">
			<div class="toaster">
				<div class="toaster-head">
					<h4><?= $_SESSION['REPORT_MSG']['msg'] ?></h4>
					<button class="toaster-close fa fa-close"></button>
				</div>

				<div class="toaster-body">
					<p><?= $_SESSION['REPORT_MSG']['body'] ?></p>
				</div>
			</div>
		</div>

	<?php endif ?>

	<script src="assets/js/jquery-3.2.1.min.js"></script>
	<script src="assets/js/popper.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
	<script src="message-sidebar.js"></script>
	<script src="toaster.js"></script>

</body>

</html>