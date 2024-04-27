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


?>

<?php include_once 'partials/header.php'; ?>

<div class="container mx-auto mt-5 ">
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
</div>

<?php include_once 'partials/footer.php'; ?>