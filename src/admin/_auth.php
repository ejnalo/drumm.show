<?php
	$ADMIN_USERNAME = 'admin';
	$ADMIN_PASSWORD = 'admin123';

	function get_admin_user() {
		if (isset($_POST['admin_user'])) {
			return $_POST['admin_user'];
		}

		if (isset($_GET['admin_user'])) {
			return $_GET['admin_user'];
		}

		return '';
	}

	function get_admin_pass() {
		if (isset($_POST['admin_pass'])) {
			return $_POST['admin_pass'];
		}

		if (isset($_GET['admin_pass'])) {
			return $_GET['admin_pass'];
		}

		return '';
	}

	function admin_params() {
		return 'admin_user=' . get_admin_user() . '&admin_pass=' . get_admin_pass();
	}

	function is_admin_logged_in() {
		global $ADMIN_USERNAME, $ADMIN_PASSWORD;
		$user = get_admin_user();
		$pass = get_admin_pass();

		return $user === $ADMIN_USERNAME && $pass === $ADMIN_PASSWORD;
	}

	function require_admin() {
		if (!is_admin_logged_in()) {
			header('Location: /admin/login.php');
			exit;
		}
	}
?>
