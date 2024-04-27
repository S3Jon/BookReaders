<?php

$book = [
    'title' => 'La metamorfosis',
    'author' => 'Franz Kafka',
    'description' => 'Al despertar Gregorio Samsa una mañana, tras un sueño intranquilo, encontróse en su cama convertido en un monstruoso insecto. Tal es el abrupto comienzo, que nos sitúa de raíz bajo unas reglas distintas, de LA METAMORFOSIS, sin duda alguna la obra de Franz Kafka (1883-1924) que ha alcanzado mayor celebridad. Escrito en 1912 y publicado en 1916, este relato es considerado una de las obras maestras de este siglo por sus innegables rasgos precursores y el caudal de ideas e interpretaciones que desde siempre ha suscitado. Completan el volumen los relatos "Un artista del hambre" y "Un artista del trapecio".',
    'cover' => 'uploads/kafka.svg',
    'tags' => ['Ficción', 'Fantástico', 'Absurdo'],
    'reviews' => [
        [
            'user' => 'María José',
            'rating' => 3,
            'date' => '16/04/2024',
            'comment' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus vel magna eu arcu commodo eleifend non sit amet leo. Curabitur laoreet ultrices mauris, pellentesque tempus ipsum dictum eget. Aenean ac velit pulvinar, placerat dui a, malesuada dui. Ut tortor nisl, ornare placerat miLorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus vel magna eu arcu commodo eleifend non sit amet leo. Curabitur laoreet ultrices mauris, pellentesque tempus ipsum dictum eget. Aenean ac velit pulvinar, placerat dui a, malesuada dui. Ut tortor nisl, ornare placerat mi.'
        ],
        [
            'user' => 'María González',
            'rating' => 4,
            'date' => '10/04/2024',
            'comment' => 'Muy buen libro, aunque un poco difícil de leer.'
        ],
        [
            'user' => 'Pedro Rodríguez',
            'rating' => 3,
            'date' => '12/04/2024',
            'comment' => 'No me gustó mucho, no lo recomendaría.'
        ]
    ]
];

//TODO - esto es fix temporal, mejorar sistema
session_start();

//añade una nueva review al array de reviews del libro con el formulario
if (isset($_POST['submit'])) {
    $newReview = [
        'user' => $_SESSION['userData']['username'],
        'rating' => $_POST['rate'],
        'date' => date('d/m/Y'),
        'comment' => $_POST['comment']
    ];
    array_push($book['reviews'], $newReview);
}

$totalReviews = count($book['reviews']);

//suma de todos los ratings de las reviews
$sum = 0;
foreach ($book['reviews'] as $review) {
    $sum += $review['rating'];
}

//calculo de la media
$average = $sum / $totalReviews;

$book['rating'] = round($average, 1);

?>

<?php include_once 'partials/header.php'; ?>

<div class="my-14 container mx-auto min-h-screen">
    <div class="md:mx-auto">
        <div class="flex gap-12 justify-center">
            <!-- Book cover and actions -->
            <aside class="flex flex-col items-center gap-5 w-56">
                    <img src="<?= $book['cover'] ?>" alt="<?= $book['title'] ?>" class="w-52">
                    <button class="w-4/5 p-2 bg-accent font-semibold rounded-md">Añadir a favoritos</button>
                    <button class="w-4/5 p-2 bg-primary text-background font-semibold rounded-md">Añadir a una lista</button>
            </aside>
            <!-- Book details -->
            <section class="border border-borderGrey w-2/4 px-10 py-12">
                <header class="flex justify-between">
                    <div class="flex flex-col gap-1">
                        <h1 class="font-extrabold font-serif4 text-3xl"><?= $book['title'] ?></h1>
                        <p><?= $book['author'] ?></p>
                    </div>
                    <div class="flex flex-col items-end">
                        <div class="flex items-center gap-2 ">
                            <img src="img/star.svg" alt="">
                            <span class="pt-1 text-3xl font-bold"><?= $book['rating'] ?>/5</span>
                        </div>
                        <p class="mr-[2px]"><?= $totalReviews ?> votos</p>
                    </div>
                </header>
                <div class="flex gap-4 mt-6">
                    <?php foreach ($book['tags'] as $tag) : ?>
                        <span class="px-[12px] py-[7px] border-2 font-semibold text-[12px] border-primary rounded-[25px] cursor-pointer"><?= $tag ?></span>
                    <?php endforeach; ?>
                </div>
                <p class="mt-8"><?= $book['description'] ?></p>                

                <!-- Add Review -->
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

                <!-- Reviews -->
                <!-- <section class="mt-10">
                    <h2 class=""><?= $totalReviews ?> reseñas de usuarios</h2>
                    <?php foreach ($book['reviews'] as $review) : ?>
                        <div class="mt-6 border border-borderGrey p-6 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img src="img/maestro.svg" alt="user" class="w-10">
                                    <div class="flex flex-col">
                                        <p class="font-bold"><?= $review['user'] ?></p>
                                        <p class="text-sm"><?= $review['date'] ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <img src="img/star.svg" alt="rating" class="w-5">
                                    <span class="font-bold pt-1 "><?= $review['rating'] ?>/5</span>
                                </div>
                            </div>
                            <p class="mt-4"><?= $review['comment'] ?></p>
                        </div>                        
                    <?php endforeach; ?>
                </section> -->
                <section class="mt-10">
                    <h2><?= $totalReviews ?> reseñas de usuarios</h2>
                    <?php foreach ($book['reviews'] as $review) : ?>
                        <div class="my-3 border-t border-t-borderGrey p-6 last:pb-0">
                            <div class="flex justify-between">
                                <div class="flex gap-3">
                                    <img src="img/maestro.svg" alt="user" class="w-10">
                                    <div class="flex flex-col">
                                        <p class="font-bold"><?= $review['user'] ?></p>
                                        <p class="text-sm"><?= $review['date'] ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <img src="img/star.svg" alt="rating" class="w-5">
                                    <span class="font-bold pt-1"><?= $review['rating'] ?>/5</span>
                                </div>
                            </div>
                            <p class="mt-4"><?= $review['comment'] ?></p>
                        </div>
                        
                    <?php endforeach; ?>
                </section>
            </section>
        </div>
    </div>
</div>

<?php include_once 'partials/footer.php'; ?>
