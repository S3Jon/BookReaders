<?php

require_once '../app/controllers/UserController.php';
require_once '../app/models/User.php';
require_once '../app/controllers/ListController.php';
require_once '../app/models/List.php';
//En el futuro incluir controlador/modelo de libros

$listController = new controllers\ListController(new models\ListModel());
$userController = new controllers\UserController(new models\User());
$listaInfo = $listController->exploreLists($_POST['id_list']);

$nombreLista = isset($listaInfo['list_name']) ? $listaInfo['list_name'] : 'Error al cargar el titulo';
$propietarioLista = $userController->getUsernameById($listaInfo['id_user']);
$visibilidadLista = isset($listaInfo['visibility']) ? $listaInfo['visibility'] : 'Error al cargar la visibilidad';
session_start();

//Medida extra de seguridad: Si la lista no es pública y no tienes sesión/tu id de user no coincide, no puedes ver la lista
if ($visibilidadLista != "public")
{
	$listAccess = true;
	if (!isset($_SESSION['userData']) || $_SESSION['userData']['id_user'] !== $listaInfo['id_user'])
	{
		$listAccess = false;
	}
}
?>

<?php include_once 'partials/header.php'; ?>

<?php if ($visibilidadLista == "public" || $listAccess): ?>
	<div class="my-14 container mx-auto min-h-screen">
		<div class="w-3/4 mx-auto">
			<div class ="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 justify-items-center gap-10 mt-10">
				<div class="bg-white shadow-md rounded-lg max-w-sm w-full">
					<div class="p-4">
						<h1 class="text-lg font-semibold text-gray-900"><?= $nombreLista ?></h1>
						<p class="text-sm text-gray-600"><?= $propietarioLista ?></p>
						<p class="text-sm text-gray-600"><?= $visibilidadLista ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php else: ?>
	<p>No tienes permiso para ver esta lista</p>
<?php endif; ?>
<?php include_once 'partials/footer.php'; ?>