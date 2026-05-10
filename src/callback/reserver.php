<?php
	function gen_link() {
		global $DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME;

		$DB_HOST = 'db';
		$DB_USER = 'root';
		$DB_PASSWORD = 'root';
		$DB_NAME = 'test';

		return mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
	}

	function clean_value($link, $key, $required = true, $default = '') {
		if (!isset($_POST[$key])) {
			return $required ? null : $default;
		}

		return mysqli_real_escape_string($link, trim($_POST[$key]));
	}

	function add_visitor($link, $first_name, $last_name, $age, $mail, $tel) {
		$first_name = mysqli_real_escape_string($link, trim($first_name));
		$last_name = mysqli_real_escape_string($link, trim($last_name));
		$age = mysqli_real_escape_string($link, trim($age));
		$mail = mysqli_real_escape_string($link, trim($mail));
		$tel = $tel === null ? null : mysqli_real_escape_string($link, trim($tel));
		$telValue = $tel === null || $tel === '' ? 'NULL' : "'" . $tel . "'";

		$result = mysqli_query(
			$link,
			"INSERT INTO visitors (first_name, last_name, age, mail, status, student_number, tel, address)
			VALUES ('$first_name', '$last_name', '$age', '$mail', 'normal', NULL, $telValue, NULL)
			ON DUPLICATE KEY UPDATE
				first_name = VALUES(first_name),
				last_name = VALUES(last_name),
				age = VALUES(age),
				tel = VALUES(tel),
				id = LAST_INSERT_ID(id)"
		);

		if ($result) {
			$customerId = mysqli_insert_id($link);
			if ($customerId) {
				return $customerId;
			}
		}

		$result = mysqli_query($link, "SELECT id FROM visitors WHERE mail = '$mail' LIMIT 1");
		if ($result && mysqli_num_rows($result) > 0) {
			$visitor = mysqli_fetch_assoc($result);
			return $visitor['id'];
		}

		return null;
	}

	function show_message($title, $message) {
			echo "<html lang='fr'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>DRUMM'Show - Réservation</title><link rel='stylesheet' href='/assets/css/main.css' type='text/css' /></head><body><nav class='navbar'><a class='navlink' href='/'><span>Accueil</span></a><a class='navlink' href='/programme.php'><span>Programme</span></a><a class='navlink active' href='/reservation.php'><span>Réserver un concert</span></a><a class='navlink' href='/register/artist.php'><span>Inscription artiste</span></a></nav><header><h1>$title</h1><p>$message</p><a href='/reservation.php'>Retour au formulaire</a></header></body></html>";
	}

	$link = gen_link();

	$first_name = clean_value($link, 'first_name');
	$last_name = clean_value($link, 'last_name');
	$age = clean_value($link, 'age');
	$mail = clean_value($link, 'mail');
	$tel = clean_value($link, 'tel', false, '');
	$concert = clean_value($link, 'concert');
	$seats = clean_value($link, 'seats');
	$comment = clean_value($link, 'comment', false, '');

	if (!$first_name || !$last_name || !$age || !$mail || !$concert || !$seats) {
		show_message("Impossible de réserver le concert", "Tous les champs obligatoires doivent être remplis.");
		exit;
	}

	$check = mysqli_query($link, "SELECT id FROM concerts WHERE id = '$concert' LIMIT 1");
	if (!$check || mysqli_num_rows($check) === 0) {
		show_message("Impossible de réserver le concert", "Le concert choisi n'existe pas.");
		exit;
	}

	$visitorId = add_visitor($link, $first_name, $last_name, $age, $mail, $tel);
	if (!$visitorId) {
		show_message("Impossible de créer le visiteur", "Nous n'avons pas pu enregistrer ce visiteur.");
		exit;
	}

	$query = mysqli_query(
		$link,
		"INSERT INTO reservations (owner, concert, seats, comment)
		VALUES ('$visitorId', '$concert', '$seats', '$comment')"
	);

	if (!$query) {
		show_message("Impossible de réserver le concert", mysqli_error($link));
		exit;
	}

	header('Location: /programme.php');
	exit;
?>
