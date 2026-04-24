<?php
	function enregistrer_artiste(
		$nom,
		$prenom,
		$mail,
		$nom_scene,
		$bio,
		$avatar_url,
		$style,
		$spotify,
		$youtube,
		$deezer,
		$soundcloud,
		$bandlab,
		$beam,
		$instagram
	) {
		$link = mysqli_connect(
			'db',
			'root',
			'root',
			'test'
		);

		$query = mysqli_query(
			$link,
			"INSERT INTO artists (last_name, first_name, email, nickname, bio, avatar_url, style, spotify, youtube, deezer, soundcloud, bandlab, beam, instagram)
			VALUES (\"$nom\", \"$prenom\", '$mail', \"$nom_scene\", \"$bio\", '$avatar_url', \"$style\", '$spotify', '$youtube', '$deezer', '$soundcloud', '$bandlab', '$beam', '$instagram')"
		);

		if ($query) {
			echo "<main>
				<section>
					<h1>Votre compte a été enregistré !</h1>
					<a href='/'>Cliquez pour retourner à l'accueil</a>
				</section>
			</main>";

			header("Location: /");
			exit;
		} else {
			echo "<main>
				<section>
					<h1>Impossible d'enregistrer votre compte.</h1>
					<a href='/'>Cliquez pour retourner à l'accueil</a>
				</section>
			</main>";

			die(mysqli_error($link));
		}
	}
?>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Inscription</title>

		<link rel="stylesheet" href="/assets/css/main.css" type="text/css" />
	</head>
	<body>
		<nav class="navbar">
			<a class="navlink" href="/">
				<span>Accueil</span>
			</a>
			<a class="navlink" href="/festival.php">
				<span>Festival</span>
			</a>
			<a class="navlink active" href="/inscription.php">
				<span>Inscription</span>
			</a>
			<a class="navlink" href="/inscription.php">
				<span>Réservation</span>
			</a>
		</nav>
		<?php
			echo "<header>";

			$valid = true;
			$message = "<h1>Impossible de créer le compte.</h1><ul>";

			if (isset($_POST['prenom'])) {
				$prenom = $_POST['prenom'];
			} else {
				$prenom = '';
				$valid = false;
				$message .= "<li>Le prénom doit être spécifié</li>";
			}

			if (isset($_POST['nom'])) {
				$nom = $_POST['nom'];
			} else {
				$nom = '';
				$valid = false;
				$message .= "<li>Le nom de famille doit être spécifié</li>";
			}

			if (isset($_POST['mail'])) {
				$mail = $_POST['mail'];
			} else {
				$mail = '';
			}

			if (isset($_POST['nickname'])) {
				$nom_scene = $_POST['nickname'];
			} else {
				$nom_scene = $prenom . " " . $nom;
			}

			if (isset($_POST['avatar_url'])) {
				$avatar_url = $_POST['avatar_url'];
			} else {
				$avatar_url = 'https://api.beam.ejnalo.me/users/beam/avatar.png';
			}

			if (isset($_POST['bio'])) {
				$bio = $_POST['bio'];
			} else {
				$bio = '';
			}

			if (isset($_POST['style'])) {
				$style = $_POST['style'];
			} else {
				$style = '';
			}


			if (isset($_POST['spotify'])) {
				$spotify = $_POST['spotify'];
			} else {
				$spotify = '';
			}

			if (isset($_POST['youtube'])) {
				$youtube = $_POST['youtube'];
			} else {
				$youtube = '';
			}

			if (isset($_POST['deezer'])) {
				$deezer = $_POST['deezer'];
			} else {
				$deezer = '';
			}

			if (isset($_POST['soundcloud'])) {
				$soundcloud = $_POST['soundcloud'];
			} else {
				$soundcloud = '';
			}

			if (isset($_POST['bandlab'])) {
				$bandlab = $_POST['bandlab'];
			} else {
				$bandlab = '';
			}

			if (isset($_POST['beam'])) {
				$beam = $_POST['beam'];
			} else {
				$beam = '';
			}

			if (isset($_POST['instagram'])) {
				$instagram = $_POST['instagram'];
			} else {
				$instagram = '';
			}


			if ($valid) {
				enregistrer_artiste(
					$nom,
					$prenom,
					$mail,
					$nom_scene,
					$bio,
					$avatar_url,
					$style,
					$spotify,
					$youtube,
					$deezer,
					$soundcloud,
					$bandlab,
					$beam,
					$instagram
				);
			} else {
				echo "<main><section>" . $message . "</ul></section></main>";
			}
		?>
	</body>
</html>
