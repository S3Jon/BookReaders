<?php

require_once '../app/controllers/UserController.php';
require_once '../app/models/User.php';
require_once '../app/controllers/BookController.php';
require_once '../app/models/Book.php';
require_once '../app/controllers/ReviewController.php';
require_once '../app/models/Review.php';

session_start();
if (!isset($_SESSION['userData'])) {
    header('Location: home');
    exit;
}

$bookController = new controllers\BookController(new models\Book());
$reviewController = new controllers\ReviewController(new models\Review());
$userController = new controllers\UserController(new models\User());

$book = $bookController->readBook($_GET['isbn']);
$reviews = $reviewController->getReviews($_GET['isbn']);

//reviws visibility control 
$reviews = array_filter($reviews, function ($review) {
    if ($review['visibility'] === 'private' && $review['id_user'] === $_SESSION['userData']['id_user']) {
        return true;
    }
    return $review['visibility'] === 'public';
});

$canReview = true;
$currentUserId = $_SESSION['userData']['id_user'];

foreach ($reviews as $review) {
    if ($review['id_user'] == $currentUserId) {
        $canReview = false;
        break;
    }
}

$title = isset($book['title']) ? $book['title'] : 'Título no disponible';
$author = isset($book['author']) ? $book['author'] : 'Autor no disponible';
$description = isset($book['description']) ? $book['description'] : 'Descripción no disponible';
$image = isset($book['image']) ? $book['image'] : 'uploads/book_placeholder.jpg';
$genres = json_decode(isset($book['genre'])) ? json_decode($book['genre']) : [];
$reviews = isset($reviews) ? $reviews : [];
$totalReviews = count($reviews);

$rating = 0;

if (count($reviews) > 0) {
    $totalRating = 0;
    foreach ($reviews as $review) {
        $totalRating += $review['rating'];
    }
    $rating = number_format($totalRating / count($reviews), 2);
}

// Ordenar reviws por fecha 
$currentUserReview = null;
$otherReviews = [];

// Separar la reseña del usuario actual del resto
foreach ($reviews as $key => $review) {
    if ($review['id_user'] === $currentUserId) {
        $currentUserReview = $review;
    } else {
        $otherReviews[] = $review;
    }
}

