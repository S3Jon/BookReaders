<?php
session_start();
require_once "../app/models/User.php";
require_once "../app/controllers/UserController.php";
require_once "../app/models/Book.php";
require_once "../app/models/List.php";
require_once "../app/controllers/ListController.php";
require_once "../app/controllers/BookController.php";
require_once "../app/models/UserFollowLists.php";
require_once "../app/models/BookInList.php";
require_once "../app/models/test_book.php";
require_once "../app/models/Followers.php";

$user = new models\User();
$book = new models\Book();
$list = new models\ListModel();
$bookController = new controllers\BookController($book);
$userController = new controllers\UserController($user);
$listController = new controllers\ListController($list);
$UFLController = new models\UFLModel();
$BILController = new models\BILModel();
$testbookCon = new models\Booktest();
$UFUController = new models\Followersmodel();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $user_ID = $_GET['id'];
        
		$FLInfo = [];
        $userInfo = $userController->readByUserId($user_ID);
        $pLists = $listController->getUserBasicLists($user_ID);
        $createdLists = $listController->getUserTopLists($user_ID);
        $followedLists = $UFLController->getFollowedLists($user_ID);
        $followedUsers = $UFUController->getFollowedUsers($user_ID);
        $followers = $UFUController->getFollowers($user_ID);
        //$reviews = $review->getPublicReviewsByUser($user_ID); //A futuro si los usuarios son mutuals igual poner getReviewsByUser ya que devuelve todas (no solo public)
		$reviews = [
			[
				'book_title' => 'The Girl He Never Noticed',
				'id_review' => 1,
				'id_user' => 123,
				'isbn' => 'BRS123456789',
				'rating' => 5,
				'comment' => 'An amazing read! The characters were well developed and the plot was thrilling from start to finish.',
				'visibility' => 'public',
				'created_at' => '2024-05-20 10:00:00',
				'updated_at' => '2024-05-20 10:00:00',
			],
			[
				'book_title' => 'A different virus: Heartfire',
				'id_review' => 2,
				'id_user' => 124,
				'isbn' => 'BRS234567890',
				'rating' => 4,
				'comment' => 'Great book with a few minor flaws. Overall, a very enjoyable experience.',
				'visibility' => 'public',
				'created_at' => '2024-05-18 15:30:00',
				'updated_at' => '2024-05-18 15:30:00',
			]
		];
	} else {
        header('Location: lists');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// por si los loles
	if (!isset($_SESSION['userData'])) {
		echo "Error: no user session found.";
		exit();
	}
    if (isset($_POST['follow_user'])) {
        $id_user = $_POST['id_user'];
        $UFUController->followUser($_SESSION['userData']['id_user'], $id_user);
        header("Location: profile?id=$id_user");
        exit();
    }
    if (isset($_POST['unfollow_user'])) {
        $id_user = $_POST['id_user'];
        $UFUController->unfollowUser($_SESSION['userData']['id_user'], $id_user);
        header("Location: profile?id=$id_user");
        exit();
    }
}
?>

<?php include 'partials/header.php'; ?>

