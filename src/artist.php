<?php
	function gen_link() {
		global $DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME;

		$DB_HOST = 'db';
		$DB_USER = 'root';
		$DB_PASSWORD = 'root';
		$DB_NAME = 'test';

		return mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
	}

	function fetch_artist($id) {
		$link = gen_link();
		$id = mysqli_real_escape_string($link, $id);

		$result = mysqli_query($link, "SELECT * FROM artists WHERE id = '$id' LIMIT 1");

		if (!$result) {
			return null;
		}

		return mysqli_fetch_assoc($result);
	}

	function fetch_concerts($artistId) {
		$link = gen_link();
		$artistId = mysqli_real_escape_string($link, $artistId);
		$result = mysqli_query(
			$link,
			"SELECT concerts.name, concerts.planned_at, concerts.ends_at, scenes.name AS scene_name
			FROM concerts
			INNER JOIN scenes ON scenes.id = concerts.scene
			WHERE concerts.artist = '$artistId'
			ORDER BY concerts.planned_at ASC"
		);

		if (!$result) {
			return [];
		}

		$concerts = [];
		while ($concert = mysqli_fetch_assoc($result)) {
			$concerts[] = $concert;
		}

		return $concerts;
	}

	function render_social_link($label, $baseUrl, $handle) {
		if (!$handle) {
			return;
		}

		echo '<li><a href="' . $baseUrl . $handle . '" target="_blank" rel="noreferrer">' . $label . '</a></li>';
	}

	$artistId = $_GET['id'] ?? ($_GET['artistId'] ?? null);
	$artist = $artistId ? fetch_artist($artistId) : null;
	$concerts = $artist ? fetch_concerts($artist['id']) : [];
	$pageTitle = $artist ? (($artist['nickname'] ?: ($artist['first_name'] . ' ' . $artist['last_name'])) . ' - DRUMM\'Show') : 'DRUMM\'Show - Artiste';
?>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo $pageTitle; ?></title>

		<link rel="stylesheet" href="/assets/css/main.css" type="text/css" />
	</head>
	<body>
		<nav class="navbar">
			<a class="navlink" href="/">
				<span>Accueil</span>
			</a>
			<a class="navlink" href="/programme.php">
				<span>Programme</span>
			</a>
			<a class="navlink" href="/reservation.php">
				<span>Réserver un concert</span>
			</a>
			<a class="navlink active" href="/artist.php">
				<span>Artiste</span>
			</a>
		</nav>
		<header>
			<h1>Artiste</h1>
			<p>Découvre le profil et les concerts de l'artiste sélectionné.</p>
		</header>
		<main>
			<section>
				<?php if (!$artist) { ?>
					<h2>Aucun artiste sélectionné</h2>
					<p>Ajoute un identifiant dans l'URL, par exemple <strong>/artist.php?id=1</strong>.</p>
				<?php } else { ?>
					<div class="artist-card">
						<img src="<?php echo $artist['avatar_url']; ?>" alt="<?php echo $artist['nickname'] ?: ($artist['first_name'] . ' ' . $artist['last_name']); ?>" />
						<div>
							<h2><?php echo $artist['nickname'] ?: ($artist['first_name'] . ' ' . $artist['last_name']); ?></h2>
							<p><?php echo $artist['bio'] ?: 'Aucune description pour le moment.'; ?></p>
							<?php if ($artist['style']) { ?>
								<p><strong>Style:</strong> <?php echo $artist['style']; ?></p>
							<?php } ?>
							<ul>
								<?php
									render_social_link('Spotify', 'https://open.spotify.com/user/', $artist['spotify']);
									render_social_link('YouTube', 'https://www.youtube.com/@', $artist['youtube']);
									render_social_link('Deezer', 'https://www.deezer.com/profile/', $artist['deezer']);
									render_social_link('SoundCloud', 'https://soundcloud.com/', $artist['soundcloud']);
									render_social_link('Bandlab', 'https://www.bandlab.com/', $artist['bandlab']);
									render_social_link('Beam', 'https://beam.ejnalo.me/', $artist['beam']);
									render_social_link('Instagram', 'https://www.instagram.com/', $artist['instagram']);
								?>
							</ul>
						</div>
					</div>
				<?php } ?>
			</section>

			<section>
				<h2>Concerts</h2>
				<?php if (!$artist) { ?>
					<p>La liste des concerts s'affichera une fois l'artiste sélectionné.</p>
				<?php } elseif (!$concerts) { ?>
					<p>Aucun concert programmé pour le moment.</p>
				<?php } else { ?>
					<ul>
						<?php foreach ($concerts as $concert) { ?>
							<li class="concert">
								<div>
									<h3><?php echo $concert['name'] ?: 'Concert'; ?></h3>
									<p><strong>Scène:</strong> <?php echo $concert['scene_name']; ?></p>
									<p><strong>Horaire:</strong> <?php echo date('d/m/Y H:i', strtotime($concert['planned_at'])); ?> - <?php echo date('H:i', strtotime($concert['ends_at'])); ?></p>
								</div>
							</li>
						<?php } ?>
					</ul>
				<?php } ?>
			</section>
		</main>
	</body>
</html>
