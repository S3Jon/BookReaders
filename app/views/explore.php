<?php

require_once '../app/controllers/UserController.php';
require_once '../app/models/User.php';
require_once '../app/controllers/BookController.php';
require_once '../app/models/Book.php';
require_once '../app/controllers/ReviewController.php';
require_once '../app/models/Review.php';

session_start();

$bookController = new controllers\BookController(new models\Book());
$reviewController = new controllers\ReviewController(new models\Review());
$books = $bookController->getTop50Books();

?>

<?php require_once 'partials/header.php'; ?>

<div class="my-14 container mx-auto min-h-screen">
    <div class="w-3/4 mx-auto">
        <!-- Header buscador de libros -->
        <div class="flex flex-col md:flex-row md:items-center w-full gap-5 md:gap-20">
            <h1 class="font-serif4 font-bold text-3xl min-w-fit">Explorar libros</h1>
            <form class="flex items-center gap-5 w-full pb-1 border-b-2 border-primary">
                <input type="text" class="w-full py-2 bg-transparent outline-none"
                    placeholder="Busca un libro, autor/a o editorial">
                <div class="relative inline-block">
                    <button type="button" onclick="toggleDropdown()"
                        class="flex items-center gap-3 w-32 text-black p-3 text-sm border-none cursor-pointer">
                        <img class="w-5 h-5" src="img/arrow_down.svg" alt="">
                        <p class="text-base" id="selectedOption">Título</p>
                    </button>
                    <div id="myDropdown" class="hidden absolute bg-white min-w-[160px] shadow z-10">
                        <a class="text-black px-2 py-3 block no-underline cursor-pointer border"
                            onclick="selectOption('Título')">Título</a>
                        <a class="text-black px-2 py-3 block no-underline cursor-pointer border"
                            onclick="selectOption('Autor')">Autor</a>
                        <a class="text-black px-2 py-3 block no-underline cursor-pointer border"
                            onclick="selectOption('Editorial')">Editorial</a>
                    </div>
                </div>

                <button class="min-w-4 min-h-4" type="submit"><img class="min-w-4 min-h-4" src="img/lupa.svg" alt="lupa.svg"></button>
            </form>
        </div>

        <!-- Lista de libros -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 justify-items-center gap-10 mt-10">
            <?php foreach ($books as $book) : ?>
                <form action="book" method="POST" class="cursor-pointer">
                    <input type="hidden" name="isbn" value="<?= $book['isbn']?>">
                    <div class="flip-container">
                        <div class="flip-card">
                            <div class="flip-front bg-white w-full h-full">
                                <img class="w-full h-full" src="<?= $book['image']?>" alt="<?= $book['image']?>">
                            </div>
                            <div class="flip-back">
                                <div class="flex flex-col items-center justify-center h-full gap-5 p-3">
                                    <h2 class="font-serif4 font-bold text-xl"><?= $book['title']?></h2>
                                    <p class="font-serif font-light text-sm"><?= $book['author']?></p>
                                    <p class="font-serif font-light text-sm"><?= $book['editorial']?></p>
                                    <div class="flex items center gap-1">
                                        <?php if ($book['rating'] > 0) : ?>
                                            <p class = "text-xs"><?= number_format($book['rating'], 1)?></p>
                                            <img class="w-3 h-3" src="img/star.svg" alt="star">
                                        <?php else : ?>
                                            <p class="text-xs">Sin reseñas!</p>
                                        <?php endif; ?>
										<?php if ($book['review_count'] > 0) : ?>
											<p class="text-xs"> (<?= $book['review_count'] ?>)</p>
										<?php endif; ?>
                                    </div>
                                    <a href="book?isbn=<?= $book['isbn'] ?>" class="bg-primary/40 text-white px-5 py-2 rounded-full">Ver más</a>
                                </div>
                            </div>
                        </div>
                    </div> 
                </form>
            <?php endforeach; ?>
        </div>
        
    </div>
</div>

<script>
    function toggleDropdown() {
        var dropdown = document.getElementById("myDropdown");
        dropdown.classList.toggle("show");
    }

    function selectOption(option) {
        document.getElementById("selectedOption").innerText = option;
        toggleDropdown();
    }

    // Cerrar el dropdown si se hace clic fuera de él
    window.onclick = function (event) {
        if (!event.target.matches('.dropbtn')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            var i;
            for (i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
</script>

<?php require_once 'partials/footer.php'; ?>