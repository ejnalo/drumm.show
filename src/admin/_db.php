<?php
	function gen_link() {
		$DB_HOST = 'db';
		$DB_USER = 'root';
		$DB_PASSWORD = 'root';
		$DB_NAME = 'test';

		return mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
	}

	function clean_value($link, $key, $required = true, $default = '') {
		if (!isset($_POST[$key])) {
			if ($required) {
				return null;
			}

			return mysqli_real_escape_string($link, $default);
		}

		$value = trim($_POST[$key]);

		if ($required && $value === '') {
			return null;
		}

		return mysqli_real_escape_string($link, $value);
	}

	function sql_optional($value) {
		if ($value === null || $value === '') {
			return 'NULL';
		}

		return "'" . $value . "'";
	}

	function escape_html($value) {
		if ($value === null) {
			return '';
		}

		$value = str_replace('&', '&amp;', $value);
		$value = str_replace('<', '&lt;', $value);
		$value = str_replace('>', '&gt;', $value);
		$value = str_replace('"', '&quot;', $value);
		$value = str_replace("'", '&#039;', $value);

		return $value;
	}
?>
