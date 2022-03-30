<?php
require 'connection.php';
require 'utils.php';

unset($_SESSION['USER_LOGIN']);


if (isset($_POST['login-student-btn'])) {
	try {
		$sql = "SELECT Password FROM usertable WHERE Username = :1";

		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':1', $_POST['username']);
		$stmt->execute();

		if ($stmt->rowCount() > 0) {
			$result = $stmt->fetch();

			if (password_verify($_POST['password'], $result['Password'])) {
				$_SESSION['USER_LOGIN'] = array(
					"username" => $_POST['username']
				);

				redirectToPage('./home.php');
			} else {
				$_SESSION['REPORT_MSG'] = array(
					'code' => 1,
					'msg' => '',
					'body' => 'Incorrect password.'
				);
			}
		} else {
			$_SESSION['REPORT_MSG'] = array(
				'code' => 1,
				'msg' => 'Login failed',
				'body' => 'Username not found.'
			);
			redirectToPage('./login.php');
		}
	} catch (\Throwable $th) {
		$_SESSION['REPORT_MSG'] = array(
			'code' => 3,
			'msg' => 'Server error',
			'body' => 'Connection error. ' . $th->getMessage()
		);
		redirectToPage('./login.php');
	}
}

?>

<!DOCTYPE html>
<html>

<head>
	<title>User Log-in</title>
	<link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
	<div class="container">
		<div class="login-box" style="margin-top: 5%; margin-bottom: 0;">
			<div class="d-flex">
				<div class="w-50 mx-auto login-left">
					<h2>LOG IN</h2>
					<form method="POST">
						<div class="form-group">
							<label>Username</label>
							<input type="text" name="username" value="<?= isset($_POST['username']) ? $_POST['username'] : '' ?>" class="form-control" required>
						</div>

						<div class="form-group">
							<label>Password</label>
							<input type="password" name="password" class="form-control" required>
						</div>
						<br>
						<button type="submit" name="login-student-btn" class="btn btn-primary">Log In</button>
						<br><br>
						<a href="admin_login.php" style="color: purple;">Administrator login</a>
					</form>

					<?php if (isset($_SESSION['REPORT_MSG'])) : ?>
						<div class="alert alert-warning alert-dismissible fade show" role="alert">
							<?= $_SESSION['REPORT_MSG']['body'] ?>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					<?php endif ?>

				</div>

				<!-- <div class="col-md-6 login-right">
					<h2>REGISTER</h2>
					<form action="registration.php" method="post">
						<div class="form-group">
							<label>Username</label>
							<input type="text" name="user" class="form-control" required="">
						</div>
						<div class="form-group">
							<label>Password</label>
							<input type="password" name="password" class="form-control" required="">
						</div>
						<br>
						<button type="submit" class="btn btn-primary">Register</button>
						<br><br>
						<p style="color: violet; ">Don't have an account? Register Here</p>
					</form>
				</div> -->
			</div>

		</div>

	</div>

	<script src="./assets/js/jquery-3.2.1.min.js"></script>
	<script src="./assets/js/bootstrap.min.js"></script>

</body>

</html>

<?php

unset($_SESSION['REPORT_MSG']);
