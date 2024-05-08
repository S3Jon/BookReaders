<?php

require_once '../app/controllers/UserController.php';
require_once '../app/models/User.php';
require_once '../app/controllers/ListController.php';
require_once '../app/models/List.php';
//TODO: Modificar en el futuro;  fase test
require_once '../app/models/test_book.php';
require_once '../app/models/BookInList.php';
//En el futuro incluir controlador/modelo de libros

$listController = new controllers\ListController(new models\ListModel());
$userController = new controllers\UserController(new models\User());
$bookController = new models\Booktest();
$BILController = new models\BILModel();
$listaInfo = $listController->exploreLists($_POST['id_list']);
$BILInfo = $BILController->getlistbooks($_POST['id_list']);


$nombreLista = isset($listaInfo['list_name']) ? $listaInfo['list_name'] : 'Error al cargar el titulo';
$propietarioLista = $userController->getUsernameById($listaInfo['id_user']);
$visibilidadLista = isset($listaInfo['visibility']) ? $listaInfo['visibility'] : 'Error al cargar la visibilidad';
session_start();

//Medida extra de seguridad: Si la lista no es pública y no tienes sesión/tu id de user no coincide, no puedes ver la lista
$listOS = false; //List OwnerShip
if (isset($_SESSION['userData']) && ($_SESSION['userData']['id_user'] == $listaInfo['id_user']))
{
	$listOS = true;
}

?>

<?php include_once 'partials/header.php'; ?>

<?php if ($visibilidadLista != "public" && !$listOS) { include 'list_partials/list_forbidden.php'; }
else { ?>
	<div class="my-14 container mx-auto min-h-screen">
		<div class="w-3/4 mx-auto">
			<div class="flex justify-between">
				<div class="w=2/6">
					<h1 class="text-3xl font-bold text-gray-900">Explorar listas creadas por la comunidad</h1>
				</div>
				<!-- absolutamente robado parte 365 -->
				<div class="w-2/3 md:mx-auto">
					<div class="flex items-center w-full gap-20">
						<form class="flex items-center gap-5 w-full pb-1 border-b-2 border-primary">
							<input type="text" class="w-full py-2 bg-transparent outline-none"
							placeholder=" Busca libro"> <!--lol-->
							<button type="submit"><img src="img/lupa.svg" alt=""></button>
						</form>
					</div>
				</div>
			</div>
			<div class="grid grid-cols-2 gap-10 mt-10">
				<div>
					<div class="bg-white shadow-md rounded-lg max-w-sm w-full">
						<div class="p-4">
							<?php include 'list_partials/list_info.php'; ?>
						</div>
					</div>
				</div>
				<div>
					<div class ="grid grid-cols-4 justify-items-center gap-25 mt-10">
						<?php foreach ($BILInfo as $key => $book): ?>
							<div class="container w-40">
								<div class="bg-white shadow-md rounded-lg max-w-sm w-full">
									<div class="p-4">
										<form action="book" method="POST">
											<input type="hidden" name="id_book" value="<?= $book['isbn'] ?>">
											<button type="submit" class="underline text-lg font-semibold text-gray-900" name="submit_button"><?= implode($bookController->getBookTitle($book['isbn'])) ?></button>
										</form>
										<p class="text-sm text-gray-600"><?= implode($bookController->getBookAuthor($book['isbn'])) ?></p>
										<p class="text-sm text-gray-600"><?= implode($bookController->getBookGenre($book['isbn'])) ?></p>
										//eliminar libro de la lista 
										<?php if ($listOS): ?>
											<form action="delete_book" method="POST">
												<input type="hidden" name="id_list" value="<?= $_POST['id_list'] ?>">
												<input type="hidden" name="isbn" value="<?= $book['isbn'] ?>">
												<button type="submit" class="underline text-sm text-gray-600">Eliminar libro</button>
											</form>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

<?php include_once 'partials/footer.php'; ?>