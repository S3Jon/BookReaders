<?php

require_once "../app/models/List.php";
require_once "../app/controllers/ListController.php";
require_once "../app/models/User.php";
require_once "../app/controllers/UserController.php";
//TODO- Actualizar fuente de BooksInList
require_once "../app/models/test_bookinlist.php";

session_start();

$listController = new controllers\ListController(new models\ListModel());
$userController = new controllers\UserController(new models\User());
//TODO- Actualizar fuente de BooksInList
$BILController = new models\BILTest();

$listasp = $listController->getPublicLists();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $listController->deleteList($_POST['id_list']);
    }
    if (isset($_POST['create'])) {
        $listController->createList($_SESSION['user_id'], $_POST['list_name'], $_POST['visibility']);
    }
}

?>

<?php include_once 'partials/header.php'; ?>

<div class="my-14 container mx-auto min-h-screen">
    <div class="w-3/4 mx-auto">
        <div class="grid grid-cols-2 justify-items-center gap-10 mt-10">
            <?php foreach ($listasp as $key => $list): ?>
                <div class="container">
                    <div class="bg-[rgba(36,38,51,0.15)] shadow-md rounded-lg w-full">
						<div class="flex fex-col gap-1">
							<div class="justify-end my-4 ml-4">
								<img src="img/fk_placeholder.png" alt="book" class="w-14 h-14" >
							</div>
							<div class="p-4">
								<form action="list" method="POST">
									<input type="hidden" name="id_list" value="<?= $list['id_list'] ?>">
									<button type="submit" class="text-lg font-extrabold text-gray-900" name="submit_button"><?= $list['list_name'] ?></button>
								</form>
								<div>
									<div class="flex items-center gap-2 my-3 ml-1">
										<img src="img/users.svg" alt="user" class="w-4 h-4">
										<form action="profile_list" method="POST">
											<input type="hidden" name="id_user" value="<?= $list['id_user'] ?>">
											<button type="submit" class="text-sm text-black font-semibold" name="submit_button"><?= $userController->getUsernameById($list['id_user']) ?></button>
										</form>
									</div>
									<div class="flex items-center gap-2 my-3 ml-1">
										<img src="img/followers.svg" alt="followers" class="w-4 h-4">
										<p class="text-sm text-black font-semibold"># Seguidores</p> <!-- TODO: Implementar seguidores -->	
									</div>
									<div class="flex items-center gap-2 my-3 ml-1">
										<img src="img/bookStack.svg" alt="bils" class="w-4 h-4">
										<p class="text-sm text-black font-semibold"><?= implode($BILController->getBILCount($list['id_list'])) ?></p>
									</div>
									<p class="text-sm text-black">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus vel magna eu arcu commodo ...</p>
								</div>
							</div>
						</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include_once 'partials/footer.php'; ?>