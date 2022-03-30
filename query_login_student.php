<?php
require 'connection.php';
require 'utils.php';

if (isset($_POST['login-student-btn'])) {
	$sql = "SELECT Password FROM usertable WHERE Username = :1";

	$stmt = $conn->prepare($sql);
	$stmt->bindParam(':1', $_POST['username']);
	$stmt->execute();

	if ($stmt->rowCount() > 0) {
		$result = $stmt->fetch();

		if (password_verify($_POST['password'], $result['Password'])) {
		}
	} else {
		$_SESSION['REPORT_MSG'] = array(
			'code' => 1,
			'msg' => 'Login failed',
			'body' => 'Username not found.'
		);
	}
} else {
	redirectToPage('./login.php');
}
