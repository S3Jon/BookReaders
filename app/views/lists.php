<?php

require_once "../app/models/List.php";
require_once "../app/controllers/ListController.php";
require_once "../app/models/User.php";
require_once "../app/controllers/UserController.php";

session_start();

$listController = new controllers\ListController(new models\ListModel());
$userController = new controllers\UserController(new models\User());

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
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 justify-items-center gap-10 mt-10">
            <?php foreach ($listasp as $key => $list): ?>
                <div class="container">
                    <div class="bg-white shadow-md rounded-lg max-w-sm w-full">
                        <div class="p-4">
                            <form action="list" method="POST">
                                <input type="hidden" name="id_list" value="<?= $list['id_list'] ?>">
                                <button type="submit" class="underline text-lg font-semibold text-gray-900" name="submit_button"><?= $list['list_name'] ?></button>
                            </form>
							<form action="profile_list" method="POST">
								<input type="hidden" name="id_user" value="<?= $list['id_user'] ?>">
								<button type="submit" class="text-sm text-gray-600" name="submit_button"><?= $userController->getUsernameById($list['id_user']) ?></button>
							</form>
                            <p class="text-sm text-gray-600"><?= $list['visibility'] ?></p>
                            <p class="text-sm text-gray-600"><?= $list['created_at'] ?></p>
                        </div>
                        <div class="flex justify-end p-4">
                            <form action="" method="POST">
                                <input type="hidden" name="id_list" value="<?= $list['id_list'] ?>">
                                <button type="submit" name="delete" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Borrar</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include_once 'partials/footer.php'; ?>