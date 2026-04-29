<?php
	function gen_link() {
		global $DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME;

		$DB_HOST = 'db';
		$DB_USER = 'root';
		$DB_PASSWORD = 'root';
		$DB_NAME = 'test';

		return mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
	}

	function enregistrer_utilisateur(
		$prenom,
		$nom,
		$age,
		$mail,
		$status,
		$student_number,
		$tel,
		$address
	) {
		$link = gen_link();

		$query = mysqli_query(
			$link,
			"INSERT INTO visitors (first_name, last_name, age, mail, status, student_number, tel, address)
			VALUES ('$prenom', '$nom', '$age', '$mail', '$status', '$student_number', '$tel', '$address');"
		) or die(mysqli_error($link));

		$result = mysqli_query(
			$link,
			"SELECT * FROM visitors WHERE mail = '". $mail ."';"
		) or die(mysqli_error($link));

		if ($result) {
			echo "
				<h1>Votre compte a été enregistré !</h1>
				<a href='/'>Cliquez pour retourner à l'accueil</a>
			</header>";

			return mysqli_fetch_assoc($result);
		} else {
			echo "
				<h1>Impossible d'enregistrer votre compte.</h1>
				<a href='/'>Cliquez pour retourner à l'accueil</a>
			</header>";

			die(mysqli_error($link));
		}
	}

	function callback($utilisateur) {
		echo "<input id='userStatusIndicator' value='".$utilisateur['status']."' disabled />";
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
			<a class="navlink" href="/programme.php">
				<span>Programme</span>
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

			if (isset($_POST['age'])) {
				$age = $_POST['age'];
			} else {
				$age = '';
				$valid = false;
				$message .= "<li>L'âge doit être spécifié</li>";
			}

			if (isset($_POST['mail'])) {
				$mail = $_POST['mail'];
			} else {
				$mail = '';
				$valid = false;
				$message .= "<li>L'email doit être spécifié</li>";
			}

			if (isset($_POST['status'])) {
				$status = $_POST['status'];
			} else {
				$status = 'normal';
			}

			if (isset($_POST['student_number'])) {
				$student_number = $_POST['student_number'];
			} else {
				$student_number = NULL;
			}

			if (isset($_POST['tel'])) {
				$tel = $_POST['tel'];
			} else {
				$tel = NULL;
			}

			if (isset($_POST['address'])) {
				$address = $_POST['address'];
			} else {
				$address = NULL;
			}

			if ($valid) {
				$utilisateur = enregistrer_utilisateur(
					$prenom,
					$nom,
					$age,
					$mail,
					$status,
					$student_number,
					$tel,
					$address
				);

				echo "</header>";

				callback($utilisateur);
			} else {
				echo $message . "</header>";
			}
		?>
		<form action="/callback/ajouter_artiste.php" method="POST" id="artistRegForm" style="display: none;">
			<main>
				<section>
					<h2>Présente-toi au grand public</h2>
					<img src="https://api.beam.ejnalo.me/users/beam/avatar.png" id="avatar" height="256" />
					<div class="form-row">
						<label for="compte_id">
							<span>ID de compte</span>
							<?php
								echo '<input type="text" name="compte_id" value="'.$utilisateur['id'].'" readonly />';
							?>
						</label>
						<label for="nom_scene">
							<span>Nom de scène</span>
							<input type="text" name="nom_scene" placeholder="MAÎTRE GRIS" required />
						</label>
					</div>
					<label for="avatar_url">
						<span>Lien de votre avatar</span>
						<input type="url" id="avatarUrl" name="avatar_url" placeholder="https://" value="https://api.beam.ejnalo.me/users/beam/avatar.png" required onchange="update_avatar()" />
					</label>
					<label for="style">
						<span>Style (séparé par des virgules)</span>
						<input type="phone" name="tel" placeholder="Rock, Pop, Rap, Incantations..." />
					</label>
					<label for="bio">
						<span>Description</span>
						<textarea name="bio" placeholder="C'est moi wsh"></textarea>
					</label>
					<input type="submit" value="Valider" />
				</section>
			</main>
		</form>

		<script>
			window.onload = function () {
				const indicator = document.querySelector('#userStatusIndicator');

				switch (indicator.value) {
					case 'artist':
						document.querySelector("#artistRegForm").style.display = 'block';
				}

				indicator.remove();
			}

			function update_avatar() {
				let url = document.querySelector('#avatarUrl');
				let img = document.querySelector('#avatar');

				img.src = url.value
			}
		</script>
	</body>
</html>
