<?php
require_once "../app/models/User.php";
require_once "../app/controllers/UserController.php";
require_once "../app/models/Book.php";
require_once "../app/models/List.php";
require_once "../app/controllers/ListController.php";
require_once "../app/controllers/BookController.php";
require_once "../app/models/Review.php";
require_once "../app/controllers/ReviewController.php";
require_once "../app/models/UserFollowLists.php";
require_once "../app/models/BookInList.php";
require_once "../app/models/test_book.php";
require_once "../app/models/UserFollowUsers.php";

$user = new models\User();
$book = new models\Book();
$list = new models\ListModel();
$bookController = new controllers\BookController($book);
$userController = new controllers\UserController($user);
$listController = new controllers\ListController($list);
$UFLController = new models\UFLModel();
$BILController = new models\BILModel();
$testbookCon = new models\Booktest();
$UFUController = new models\UFUmodel();
$review = new models\Review();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	if (isset($_GET['id'])) {
		$user_ID = $_GET['id'];
		
		$userInfo = $userController->readByUserId($user_ID);
		$pLists = $listController->getUserBasicLists($user_ID);
		$createdLists = $listController->getUserLists($user_ID);
		$followedLists = $UFLController->getFollowedLists($user_ID);
		$followedUsers = $UFUController->getFollowedUsers($user_ID);
		$followers = $UFUController->getFollowers($user_ID);
		$reviews = $review->getPublicReviewsByUser($user_ID); //A futuro si los usuarios son mutuals igual poner getReviewsByUser ya que devuelve todas
	}
	else {
		header('Location: lists');
		exit;
	}
}

session_start();

?>

<?php include 'partials/header.php'; ?>

<div class="my-14 container mx-auto min-h-screen">
	<div class="w-3/4 mx-auto">
		<div class="flex justify-between">
			<div>
				<p class="text-2xl font-semibold text-gray-900"><?= $userInfo['username'] ?></p>
				<p class="text-lg text-gray-600"><?= $userInfo['email'] ?></p>
			</div>
			<div>
				<?php if (isset($_SESSION['user']) && $_SESSION['user']['id_user'] != $user_ID): ?>
					<form action="" method="POST">
						<input type="hidden" name="id_user" value="<?= $user_ID ?>">
						<?php if ($UFUController->isFollowing($user_ID, $_SESSION['user']['id_user'])): ?>
							<button type="submit" name="unfollow" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Dejar de seguir</button>
						<?php else: ?>
							<button type="submit" name="follow" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Seguir</button>
						<?php endif; ?>
					</form>
				<?php endif; ?>
			</div>
		</div>
		<div class="mt-10">
			<p class="text-2xl font-semibold text-gray-900">Listas Básicas</p>
			<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 justify-items-center gap-10 mt-10">
				<?php foreach ($pLists as $key => $list): ?>
					<div class="container">
						<div class="bg-white shadow-md rounded-lg max-w-sm w-full">
							<div class="p-4">
								<form action="list" method="POST">
									<input type="hidden" name="id_list" value="<?= $list['id_list'] ?>">
									<button type="submit" class="underline text-lg font-semibold text-gray-900" name="submit_button"><?= $list['list_name'] ?></button>
								</form>
								<p class="text-sm text-gray-600"><?= $list['visibility'] ?></p>
								<p class="text-sm text-gray-600"><?= $list['created_at'] ?></p>
								

</div>

<?php include 'partials/footer.php'; ?>