<div class="my-14 container mx-auto min-h-screen">
    <div class="md:mx-auto">
		<?php if ($userController->readByUserID($user_ID) === false): ?>
			<p class="text-3xl text-center text-black font-bold">Usuario no encontrado</p>
		<?php else: ?>
			<div class="flex gap-12 justify-center">
				<!-- User info and actions -->
				<aside class="flex flex-col items-center gap-5 w-80 border border-borderGrey px-10 py-12">
					<img src="<?= htmlspecialchars($userInfo['profile_image']) ?>" alt="<?= htmlspecialchars($userInfo['username']) ?>" class="w-56 h-80 object-image rounded-lg">
					<p class="text-center text-3xl text-black font-bold"><?= htmlspecialchars($userInfo['username']) ?></p>
					<?php if (isset($_SESSION['userData']) && $_SESSION['userData']['id_user'] == $user_ID): ?>
						<button class="w-4/5 p-2 bg-accent font-semibold rounded-md" onclick="window.location.href = 'edit_profile';">Editar perfil</button>
					<?php elseif (!isset($_SESSION['userData'])): ?>
						<button class="w-full bg-green-500 font-semibold rounded-md" onclick="window.location.href = 'login';">Seguir</button>
					<?php else: ?>
						<?php if ($UFUController->isFollowing($_SESSION['userData']['id_user'], $user_ID)): ?>
							<form action="profile" method="POST">
								<input type="hidden" name="id_user" value="<?= $user_ID ?>">
								<button type="submit" name="unfollow_user" class="p-2 bg-red-500 text-white font-semibold rounded-md">Dejar de seguir</button>
							</form>
						<?php else: ?>
							<form action="profile" method="POST">
								<input type="hidden" name="id_user" value="<?= $user_ID ?>">
								<button type="submit" name="follow_user" class="p-2 bg-green-500 text-white font-semibold rounded-md">Seguir</button>
							</form>
						<?php endif; ?>
					<?php endif; ?>
					<div class="container">
						<div class="bg-[rgba(36,38,51,0.15)] shadow-md rounded-lg w-full grid grid-cols-2 auto-rows-auto"> <!-- Cuantos items queremos? idk -->
							<div class="p-4 flex flex-col items-center">
								<p class="text-xl text-black-700 font-bold"><?= count($followedUsers) ?></p>
								<p class="text-sm text-black-700 font-semibold">seguidos</p>
							</div>
							<div class="p-4 flex flex-col items-center">
								<p class="text-xl text-black-700 font-bold"><?= count($followers) ?></p>
								<p class="text-sm text-black-700 font-semibold">seguidores</p>
							</div>
							<div class="p-4 flex flex-col items-center text-align">
								<p class="text-xl text-black-700 font-bold"><?= count($pLists) ?></p>
								<p class="text-sm text-black-700 font-semibold">listas</p>
							</div>
							<div class="p-4 flex flex-col items-center">
								<p class="text-xl text-black-700 font-bold"><?= count($reviews) ?></p>
								<p class="text-sm text-black-700 font-semibold">reseñas</p>
							</div>
						</div>
					</div>
				</aside>
				<section class="border border-borderGrey w-2/4 px-10 py-12">
				<div class="flex items-center">
					<a href="lists" class="text-3xl font-bold text-gray-900 underline mr-2">Colección de <?= $userInfo['username'] ?></a>
					<span class="text-3xl font-bold text-gray-900">(<?= count($pLists) ?>)</span>
				</div>
					<?php if (count($pLists) > 0) : ?>
						<div class="gap-4 mt-6 grid grid-cols-4 pl-2">
							<?php foreach ($pLists as $list) : ?>
								<div class="flex flex-col gap-1 bg-[rgba(36,38,51,0.15)] shadow-md rounded-lg w-32">
									<a href="list?id=<?= $list['id_list'] ?>" class="text-lg font-extrabold text-gray-900"><?= $list['list_name'] ?></a>
									<div class="flex items-center gap-2 my-2 ml-1">
										<img src="img/bookStack.svg" alt="bils" class="w-4 h-4">
										<p class="text-sm text-black font-semibold"><?= $list['BILCount'] ?></p>
									</div> 
								</div>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<p class="text-lg text-gray-900">Colección privada.</p>
					<?php endif; ?>
					<div class="grid grid-cols-3 mt-6">
						<h1 class="text-3xl font-bold text-gray-900 w-full col-span-2 underline">Listas creadas por <?= $userInfo['username'] ?></h1>
						<?php if (isset($_SESSION['userData']) && $_SESSION['userData']['id_user'] == $user_ID): ?>
							<button class="w-fit h-max-2 p-4 bg-primary text-background font-semibold rounded-md justify-end" onclick="window.location.href = 'create_list';">Crear lista</button>
						<?php endif; ?>
					</div>
					<?php if (count($createdLists) > 0) : ?>
						<div class="flex gap-4 mt-4 pl-2">
							<?php foreach ($createdLists as $list) : ?>
								<div class="flex flex-col gap-1">
									<a href="list?id=<?= $list['id_list'] ?>" class="text-lg font-extrabold text-gray-900"><?= $list['list_name'] ?></a>
									<div class="flex items-center gap-2 my-2 ml-1">
										<img src="img/bookStack.svg" alt="bils" class="w-4 h-4">
										<p class="text-sm text-black font-semibold"><?= $list['BILCount'] ?></p>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<p class="text-lg text-gray-900">Este usuario no tiene listas públicas.</p>
					<?php endif; ?>
					<h1 class="text-3xl font-bold text-gray-900 mt-6 underline">Listas seguidas por <?= $userInfo['username'] ?></h1>
					<?php if (count($followedLists) > 0) : ?>
						<div class="flex gap-4 mt-6 pl-2">
							<?php foreach ($followedLists as $list) : ?>
								<div class="flex flex-col gap-1">
									<a href="list?id=<?= $list['id_list'] ?>" class="text-lg font-extrabold text-gray-900"><?= $list['list_name'] ?></a>
									<div class="flex items-center gap-2 my-1 ml-1">
										<img src="img/users.svg" alt="user" class="w-4 h-4">
										<a href="profile?id=<?= $list['id_user'] ?>" class="text-sm text-black font-semibold"><?= $list['ownerName'] ?></a>
									</div>
									<div class="flex items-center gap-2 my-1 ml-1">
										<img src="img/followers.svg" alt="followers" class="w-4 h-4">
										<p class="text-sm text-black font-semibold"><?= $list['followersNum'] ?></p>
									</div>
									<div class="flex items-center gap-2 my-1 ml-1">
										<img src="img/bookStack.svg" alt="bils" class="w-4 h-4">
										<p class="text-sm text-black font-semibold"><?= $list['BILCount'] ?></p>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<p class="text-lg text-gray-900">Este usuario no sigue ninguna lista.</p>
					<?php endif; ?>
					<h1 class="text-3xl font-bold text-gray-900 mt-6 underline">Reseñas de <?= $userInfo['username'] ?></h1>
					<?php if (count($reviews) > 0) : ?>
						<div class="flex gap-4 mt-6 pl-2">
							<?php foreach ($reviews as $review) : ?>
								<div class="flex flex-col gap-1">
									<h1 class="text-lg font-extrabold text-gray-900"><?= $review['book_title'] ?></h1>
									<div class="flex items-left gap-1">
										<p class="text-sm text-black font-semibold"><?= $review['rating'] ?></p>
										<img src="img/star.svg" alt="star" class="w-4 h-4">
									</div>
									<p class="text-sm text-black font-semibold"><?= $review['comment'] ?></p>
								</div>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<p class="text-lg text-gray-900">Este usuario no ha hecho ninguna reseña.</p>
					<?php endif; ?>	
				</section>
			</div>
		<?php endif; ?>
    </div>
</div>

<?php include 'partials/footer.php'; ?>