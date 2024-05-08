<h1 class="text-lg font-semibold text-gray-900"><?= $nombreLista ?></h1>
							<p class="text-sm text-gray-600">Propietario: <?= $propietarioLista ?></p>
							<p class="text-sm text-gray-600">Visibilidad: <?= $visibilidadLista ?></p>
							<?php if ($listOS): ?>
								<form action="edit_list" method="POST">
									<input type="hidden" name="id_list" value="<?= $_POST['id_list'] ?>">
									<button type="submit" class="underline text-sm text-gray-600">Editar lista</button>
								</form>
							<?php endif; ?>