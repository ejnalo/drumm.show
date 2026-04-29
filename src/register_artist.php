<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>DRUMM'Show - Inscription Artiste</title>

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
		<header>
			<h1>Inscription Artiste</h1>
		</header>
		<form action="/callback/inscription.php" method="POST">
			<main>
				<section>
					<h2>Infos de communication</h2>
					<p>Nous gardons ces informations pour te communiquer en cas d'imprévu, de report, d'annulation ou pour te dénoncer à l'URSSAF.</p>
					<div class="form-row">
						<label for="prenom">
							<span>Ton prénom*</span>
							<input type="text" name="prenom" placeholder="John" required />
						</label>
						<label for="nom">
							<span>Ton nom*</span>
							<input type="text" name="nom" placeholder="DOE" required />
						</label>
					</div>
					<label for="mail">
						<span>Adresse mail (optionnel)</span>
						<input type="email" name="mail" placeholder="exemple@drum.ms" />
					</label>
					<h2>Profil d'artiste</h2>
					<div class="form-row">
						<label for="nickname">
							<span>Nom d'artiste (optionnel)</span>
							<input type="text" name="nickname" placeholder="MAÎTRE GRIS" />
						</label>
						<label for="avatar_url">
							<span>Lien de ton avatar (optionnel)</span>
							<input type="url" name="avatar_url" placeholder="https://" value="https://api.beam.ejnalo.me/users/beam/avatar.png" id="avatar_url" required />
						</label>
					</div>
					<label for="style">
						<span>Style (optionnel)</span>
						<input type="text" name="style" placeholder="Pop, Rock..." />
					</label>
					<label for="bio">
						<span>Description (optionnel)</span>
						<textarea name="bio" placeholder="Description"></textarea>
					</label>
					<h2>Profils sociaux</h2>
					<p>Pour chaque profil, veuillez entrer le nom de votre chaîne (celui qui apparaît dans l'URL) sans inclure le @ du début.</p>
					<div class="form-row">
						<label for="spotify">
							<span>Compte Spotify (optionnel)</span>
							<input type="text" name="spotify" placeholder="spotify_user" />
						</label>
						<label for="youtube">
							<span>Chaîne Youtube (optionnel)</span>
							<input type="text" name="youtube" placeholder="youtube_user" />
						</label>
						<label for="deezer">
							<span>Compte Deezer (optionnel)</span>
							<input type="text" name="deezer" placeholder="deezer_user" />
						</label>
					</div>
					<div class="form-row">
						<label for="soundcloud">
							<span>Compte Soundcloud (optionnel)</span>
							<input type="text" name="soundcloud" placeholder="soundcloud_user" />
						</label>
						<label for="bandlab">
							<span>Profil Bandlab (optionnel)</span>
							<input type="text" name="bandlab" placeholder="bandlab_user" />
						</label>
						<label for="beam">
							<span>Profil Beam (optionnel)</span>
							<input type="text" name="beam" placeholder="beamer_123" />
						</label>
						<label for="instagram">
							<span>Profil Instagram (optionnel)</span>
							<input type="text" name="instagram" placeholder="instagram_user" />
						</label>
					</div>
					<input type="submit" value="Suivant" />
				</section>
			</main>
		</form>
	</body>
</html>
