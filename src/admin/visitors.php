<?php
	require_once __DIR__ . '/_auth.php';
	require_admin();
	require_once __DIR__ . '/_db.php';

	$link = gen_link();
	$message = null;
	$edit_visitor = null;

	function fetch_visitors($link) {
		$result = mysqli_query($link, 'SELECT * FROM visitors ORDER BY created_at DESC');

		if (!$result) {
			return [];
		}

		$visitors = [];
		while ($visitor = mysqli_fetch_assoc($result)) {
			$visitors[] = $visitor;
		}

		return $visitors;
	}

	function fetch_visitor($link, $id) {
		$id = mysqli_real_escape_string($link, $id);
		$result = mysqli_query($link, "SELECT * FROM visitors WHERE id = '$id' LIMIT 1");

		if (!$result) {
			return null;
		}

		return mysqli_fetch_assoc($result);
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$action = $_POST['action'] ?? '';

		if ($action === 'create' || $action === 'update') {
			$first_name = clean_value($link, 'first_name');
			$last_name = clean_value($link, 'last_name');
			$age_raw = clean_value($link, 'age');
			$mail = clean_value($link, 'mail');
			$status = clean_value($link, 'status', false, 'normal');
			$student_number = clean_value($link, 'student_number', false, '');
			$tel = clean_value($link, 'tel', false, '');
			$address = clean_value($link, 'address', false, '');

			if ($status === '') {
				$status = mysqli_real_escape_string($link, 'normal');
			}

			if (!$first_name || !$last_name || !$age_raw || !$mail) {
				$message = 'Tous les champs obligatoires doivent etre remplis.';
			} else {
				$age = $age_raw;

				if ($action === 'create') {
					$query = "INSERT INTO visitors (first_name, last_name, age, mail, status, student_number, tel, address)
						VALUES ('$first_name', '$last_name', '$age', '$mail', '$status', " . sql_optional($student_number) . ", " . sql_optional($tel) . ", " . sql_optional($address) . ")";

					if (mysqli_query($link, $query)) {
						$message = 'Visiteur cree.';
					} else {
						$message = 'Erreur: ' . mysqli_error($link);
					}
				} else {
					$id = isset($_POST['id']) ? $_POST['id'] : '';

					if ($id === '') {
						$message = 'ID invalide.';
					} else {
						$id = mysqli_real_escape_string($link, $id);
						$query = "UPDATE visitors
							SET first_name = '$first_name',
								last_name = '$last_name',
								age = '$age',
								mail = '$mail',
								status = '$status',
								student_number = " . sql_optional($student_number) . ",
								tel = " . sql_optional($tel) . ",
								address = " . sql_optional($address) . "
							WHERE id = '$id'
							LIMIT 1";

						if (mysqli_query($link, $query)) {
							$message = 'Visiteur mis a jour.';
						} else {
							$message = 'Erreur: ' . mysqli_error($link);
						}
					}
				}
			}
		} elseif ($action === 'delete') {
			$id = isset($_POST['id']) ? $_POST['id'] : '';

			if ($id === '') {
				$message = 'ID invalide.';
			} else {
				$id = mysqli_real_escape_string($link, $id);
				if (mysqli_query($link, "DELETE FROM visitors WHERE id = '$id' LIMIT 1")) {
					$message = 'Visiteur supprime.';
				} else {
					$message = 'Erreur: ' . mysqli_error($link);
				}
			}
		}
	}

	$edit_id = isset($_GET['id']) ? $_GET['id'] : '';
	if ($edit_id !== '') {
		$edit_visitor = fetch_visitor($link, $edit_id);

		if (!$edit_visitor) {
			$message = 'Visiteur introuvable.';
		}
	}

	$visitors = fetch_visitors($link);
	$form_title = $edit_visitor ? 'Modifier un visiteur' : 'Ajouter un visiteur';
	$form_action = $edit_visitor ? 'update' : 'create';
	$submit_label = $edit_visitor ? 'Mettre a jour' : 'Ajouter';
