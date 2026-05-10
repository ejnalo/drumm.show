<?php
	function gen_link() {
		global $DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME;

		$DB_HOST = 'db';
		$DB_USER = 'root';
		$DB_PASSWORD = 'root';
		$DB_NAME = 'test';

		return mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
	}

	function get_count($field, $filter = null) {
		$link = gen_link();

		$query = "SELECT COUNT(*) as count FROM $field";

		if ($filter !== null) {
			$query .= " WHERE ";
			$query .= $filter;
		}

		$result = mysqli_query($link, $query);

		$row = mysqli_fetch_assoc($result);

		return $row['count'];
	}

	function get_featured_artist() {
		$link = gen_link();

		$query = "SELECT * FROM artists LIMIT 1";
		$result = mysqli_query($link, $query);
		$row = mysqli_fetch_assoc($result);

		return $row;
	}

	function get_featured_concert($artist) {
		if (!isset($artist)) {
			return;
		}

		$link = gen_link();

		$query = "SELECT * FROM concerts WHERE artist = " . $artist['id'] . " LIMIT 1";

		$result = mysqli_query($link, $query);
		$row = mysqli_fetch_assoc($result);

		return $row;
	}

	$artist = get_featured_artist();
	$concert = get_featured_concert($artist);
?>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>DRUMM'Show: Le festival de musique pour les jeunes</title>

		<link rel="stylesheet" href="/assets/css/main.css" type="text/css" />
	</head>
	<body>
		<nav class="navbar" id="navbar-home">
			<a class="navlink active" href="/">
				<span>Accueil</span>
			</a>
			<a class="navlink" href="/programme.php">
				<span>Programme</span>
			</a>
			<a class="navlink" href="/reservation.php">
				<span>Réserver un concert</span>
			</a>
			<a class="navlink" href="/register/artist.php">
				<span>Inscription artiste</span>
			</a>
		</nav>
		<header id="header-home">
			<div id="title-container">
				<h1>DRUMM'Show: Le festival de musique pour les jeunes</h1>
				<div class="btn-row">
					<a class="button" href="/programme.php">Voir le programme</a>
					<a class="button btn-secondary" href="/register/concert.php">Réserver un horaire de concert</a>
				</div>
			</div>
			<div id="artist-container">
				<div class="artist-card">
					<?php
						echo "<img src='" . $artist['avatar_url'] . "' />";
					?>
					<div>
						<h3><?php echo $artist['nickname']; ?></h3>
						<p>
							Concert à l'affiche |
							<?php
								echo date('d F Y \d\e H:i', strtotime($concert['planned_at'])) . " à " . date('H:i', strtotime($concert['ends_at']));
							?>
						</p>
						<?php
							echo "<a href='/artist.php?id=".$artist['id']."'>Voir sa page</a>";
						?>
					</div>
				</div>
			</div>
		</header>
		<main id="content-home">
			<section id="stats">
				<h2>Le DRUMM'Show en chiffres</h2>
				<p>Voici quelques statistiques sur notre festival:</p>
				<ul class="grid">
					<li>
						<span class="bigint"><?php
							echo get_count("scenes");
						?></span>
						<p>Salles de concert</p>
					</li>
					<li>
						<span class="bigint"><?php
							echo get_count("reservations");
						?></span>
						<p>Pass demi-journée réservées</p>
					</li>
					<li>
						<span class="bigint"><?php
							echo get_count("artists");
						?></span>
						<p>Artistes </p>
					</li>
				</ul>
			</section>
			<section id="sponsors">
				<h2>Un immense remerciement à nos partenaires</h2>
				<ul class="carrousel">
					<li>
						<img src="/public/sponsors/mmiplace.svg" class="sponsor" />
					</li>
					<li>
						<img src="/public/sponsors/sum.png" class="sponsor" />
					</li>
					<li>
						<img src="/public/sponsors/RégionIDF.png" class="sponsor" />
					</li>
					<li>
						<img src="/public/sponsors/cloudflare.png" class="sponsor" />
					</li>
					<li>
						<img src="/public/sponsors/Steam.png" class="sponsor" />
					</li>
					<li>
						<img src="/public/sponsors/creditmutuel.svg" class="sponsor" />
					</li>
				</ul>
			</section>
		</main>
	</body>
</html>
