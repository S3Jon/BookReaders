<?php
require_once "../app/models/User.php";
require_once "../app/controllers/UserController.php";
require_once "../app/models/Book.php";
require_once "../app/controllers/BookController.php";
require_once "../app/models/Review.php";
require_once "../app/controllers/ReviewController.php";
require_once "../app/models/UserFollowLists.php";
require_once "../app/models/BookInList.php";
require_once "../app/models/test_book.php";

$user = new models\User();
$book = new models\Book();
$bookController = new controllers\BookController($book);
$userController = new controllers\UserController($user);
$UFLController = new models\UFLModel();
$BILController = new models\BILModel();
$testbookCon = new models\Booktest();

session_start();

?>

<?php include 'partials/header.php'; ?>

<?php include 'partials/footer.php'; ?>