?>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>DRUMM'Show - Admin Visiteurs</title>

		<link rel="stylesheet" href="/assets/css/main.css" type="text/css" />
		<link rel="stylesheet" href="/assets/css/admin.css" type="text/css" />
	</head>
	<body>
		<nav class="navbar admin-nav">
			<a class="navlink" href="/admin/index.php?<?php echo admin_params(); ?>">
				<span>Dashboard</span>
			</a>
			<a class="navlink" href="/admin/artists.php?<?php echo admin_params(); ?>">
				<span>Artistes</span>
			</a>
			<a class="navlink" href="/admin/concerts.php?<?php echo admin_params(); ?>">
				<span>Concerts</span>
			</a>
			<a class="navlink" href="/admin/reservations.php?<?php echo admin_params(); ?>">
				<span>Reservations</span>
			</a>
			<a class="navlink active" href="/admin/visitors.php?<?php echo admin_params(); ?>">
				<span>Visiteurs</span>
			</a>
			<a class="navlink" href="/admin/logout.php">
				<span>Deconnexion</span>
			</a>
		</nav>
		<header>
			<h1>Visiteurs</h1>
			<p>Gerer les comptes visiteurs et leur statut.</p>
		</header>
		<main>
			<section class="admin-section">
				<?php if ($message) { ?>
					<p class="admin-message"><?php echo escape_html($message); ?></p>
				<?php } ?>

				<h2><?php echo $form_title; ?></h2>
				<form class="admin-form" method="POST" action="/admin/visitors.php?<?php echo admin_params(); ?>">
					<input type="hidden" name="action" value="<?php echo $form_action; ?>" />
					<?php if ($edit_visitor) { ?>
						<input type="hidden" name="id" value="<?php echo escape_html($edit_visitor['id']); ?>" />
					<?php } ?>
					<div class="form-row">
						<label for="first_name">
							<span>Prenom*</span>
							<input type="text" name="first_name" id="first_name" value="<?php echo escape_html($edit_visitor['first_name'] ?? ''); ?>" required />
						</label>
						<label for="last_name">
							<span>Nom*</span>
							<input type="text" name="last_name" id="last_name" value="<?php echo escape_html($edit_visitor['last_name'] ?? ''); ?>" required />
						</label>
						<label for="age">
							<span>Age*</span>
							<input type="number" name="age" id="age" min="0" value="<?php echo escape_html($edit_visitor['age'] ?? ''); ?>" required />
						</label>
					</div>
					<div class="form-row">
						<label for="mail">
							<span>Email*</span>
							<input type="email" name="mail" id="mail" value="<?php echo escape_html($edit_visitor['mail'] ?? ''); ?>" required />
						</label>
						<label for="tel">
							<span>Telephone</span>
							<input type="text" name="tel" id="tel" value="<?php echo escape_html($edit_visitor['tel'] ?? ''); ?>" />
						</label>
						<label for="status">
							<span>Statut</span>
							<input type="text" name="status" id="status" value="<?php echo escape_html($edit_visitor['status'] ?? 'normal'); ?>" />
						</label>
					</div>
					<div class="form-row">
						<label for="student_number">
							<span>Numero etudiant</span>
							<input type="text" name="student_number" id="student_number" value="<?php echo escape_html($edit_visitor['student_number'] ?? ''); ?>" />
						</label>
						<label for="address">
							<span>Adresse</span>
							<input type="text" name="address" id="address" value="<?php echo escape_html($edit_visitor['address'] ?? ''); ?>" />
						</label>
					</div>
					<div class="admin-actions">
						<input type="submit" value="<?php echo $submit_label; ?>" />
						<?php if ($edit_visitor) { ?>
							<a class="button btn-secondary" href="/admin/visitors.php?<?php echo admin_params(); ?>">Annuler</a>
						<?php } ?>
					</div>
				</form>

				<h2>Liste des visiteurs</h2>
				<?php if (!$visitors) { ?>
					<p>Aucun visiteur pour le moment.</p>
				<?php } else { ?>
					<table class="admin-table">
						<thead>
							<tr>
								<th>Visiteur</th>
								<th>Contact</th>
								<th>Statut</th>
								<th>Ajoute le</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($visitors as $visitor) {
								$name = $visitor['first_name'] . ' ' . $visitor['last_name'];
								$contact = $visitor['mail'];
								if ($visitor['tel']) {
									$contact .= ' - ' . $visitor['tel'];
								}
								$created_at = $visitor['created_at'] ? date('d/m/Y', strtotime($visitor['created_at'])) : '';
							?>
							<tr>
								<td><?php echo escape_html($name); ?></td>
								<td><?php echo escape_html($contact); ?></td>
								<td><?php echo escape_html($visitor['status']); ?></td>
								<td><?php echo escape_html($created_at); ?></td>
								<td>
									<div class="admin-actions">
										<a class="button" href="/admin/visitors.php?id=<?php echo escape_html($visitor['id']); ?>&<?php echo admin_params(); ?>">Editer</a>
										<form class="admin-inline" method="POST" action="/admin/visitors.php?<?php echo admin_params(); ?>" onsubmit="return confirm('Supprimer ce visiteur ?');">
											<input type="hidden" name="action" value="delete" />
											<input type="hidden" name="id" value="<?php echo escape_html($visitor['id']); ?>" />
											<input type="submit" value="Supprimer" />
										</form>
									</div>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				<?php } ?>
			</section>
		</main>
	</body>
</html>
