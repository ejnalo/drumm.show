<?php
	function gen_link() {
		global $DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME;

		$DB_HOST = 'db';
		$DB_USER = 'root';
		$DB_PASSWORD = 'root';
		$DB_NAME = 'test';

		return mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
	}

	function get_artist($id) {
		$link = gen_link();

		$query = "SELECT * FROM artists WHERE id = '$id'";
		$result = mysqli_query($link, $query);

		if (!$result) {
			return null;
		}

		$row = mysqli_fetch_assoc($result);

		return $row;
	}

	function get_scene($id) {
		$link = gen_link();

		$query = "SELECT * FROM scenes WHERE id = '$id'";
		$result = mysqli_query($link, $query);

		if (!$result) {
			return null;
		}

		$row = mysqli_fetch_assoc($result);

		return $row;
	}

	function get_concerts($start, $end) {
		$concerts = [];

		$link = gen_link();

		$query = "SELECT * FROM concerts WHERE planned_at >= '$start' AND planned_at < '$end';";
		$result = mysqli_query($link, $query);

		if (!$result) {
			return [];
		}

		while ($concert = mysqli_fetch_assoc($result)) {
			$artist = get_artist($concert['artist']);
			if (!$artist) {
				print("Le concert " . $concert['id'] . " est produit par un artiste inexistant.");
				continue;
			}

			$scene = get_scene($concert['scene']);
			if (!$scene) {
				print("Le concert " . $concert['id'] . " est produit sur une scène inexistante.");
				continue;
			}

			$concerts[] = array_merge($concert, [
				'artist' => $artist,
				'scene' => $scene,
			]);
		}

		return $concerts;
	}

	$programme = [
		'lundi_aprem' => get_concerts('2026-04-27 15:00:00', '2026-04-27 20:00:00'),
		'lundi_soir' => get_concerts('2026-04-27 20:00:00', '2026-04-28 01:00:00'),
		'mardi_aprem' => get_concerts('2026-04-28 15:00:00', '2026-04-28 20:00:00'),
		'mardi_soir' => get_concerts('2026-04-28 20:00:00', '2026-04-29 01:00:00'),
		'mercredi_aprem' => get_concerts('2026-04-29 15:00:00', '2026-04-29 20:00:00'),
		'mercredi_soir' => get_concerts('2026-04-29 20:00:00', '2026-04-30 01:00:00'),
		'jeudi_aprem' => get_concerts('2026-04-30 15:00:00', '2026-04-30 20:00:00'),
		'jeudi_soir' => get_concerts('2026-04-30 20:00:00', '2026-05-01 01:00:00'),
		'vendredi_aprem' => get_concerts('2026-04-01 15:00:00', '2026-04-01 20:00:00'),
		'vendredi_soir' => get_concerts('2026-04-01 20:00:00', '2026-04-02 01:00:00'),
	];

	function render_concerts($programme, $periode) {
		if (!isset($programme[$periode])) {
			return;
		}

		foreach ($programme[$periode] as $concert) {
			echo '<li class="concert">';
			echo '<img src="' . $concert['artist']['avatar_url'] . '" />';
			echo '<div>';
			echo '<h3>';

			if (isset($concert['artist']['nickname'])) {
				echo $concert['artist']['nickname'];
			} else {
				echo $concert['artist']['first_name'] . ' ' . $concert['artist']['last_name'];
			}

			echo ' - ' . ($concert['name'] ?? 'Concert');
			echo '</h3>';

			echo '<p class="subtext">';
			echo $concert['description'] ?? "Pas de description";
			echo '</p><br/>';

			echo '<p><b>Scène:</b> '
				. $concert['scene']['name']
				. ' (' . $concert['scene']['capacity'] . ' places)</p>';

			echo '<p><b>Heure:</b> de '
				. date('H:i', strtotime($concert['planned_at']))
				. ' à '
				. date('H:i', strtotime($concert['ends_at']))
				. '</p><br/>';

			echo '<a href="/artist.php?id=' . $concert['artist']['id'] . '">';
			echo "Voir l'artiste</a>";

			echo '</div>';
			echo '</li>';
		}
	}
?>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>DRUMM'Show - Programme</title>

		<link rel="stylesheet" href="/assets/css/main.css" type="text/css" />
	</head>
	<body>
		<nav class="navbar">
			<a class="navlink" href="/">
				<span>Accueil</span>
			</a>
			<a class="navlink active" href="/programme.php">
				<span>Programme</span>
			</a>
			<a class="navlink" href="/inscription.php">
				<span>Inscription</span>
			</a>
			<a class="navlink" href="/inscription.php">
				<span>Réservation</span>
			</a>
		</nav>
		<header>
			<h1>Programme</h1>
		</header>
		<main>
			<section id="lundi">
				<h2>Lundi 27/04</h2>
				<ul>
					<?php render_concerts($programme, 'lundi_aprem') ?>
				</ul>
				<ul>
					<?php render_concerts($programme, 'lundi_soir') ?>
				</ul>
			</section>
			<section id="mardidi">
				<h2>Mardi 28/04</h2>
				<ul>
					<?php render_concerts($programme, 'mardi_aprem') ?>
				</ul>
				<ul>
					<?php render_concerts($programme, 'mardi_soir') ?>
				</ul>
			</section>
			<section id="mercredi">
				<h2>Mercredi 29/04</h2>
				<ul>
					<?php render_concerts($programme, 'mercredi_aprem') ?>
				</ul>
				<ul>
					<?php render_concerts($programme, 'mercredi_soir') ?>
				</ul>
			</section>
			<section id="jeudi">
				<h2>Jeudi 30/04</h2>
				<ul>
					<?php render_concerts($programme, 'jeudi_aprem') ?>
				</ul>
				<ul>
					<?php render_concerts($programme, 'jeudi_soir') ?>
				</ul>
			</section>
			<section id="vendredi">
				<h2>Vendredi 01/05</h2>
				<ul>
					<?php render_concerts($programme, 'vendredi_aprem') ?>
				</ul>
				<ul>
					<?php render_concerts($programme, 'vendredi_soir') ?>
				</ul>
			</section>
		</main>
	</body>
</html>
