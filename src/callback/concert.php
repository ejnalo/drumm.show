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

	function show_message($title, $message) {
			echo "<html lang='fr'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>DRUMM'Show - Réservation concert</title><link rel='stylesheet' href='/assets/css/main.css' type='text/css' /></head><body><nav class='navbar'><a class='navlink' href='/'><span>Accueil</span></a><a class='navlink' href='/programme.php'><span>Programme</span></a><a class='navlink' href='/reservation.php'><span>Réserver un concert</span></a><a class='navlink active' href='/register/concert.php'><span>Horaire concert</span></a></nav><header><h1>$title</h1><p>$message</p><a href='/register/concert.php'>Retour au formulaire</a></header></body></html>";
	}

	$link = gen_link();

	$artist = clean_value($link, 'artist');
	$name = clean_value($link, 'name');
	$scene = clean_value($link, 'scene');
	$planned_at = isset($_POST['planned_at']) ? mysqli_real_escape_string($link, str_replace('T', ' ', $_POST['planned_at'])) . ':00' : null;
	$ends_at = isset($_POST['ends_at']) ? mysqli_real_escape_string($link, str_replace('T', ' ', $_POST['ends_at'])) . ':00' : null;
	$description = clean_value($link, 'description', false, '');

	if (!$artist || !$name || !$scene || !$planned_at || !$ends_at) {
		show_message("Impossible de réserver l'horaire", "Tous les champs obligatoires doivent être remplis.");
		exit;
	}

	if (strtotime($ends_at) <= strtotime($planned_at)) {
		show_message("Impossible de réserver l'horaire", "L'heure de fin doit être après l'heure de début.");
		exit;
	}

	$check = mysqli_query($link, "SELECT id FROM artists WHERE id = '$artist' LIMIT 1");
	if (!$check || mysqli_num_rows($check) === 0) {
		show_message("Impossible de réserver l'horaire", "L'artiste choisi n'existe pas.");
		exit;
	}

	$check = mysqli_query($link, "SELECT id FROM scenes WHERE id = '$scene' LIMIT 1");
	if (!$check || mysqli_num_rows($check) === 0) {
		show_message("Impossible de réserver l'horaire", "La scène choisie n'existe pas.");
		exit;
	}

	$check = mysqli_query(
		$link,
		"SELECT concerts.id
		FROM concerts
		INNER JOIN scenes ON scenes.id = concerts.scene
		WHERE scenes.id = '$scene'
			AND concerts.planned_at < '$ends_at'
			AND concerts.ends_at > '$planned_at'
		LIMIT 1"
	);

	if ($check && mysqli_num_rows($check) > 0) {
		show_message("Impossible de réserver l'horaire", "Cette scène est déjà occupée sur ce créneau.");
		exit;
	}

	$query = mysqli_query(
		$link,
		"INSERT INTO concerts (planned_at, ends_at, name, description, scene, artist)
		VALUES ('$planned_at', '$ends_at', '$name', '$description', '$scene', '$artist')"
	);

	if (!$query) {
		show_message("Impossible de réserver l'horaire", mysqli_error($link));
		exit;
	}

	header('Location: /programme.php');
	exit;
?>
