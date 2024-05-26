<?php

require_once '../app/controllers/UserController.php';
require_once '../app/models/User.php';

session_start();

if (!isset($_SESSION['userData'])) {
    header('Location: home');
    exit;
}

$userController = new controllers\UserController(new models\User());

$userID = $_SESSION['userData']['id_user'];
$user = $userController->readByUserID($userID);

if (isset($_POST['modify_account'])) {
	$username = $_POST['username'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	$userController->updateUser($user['id_user'], $username, $email, $password, $user['role']);
	header('Location: profile');
	exit;
}

?>

<?php include 'partials/header.php'; ?>

<div class="container mx-auto px-4 min-h-screen">
	<div class="grid grid-cols-2 mt-6">
		<div class="flex items-center">
			<a class="text-3xl font-bold text-gray-900 underline mr-2">Modificar cuenta</a>
		</div>
		<div>
			<button onclick="window.location.href='profile?id=<?= $userID ?>'" class="flex items-center justify-end gap-2 bg-red-500 text-white font-semibold px-4 py-2 rounded-md w-24">
				<span>Cancelar</span>
			</button>
		</div>
	</div>

	<form action="modify_account" method="POST" enctype="multipart/form-data">
		<div class="mt-4">
			<label for="username" class="text-lg font-semibold text-gray-900">Nombre de usuario</label>
			<input type="text" name="username" id="username" class="w-full p-2 border border-gray-300 rounded-md" value="<?= $user['username'] ?>" required>
		</div>
		<div class="mt-4">
			<label for="email" class="text-lg font-semibold text-gray-900">Correo electrónico</label>
			<input type="email" name="email" id="email" class="w-full p-2 border border-gray-300 rounded-md" value="<?= $user['email'] ?>">
		</div>
		<div class="mt-4">
			<label for="password" class="text-lg font-semibold text-gray-900">Cambiar contraseña (dejar vacio para mantener la actual)</label>
			<input type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded-md">
		</div>
		<div class="mt-4">
			<button type="submit" name="modify_account" class="px-2 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:bg-green-600">Modificar cuenta</button>
		</div>
	</form>
</div>

<?php include 'partials/footer.php'; ?>