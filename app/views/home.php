<?php

session_start();

//TODO - esto es fix temporal, mejorar sistema
//mensaje inicio

if (isset($_SESSION['userData']) && !empty($_SESSION['userData']['username'])) {
	$sessiont = $_SESSION['userData']['role'] == 'admin' ? 2 : 1;
} else {
	$sessiont = 3;	
}

if ($sessiont < 3) {
	$mensaje = "¡Bienvenido, " . $_SESSION['userData']['username'] . "!";
} else {
	$mensaje = "¡Bienvenido!";
}

require_once '../app/controllers/BookController.php';
require_once '../app/models/Book.php';
require_once '../app/controllers/ListController.php';
require_once '../app/models/List.php';


$bookController = new controllers\BookController(new models\Book());
$listController = new controllers\ListController(new models\ListModel());

$activity = $listController->getPublicLists();

?>

<?php include_once 'partials/header.php'; ?>

<!-- <div class="container mx-auto mt-5 ">
    <div class="md:w-1/2 md:mx-auto">
        <div class="card">
            <div class="card-body flex justify-between">
                <h2 class="card-title"><?php echo $mensaje; ?></h2>
                <?php if ( $sessiont < 3): ?>
                    <form action="logout" method="post" onsubmit="return confirm('¿Estás seguro de que quieres cerrar tu sesión?');">
                        <button type="submit" class="btn-danger px-4 py-2 rounded-md bg-red-500 hover:bg-red-600 focus:outline-none focus:bg-red-600">Cerrar sesión</button>
                    </form>
                    <?php if ($sessiont == 2): ?>
                        <a href="adminpanel" class="text-blue-500">
                            <button type="button" class="px-4 py-2 rounded-md bg-blue-200 hover:bg-blue-300 focus:outline-none focus:bg-blue-300">Ir al panel de administración</button>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login" class="text-blue-500">
                        <button type="button" class="px-4 py-2 rounded-md bg-blue-200 hover:bg-blue-300 focus:outline-none focus:bg-blue-300">Iniciar sesión</button>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div> -->
<div class="my-14 container mx-auto min-h-screen">
    <div class="md:mx-auto">
        <div class="flex gap-8 justify-center">
            <!-- Book image and actions -->
            <aside class="flex flex-col justify-center items-center gap-8 w-64 px-12 py-10 bg-accent/50 border-2 border-accent rounded-md">
                <h1 class="text-3xl font-bold font-serif4 text-center">Libro de la semana</h1>
                <img src="uploads/kafka.svg" alt="Book cover" class="w-48 h-64 object-cover">
                <div class="flex flex-col items-center w-full">
                    <h2 class="font-bold truncate" title="Titulo">Titulo</h2>
                    <a href="profile?id=1" class="truncate" title="Autor">Autor</a>
                    <hr class="mt-2 w-full h-[2px] bg-accent border-accent">
                    <div class="max-h-24 mt-6 overflow-hidden text-ellipsis">
                        <p class="text-center line-clamp-3" title="Descripción">
                            Descripción
                        </p>
                    </div>
                    <button class="mt-6 w-full p-2 bg-primary text-background font-semibold rounded-md">Ver más</button>
                    <p class="mt-8 text-center">
                        ¿Quieres destacar tu libros? ¡Contacta con nosotros y te informaremos!
                    </p>
                    <button class="mt-6 w-full p-2 bg-white text-primary font-semibold rounded-md">Contactar</button>
                </div>
            </aside>
            <!-- Book details -->
            <section class="w-2/4">
                <h1 class="text-3xl font-bold font-serif4">La Comunidad de Lector@s</h1>
                <div class="flex justify-between items-center">
                    <p>
                        ¡Hola <?php echo $_SESSION['userData']['username'] ?? 'lector/a' ?>! Mira lo que está pasando en BookReaders
                    </p>
                    <div class="flex">
                        <button class="ml-4 px-5 py-2 bg-accent text-primary font-semibold rounded-full">Más recientes</button>
                        <button class="ml-4 px-5 py-2 border-2 border-primary text-primary font-semibold rounded-full">Siguiendo</button>
                    </div>
                </div>
                <div class="flex">
                    <?php foreach ($activity as $act) : ?>
                        
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>
</div>