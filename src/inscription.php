<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>DRUMM'Show: Inscription</title>

		<link rel="stylesheet" href="/assets/css/main.css" type="text/css" />

		<script>
			function checkStatut() {
				let val = document.querySelector('#status').value;
				let age = document.querySelector('#age').value;
				let container = document.querySelector('#statusDiv');
				let label = document.querySelector('#studentNbLabel');

				switch (val) {
					case 'etudiant':
						if (age && age <= 26) {
							let span = document.createElement('span');
							span.innerText = "Ton numéro étudiant";

							let input = document.createElement('input');
							input.type = 'text';
							input.placeholder = 'XXXXXXXX';
							input.name = 'student_number';

							if (!label) {
								label = document.createElement('label');
								label.htmlFor = input.type;
								label.id = "StudentNbLabel";

								label.appendChild(span);
								label.appendChild(input);

								container.appendChild(label);
							}
						} else {
							if (label) label.remove();
						}

						break;

					default:
						if (label) label.remove();
						break;
				}
			}
		</script>
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
		<header>
			<h1>Inscription</h1>
		</header>
		<form action="/callback/visiteurs.php" method="POST">
			<main>
				<section>
					<h2>Crée-toi un compte</h2>
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
					<div class="form-row" id="statusDiv">
						<label for="age">
							<span>Ton âge*</span>
							<input type="number" name="age" placeholder="19" id="age" required onchange="checkStatut()" />
						</label>
						<label for="status">
							<span>Ton statut*</span>
							<select name="status" id="status" required onchange="checkStatut()">
								<option value="normal" selected>Adulte</option>
								<option value="etudiant">Étudiant (-26 ans)</option>
								<option value="artist">Artiste</option>
							</select>
						</label>
					</div>
					<label for="mail">
						<span>Adresse mail*</span>
						<input type="email" name="mail" placeholder="exemple@drum.ms" required />
					</label>
					<label for="tel">
						<span>Numéro de téléphone (optionnel)</span>
						<input type="phone" name="tel" placeholder="+33 1 23 45 67 89" required />
					</label>
					<label for="address">
						<span>Adresse postale (optionnal)</span>
						<input type="text" name="address" placeholder="2 rue Le Dantec, 75013 Paris" required />
					</label>
					<input type="submit" value="Suivant" />
				</section>
			</main>
		</form>
	</body>
</html>
