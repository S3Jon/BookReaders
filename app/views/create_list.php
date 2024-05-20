<?php

session_start();

require_once "../app/models/User.php";
require_once "../app/controllers/UserController.php";
require_once "../app/models/List.php";
require_once "../app/controllers/ListController.php";

$user = new models\User();
$list = new models\ListModel();
$userController = new controllers\UserController($user);
$listController = new controllers\ListController($list);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['create'])) {
		$listController->createList($_SESSION['user_id'], $_POST['list_name'], $_POST['list_description'], $_POST['visibility']);
	}
}
?>

<?php include 'header.php'; ?>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1>Crear lista</h1>
			<form action="" method="post">
				<div class="form-group">
					<label for="list_name">Nombre de la lista</label>
					<input type="text" class="form-control" id="list_name" name="list_name" required>
				</div>
				
				<div class="form-group">
					<label for="visibility">Visibilidad</label>
					<select class="form-control" id="visibility" name="visibility">
						<option value="1">Pública</option>
						<option value="0">Privada</option>
					</select>
				</div>
				<button type="submit" class="btn btn-primary" name="create">Crear lista</button>
			</form>
		</div>
	</div>
</div>

<?php include 'footer.php'; ?>