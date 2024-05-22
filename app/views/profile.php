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
$UFUController = new models\Followersmodel(); //cambia M

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $user_ID = $_GET['id'];
        
		$FLInfo = [];
        $userInfo = $userController->readByUserId($user_ID);
        $pLists = $listController->getUserBasicLists($user_ID);
        $createdLists = $listController->getUserTopLists($user_ID);
        $followedLists = $UFLController->getFollowedLists($user_ID);
		foreach ($followedLists as $key => $list) {
			$FLInfo[$key] = $listController->exploreLists($list['id_list']);
		}
        $followedUsers = $UFUController->getFollowedUsers($user_ID);
        $followers = $UFUController->getFollowers($user_ID);
        //$reviews = $review->getPublicReviewsByUser($user_ID); //A futuro si los usuarios son mutuals igual poner getReviewsByUser ya que devuelve todas (no solo public)
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
        <div class="flex gap-12 justify-center">
            <!-- User info and actions -->
            <aside class="flex flex-col items-center gap-5 w-80 border border-borderGrey px-10 py-12">
                <img src="<?= htmlspecialchars($userInfo['profile_image']) ?>" alt="<?= htmlspecialchars($userInfo['username']) ?>" class="w-56 h-80 object-image rounded-lg">
                <p class="text-center text-3xl text-black font-bold"><?= htmlspecialchars($userInfo['username']) ?></p>
                <?php if (isset($_SESSION['userData']) && $_SESSION['userData']['id_user'] == $user_ID): ?>
                    <button class="w-4/5 p-2 bg-accent font-semibold rounded-md" onclick="window.location.href = 'edit_profile';">Editar perfil</button>
                    <button class="w-4/5 p-2 bg-primary text-background font-semibold rounded-md" onclick="window.location.href = 'create_list';">Crear lista</button> <!-- TODO- Mover a listas de usuario -->
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
				</div>
            </aside>
			<section class="border border-borderGrey w-2/4 px-10 py-12">
				<h1 class="text-3xl font-bold text-gray-900">Colección de <?= $userInfo['username'] ?></h1>
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
					<p class="text-lg text-gray-900">Este usuario no ha puesto pública su colección.</p>
				<?php endif; ?>
				<h1 class="text-3xl font-bold text-gray-900 mt-6">Listas creadas por <?= $userInfo['username'] ?></h1>
				<?php if (count($createdLists) > 0) : ?>
					<div class="flex gap-4 mt-6 pl-2">
					
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
					<p class="text-lg text-gray-900">Este usuario no ha creado ninguna lista.</p>
				<?php endif; ?>
				<h1 class="text-3xl font-bold text-gray-900 mt-6">Listas seguidas por <?= $userInfo['username'] ?></h1>
				<?php if (count($followedLists) > 0) : ?>
					<div class="flex gap-4 mt-6 pl-2">
						<?php foreach ($followedLists as $list) : ?>
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
					<p class="text-lg text-gray-900">Este usuario no sigue ninguna lista.</p>
				<?php endif; ?>	
			</section>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>