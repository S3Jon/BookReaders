<?php

require_once '../app/controllers/UserController.php';
require_once '../app/models/User.php';
require_once '../app/controllers/ListController.php';
require_once '../app/models/List.php';
//TODO: Modificar en el futuro;  fase test
require_once '../app/models/test_book.php';
require_once '../app/models/test_bookinlist.php';
//En el futuro incluir controlador/modelo de libros

$listController = new controllers\ListController(new models\ListModel());
$userController = new controllers\UserController(new models\User());
$bookController = new models\Booktest();
$BILController = new models\BILtest();
$listaInfo = $listController->exploreLists($_POST['id_list']);
$BILInfo = $BILController->getlistbooks($_POST['id_list']);


$nombreLista = isset($listaInfo['list_name']) ? $listaInfo['list_name'] : 'Error al cargar el titulo';
$propietarioLista = $userController->getUsernameById($listaInfo['id_user']);
$visibilidadLista = isset($listaInfo['visibility']) ? $listaInfo['visibility'] : 'Error al cargar la visibilidad';
session_start();

//Medida extra de seguridad: Si la lista no es pública y no tienes sesión/tu id de user no coincide, no puedes ver la lista
$listAccess = true;
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
			<div class ="grid grid-cols-4 justify-items-center gap-10 mt-10">
				<?php foreach ($BILInfo as $key => $book): ?>
					<div class="container">
						<div class="bg-white shadow-md rounded-lg max-w-sm w-full">
							<div class="p-4">
								<form action="book" method="POST">
									<input type="hidden" name="id_book" value="<?= $book['isbn'] ?>">
									<button type="submit" class="underline text-lg font-semibold text-gray-900" name="submit_button"><?= implode($bookController->getBookTitle($book['isbn'])) ?></button>
								</form>
								<p class="text-sm text-gray-600"><?= implode($bookController->getBookAuthor($book['isbn'])) ?></p>
								<p class="text-sm text-gray-600"><?= implode($bookController->getBookGenre($book['isbn'])) ?></p>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
<?php else: ?>
	<p>No tienes permiso para ver esta lista</p>
<?php endif; ?>
<?php include_once 'partials/footer.php'; ?>