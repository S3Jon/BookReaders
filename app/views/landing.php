
<?php include_once 'partials/header_nopage.php'; ?>

<div
    class="min-h-[90vh] px-10 flex items-center justify-center bg-cover bg-center bg-[url('https://cdn.pixabay.com/photo/2016/08/24/16/20/books-1617327_1280.jpg')]">
    <div class="text-center bg-slate-700 px-20 py-12 rounded backdrop-blur-[5px] bg-white/90">
        <div class="flex flex-col max-w-[42rem]">
            <img src="img/logo_black.svg" class="h-20" alt="BookReaders_logo">
            <p class="mt-7 text-xl">
                ¡Únete a nuestra comunidad de lectores!
            </p>
            <p class="mb-10 text-xl">
                Descubre nuevos libros, comparte reseñas y conéctate con otros amantes de la lectura.
                Regístrate ahora para acceder a todo el contenido y formar parte de esta apasionante comunidad. ¡No te
                lo pierdas!
            </p>
            <div class="flex flex-col justify-center items-center gap-3">
                <a href="register"
                    class="text-xl w-fit font-semibold px-6 py-2 rounded bg-accent text-primary hover:text-white">Quiero
                    registrarme</a>
                <a href="explore" class="text-xl w-fit px-6 py-2 rounded text-primary hover:underline">prefiero<span
                        class="font-semibold"> Explorar</span></a>
            </div>
        </div>
    </div>
</div>

<?php include_once 'partials/footer.php'; ?>