// Ordenar las demás reseñas por fecha
usort($otherReviews, function ($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

// Colocar la reseña del usuario actual al inicio, si existe
if ($currentUserReview) {
    array_unshift($otherReviews, $currentUserReview);
}

// Actualizar la variable $reviews con el nuevo orden
$reviews = $otherReviews;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $data = [
        'id_user' => $_SESSION['userData']['id_user'],
        'isbn' => $_GET['isbn'],
        'rating' => isset($_POST['rate']) ? $_POST['rate'] : 1,
        'comment' => $_POST['comment'],
        'visibility' => $_POST['review']
    ];

    if ($reviewController->createReview($data)) {
        header('Location: book?isbn=' . $_GET['isbn']);
        exit;
    } else {
        // echo 'Error al publicar la reseña';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_review'])) {
    $data = [
        'id_review' => $_POST['id_review'],
        'comment' => $_POST['comment'],
        'rating' => $_POST['rate'],
        'visibility' => $_POST['review']
    ];

    if ($reviewController->updateReview($data)) {
        header('Location: book?isbn=' . $_GET['isbn']);
        exit;
    } else {
        // echo 'Error al actualizar la reseña';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review'])) {
    echo 'delete';
    $data = [
        'id_review' => $_POST['id_review']
    ];

    if ($reviewController->deleteReview($data)) {
        header('Location: book?isbn=' . $_GET['isbn']);
        exit;
    } else {
        // echo 'Error al eliminar la reseña';
    }
}

?>

<?php include_once 'partials/header.php'; ?>

<div class="my-14 container mx-auto min-h-screen">
    <div class="md:mx-auto">
        <div class="flex gap-12 justify-center">
            <!-- Book image and actions -->
            <aside class="flex flex-col items-center gap-5 w-56">
                <img src="<?= $image ?>" alt="<?= $title ?>" class="w-56 h-80 object-image rounded-lg">
                <button class="w-4/5 p-2 bg-accent font-semibold rounded-md">Añadir a favoritos</button>
                <button class="w-4/5 p-2 bg-primary text-background font-semibold rounded-md">Añadir a una lista</button>
            </aside>
            <!-- Book details -->
            <section class="border border-borderGrey w-2/4 px-10 py-12">
                <header class="flex justify-between">
                    <div class="flex flex-col gap-1">
                        <h1 class="font-extrabold font-serif4 text-3xl"><?= $title ?></h1>
                        <p><?= $author ?></p>
                    </div>
                    <div class="flex flex-col items-end">
                        <div class="flex items-center gap-2 ">
                            <img src="img/star.svg" alt="">
                            <span class="pt-1 text-3xl font-bold"><?= $rating ?></span>
                        </div>
                        <p class="mr-[2px]"><?= $totalReviews ?> votos</p>
                    </div>
                </header>
                <div class="flex gap-4 mt-6">
                    <?php if (count($genres) > 0) : ?>
                        <?php foreach ($genres as $genre) : ?>
                            <a href="explore?genre=<?= $genre ?>" class="px-[12px] py-[7px] border-2 font-semibold text-[12px] border-primary rounded-[25px] cursor-pointer"><?= $genre ?></a>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <span class="text-black rounded-lg">Sin género</span>
                    <?php endif; ?>
                </div>
                <p class="mt-8"><?= $description ?></p>

                <!-- Add Review -->
                <?php if($canReview) : ?>
                    <section class="mt-10 bg-[rgba(36,38,51,0.15)] px-10 py-7 rounded-lg">
                        <form action="" class="flex flex-col gap-7" method="POST">
                            <div class="flex items-center gap-3">
                                <p class="mt-1">Tu voto:</p>
                                <div class="flex flex-row-reverse gap-3 rating-container">
                                    <input type="radio" id="star5" name="rate" value="5" hidden />
                                    <label for="star5" title="5 star"><img class="cursor-pointer" src="img/star2.svg" alt="5 star"></label>
                                    <input type="radio" id="star4" name="rate" value="4" hidden/>
                                    <label for="star4" title="4 stars"><img class="cursor-pointer" src="img/star2.svg" alt="3 stars"></label>
                                    <input type="radio" id="star3" name="rate" value="3" hidden/>
                                    <label for="star3" title="3 stars"><img class="cursor-pointer" src="img/star2.svg" alt="3 stars"></label>
                                    <input type="radio" id="star2" name="rate" value="2" hidden/>
                                    <label for="star2" title="2 stars"><img class="cursor-pointer" src="img/star2.svg" alt="2 stars"></label>
                                    <input type="radio" id="star1" name="rate" value="1" hidden/>
                                    <label for="star1" title="1 stars"><img class="cursor-pointer" src="img/star2.svg" alt="1 stars"></label>
                                </div>
                            </div>
                            <textarea name="comment" id="comment" placeholder="Si lo deseas, escribe aquí tu reseña" cols="5" rows="4" class="rounded-lg p-3"></textarea>
                            <div class="flex justify-between">
                                <div class="flex items-center gap-2">
                                    <label for="private" class="cursor-pointer">Reseña privada</label>
                                    <input type="radio" id="private" name="review" value="private" class="cursor-pointer mr-10 mt-[2px] w-5 h-5" checked />
                                    <label for="public" class="cursor-pointer">Reseña pública</label>
                                    <input type="radio" id="public" name="review" value="public" class="cursor-pointer mt-[2px] w-5 h-5" />
                                </div>
                                <button type="submit" name="submit" class="bg-accent text-black font-bold py-2 px-6 rounded-lg">Publicar</button>
                            </div>
                        </form>
                    </section>
                <?php endif; ?>

                <!-- Reviews -->
                <section class="mt-10">
                    <h2><?= count($reviews) > 1 ? count($reviews) . ' reseñas de usuarios' : count($reviews) . ' reseña de usuario' ?></h2>
                    <?php if (count($reviews) > 0) : ?>
                        <?php foreach ($reviews as $review) : ?>
                            <div class="my-3 border-t border-t-borderGrey p-6 last:pb-0">
                                <div class="flex justify-between">
                                    <div class="flex gap-3">
                                        <img src="img/maestro.svg" alt="user" class="w-10">
                                        <div class="flex flex-col">
                                            <p class="font-bold"><?=
                                                $userController->readByUserID($review['id_user'])['username']
                                            ?></p>
                                            <p class="text-sm"><?= $review['created_at'] ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <img src="img/star.svg" alt="rating" class="w-5">
                                        <span class="font-bold pt-1"><?= isset($review['rating']) && $review['rating'] !== null ? $review['rating'] . '/5' : '1/5' ?></span>
                                    </div>
                                </div>
                                <?php if ($review['id_user'] === $currentUserId): ?>
                                    <form action="" method="POST" class="mt-4">
                                        <input type="hidden" name="id_review" value="<?= $review['id_review'] ?>">
                                        <div class="flex items-center gap-3 mb-5">
                                            <p class="mt-1">Tu voto:</p>
                                            <div class="flex flex-row-reverse gap-3 rating-container">
                                                <input type="radio" id="star5" name="rate" value="5" hidden <?= $review['rating'] == 5 ? 'checked' : '' ?> />
                                                <label for="star5" title="5 star"><img class="cursor-pointer" src="img/star2.svg" alt="5 star"></label>
                                                <input type="radio" id="star4" name="rate" value="4" hidden <?= $review['rating'] == 4 ? 'checked' : '' ?> />
                                                <label for="star4" title="4 stars"><img class="cursor-pointer" src="img/star2.svg" alt="4 stars"></label>
                                                <input type="radio" id="star3" name="rate" value="3" hidden <?= $review['rating'] == 3 ? 'checked' : '' ?> />
                                                <label for="star3" title="3 stars"><img class="cursor-pointer" src="img/star2.svg" alt="3 stars"></label>
                                                <input type="radio" id="star2" name="rate" value="2" hidden <?= $review['rating'] == 2 ? 'checked' : '' ?> />
                                                <label for="star2" title="2 stars"><img class="cursor-pointer" src="img/star2.svg" alt="2 stars"></label>
                                                <input type="radio" id="star1" name="rate" value="1" hidden <?= $review['rating'] == 1 ? 'checked' : '' ?> />
                                                <label for="star1" title="1 star"><img class="cursor-pointer" src="img/star2.svg" alt="1 star"></label>
                                            </div>
                                        </div>
                                        <textarea name="comment" class="w-full border border-gray-300 p-2"><?= $review['comment'] ?></textarea>
                                        <div class="flex justify-between">
                                            <div class="flex items-center gap-2">
                                                <label for="private" class="cursor-pointer">Reseña privada</label>
                                                <input type="radio" id="private" name="review" value="private" class="cursor-pointer mr-10 mt-[2px] w-5 h-5" <?= $review['visibility'] == 'private' ? 'checked' : '' ?> />
                                                <label for="public" class="cursor-pointer">Reseña pública</label>
                                                <input type="radio" id="public" name="review" value="public" class="cursor-pointer mt-[2px] w-5 h-5" <?= $review['visibility'] == 'public' ? 'checked' : '' ?> />
                                            </div>
                                            <div class="flex gap-4">
                                                <button type="submit" name="update_review" class="mt-2 px-4 py-2 bg-blue-400 text-white rounded">Editar</button>
                                                <button type="submit" name="delete_review" class="mt-2 px-4 py-2 bg-red-400 text-white rounded">Eliminar</button>
                                            </div>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <p class="mt-4"><?= htmlspecialchars($review['comment']) ?></p>
                                <?php endif; ?>
                            </div>                        
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p class="mt-6">No hay reseñas para mostrar.</p>
                    <?php endif; ?>
                </section>
            </section>
        </div>
    </div>
</div>

<?php include_once 'partials/footer.php'; ?>
