<?php

session_start();

if (!isset($_SESSION['userData']) || empty($_SESSION['userData']['username'])) {
    header('Location: login');
    exit;
} else {
    $userData = $_SESSION['userData'];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Home</title>    
</head>
<body>
    <div class="container mx-auto mt-5 ">
        <div class="md:w-1/2 md:mx-auto">
            <div class="card">
                <div class="card-body flex justify-between">
                    <h2 class="card-title">¡Bienvenido, <?php echo htmlspecialchars($userData['username']); ?>!</h2>
                    <?php if ($userData['role'] === 'admin'): ?>
                        <a href="adminpanel" class="text-blue-500">
                            <button type="button" class="px-4 py-2 rounded-md bg-blue-200 hover:bg-blue-300 focus:outline-none focus:bg-blue-300">Ir al panel de administración</button>
                        </a>
                    <?php endif; ?>
                    <form action="logout" method="post" onsubmit="return confirm('¿Estás seguro de que quieres cerrar tu sesión?');">
                        <button type="submit" class="btn-danger px-4 py-2 rounded-md bg-red-500 hover:bg-red-600 focus:outline-none focus:bg-red-600">Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
