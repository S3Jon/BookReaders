<?php

session_start();

require_once '../app/controllers/ListController.php';
require_once '../app/models/List.php';

$listController = new controllers\ListController(new models\ListModel());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['edit_list'])) {
		$id_list = $_POST['id_list'];

		$list = $listController->exploreLists($id_list);

	} else if (isset($_POST['update_list'])) {
		$id_list = $_POST['id_list'];
		$list_name = $_POST['list_name'];
		$visibility = $_POST['visibility'];

		if ($listController->updateList($id_list, $list_name, $visibility)) {
			echo "Lista actualizada con éxito.";
			header("Location: lists");
			exit;
		} else {
			echo "Error al actualizar la lista.";
		}

	}
}

// Sec list ownership
if ($list['id_user'] != $_SESSION['user_id']) {
	header('Location: lists');
	exit;
}

?>

<?php include_once 'partials/header.php'; ?>

<?php include_once 'partials/footer.php'; ?>