<h1 class="text-lg font-semibold text-gray-900"><?= $nombreLista ?></h1>
	<p class="text-sm text-gray-600">Propietario: <?= $propietarioLista ?></p>
	<p class="text-sm text-gray-600">Visibilidad: <?= $visibilidadLista ?></p>
	<?php if ($listOS): ?>
		<form action="edit_list" method="POST">
			<input type="hidden" name="id_list" value="<?= $_POST['id_list'] ?>">
			<button type="submit" class="underline text-sm text-gray-600">Editar lista</button>
		</form>
	<?php else: ?>
		<?php if (!isset($_SESSION['userData'])): ?>
			<button class="px-2 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:bg-green-600" onclick="window.location.href = 'login';">Seguir lista</button>
		<?php else: ?>
			<?php if ($UFLController->isFollowing($_SESSION['userData']['id_user'], $_POST['id_list'])): ?>
				<form action="list" method="POST">
					<input type="hidden" name="id_list" value="<?= $_POST['id_list'] ?>">
					<button type="submit" name="unfollow_list" class="px-2 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:bg-red-600">Dejar de seguir</button>
				</form>
			<?php else: ?>
				<form action="list" method="POST">
					<input type="hidden" name="id_list" value="<?= $_POST['id_list'] ?>">
					<button type="submit" name="follow_list" class="px-2 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:bg-green-600">Seguir lista</button>
				</form>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
