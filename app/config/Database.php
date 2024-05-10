<?php

namespace config;

use PDO;
use PDOException;

class Database{
    private $host = "localhost";
    private $db_name = "BookReadersDB";
    private $username = "root";
    private $password = "";
    public $conn;

    // start the connection
    public function getConnection(){
        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);            
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Error de conexion: ".$exception->getMessage();
        }

        return $this->conn;
    }

    // stop the connection
    public function closeConnection(){
        $this->conn = null;
    }
    
    // create database
    public function createDatabase(){
        try {
            $this->conn = new PDO("mysql:host=".$this->host, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $sql = "CREATE DATABASE IF NOT EXISTS " . $this->db_name;
            $this->conn->exec($sql);
            
            // If exists, close the connection and open a new one
            if ($this->conn) {
                $this->getConnection();
            }
        } catch(PDOException $e) {
            echo "Error al crear la base de datos: " . $e->getMessage();
        }
    }

    // create users table
    public function createUsersTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS users (
                id_user INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) UNIQUE NOT NULL,
                username VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('user','admin') DEFAULT 'user',
                name VARCHAR(255),
                profile_image VARCHAR(255),
                metadata JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $this->conn->exec($sql);
            // echo "Tabla de usuarios creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de usuarios: " . $e->getMessage();
        }
    }

    // create default admin user
    public function createDefaultAdminUser(){
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE role = 'admin'");
            $stmt->execute();
            $existingAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$existingAdmin){
                $username = "admin";
                $email = "admin@gmail.com";
                $password = password_hash("admin", PASSWORD_DEFAULT);

                $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'admin')");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $stmt->execute();

                // echo "Usuario administrador creado exitosamente.";
            } else {
                // echo "Ya existe un usuario administrador en la base de datos.";
            }
        } catch(PDOException $e) {
            echo "Error al crear el usuario administrador: " . $e->getMessage();
        }
    }

	public function createExtraUsers(){
		try {
			$stmt = $this->conn->prepare("SELECT * FROM users");
			$stmt->execute();
			$existingUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if (count($existingUsers) < 10) {
				$extraUsers = [
					['username' => 'user1', 'email' => 'user1@user1.user1', 'password' => password_hash('user1', PASSWORD_DEFAULT), 'role' => 'user'],
					['username' => 'user2', 'email' => 'user2@user2.user2', 'password' => password_hash('user2', PASSWORD_DEFAULT), 'role' => 'user'],
					['username' => 'user3', 'email' => 'user3@user3.user3', 'password' => password_hash('user3', PASSWORD_DEFAULT), 'role' => 'user'],
					['username' => 'user4', 'email' => 'user4@user4.user4', 'password' => password_hash('user4', PASSWORD_DEFAULT), 'role' => 'user'],
					['username' => 'user5', 'email' => 'user5@user5.user5', 'password' => password_hash('user5', PASSWORD_DEFAULT), 'role' => 'user'],
					['username' => 'user6', 'email' => 'user6@user6.user6', 'password' => password_hash('user6', PASSWORD_DEFAULT), 'role' => 'user'],
					['username' => 'user7', 'email' => 'user7@user7.user7', 'password' => password_hash('user7', PASSWORD_DEFAULT), 'role' => 'user'],
					['username' => 'pedro', 'email' => 'pedro@pedro.pedro', 'password' => password_hash('pedro', PASSWORD_DEFAULT), 'role' => 'user'],
					['username' => 'juan', 'email' => 'juan@juan.juan', 'password' => password_hash('juan', PASSWORD_DEFAULT), 'role' => 'user'],
					['username' => 'maria', 'email' => 'maria@maria.maria', 'password' => password_hash('maria', PASSWORD_DEFAULT), 'role' => 'user']
				];
				
				$stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
				foreach($extraUsers as $user){
					$stmt->bindParam(':username', $user['username']);
					$stmt->bindParam(':email', $user['email']);
					$stmt->bindParam(':password', $user['password']);
					$stmt->bindParam(':role', $user['role']);
					$stmt->execute();
				}
		} else {
			// echo "Ya existen usuarios extra en la base de datos.";
		}
		} catch(PDOException $e) {
			echo "Error al crear los usuarios extra: " . $e->getMessage();
		
		}
	}

    // create followers table
    public function createFollowersTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS followers (
                id_follower INT AUTO_INCREMENT PRIMARY KEY,
                id_user INT NOT NULL,
                id_followed INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
                FOREIGN KEY (id_followed) REFERENCES users(id_user) ON DELETE CASCADE,
                UNIQUE (id_user, id_followed)
            )";
            $this->conn->exec($sql);
            // echo "Tabla de seguidores creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de seguidores: " . $e->getMessage();
        }
    }

    // create books table
    public function createBooksTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS books (
                id_book INT AUTO_INCREMENT PRIMARY KEY,
                isbn VARCHAR(255) UNIQUE NOT NULL,
                title VARCHAR(255) NOT NULL,
                author VARCHAR(255) NOT NULL,
                genre VARCHAR(255) NOT NULL,
                editorial VARCHAR(255) NOT NULL,
                image VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $this->conn->exec($sql);
            // echo "Tabla de libros creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de libros: " . $e->getMessage();
        }
    }

    // create reviews table
    public function createReviewsTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS reviews (
                id_review INT AUTO_INCREMENT PRIMARY KEY,
                id_user INT NOT NULL,
                isbn VARCHAR(255) NOT NULL,
                rating INT NOT NULL,
                comment TEXT NOT NULL,
                visibility ENUM('public','private') DEFAULT 'private',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
                FOREIGN KEY (isbn) REFERENCES books(isbn) ON DELETE CASCADE
            )";
            $this->conn->exec($sql);
            // echo "Tabla de reseñas creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de reseñas: " . $e->getMessage();
        }
    }

    // create userlists table
    public function createListsTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS lists (
                id_list INT AUTO_INCREMENT PRIMARY KEY,
                id_user INT NOT NULL,
                list_name VARCHAR(255) NOT NULL,
                type ENUM('favorite', 'read', 'want_to_read','reading','dropped') DEFAULT NULL,
                visibility ENUM('public','private') DEFAULT 'private',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
            )";
            $this->conn->exec($sql);
            // echo "Tabla de listas de usuario creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de listas de usuario: " . $e->getMessage();
        }
    }

	public function insertDummyLists(){
		try {
			$stmt = $this->conn->prepare("SELECT * FROM lists");
			$stmt->execute();
			$dummyListsExist = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!$dummyListsExist) {
				$adminID = 1;
				$dummyListsNames = ['Lista 1 (publica)', 'Lista 2 (privada)', 'Lista 3 (Si no hay lista 2 va bien)', 'Lista 4', 'ADMIN_DUMMY_LIST'];
				$dummyListsVisibility = ['public', 'private', 'public', 'public', 'private'];

				$stmt = $this->conn->prepare("INSERT INTO lists (id_user, list_name, visibility) VALUES (:id_user, :list_name, :visibility)");
				$stmt->bindParam(':id_user', $adminID); //default 1, porque solo vamos a tener admin asegurado
				for($i = 0; $i < count($dummyListsNames); $i++){
					$stmt->bindParam(':list_name', $dummyListsNames[$i]);
					$stmt->bindParam(':visibility', $dummyListsVisibility[$i]);
					$stmt->execute();
				}
				// echo "Listas de usuario dummy creadas exitosamente.";
			} else {
				// echo "Las listas de usuario dummy ya existen en la base de datos.";
			}
			} catch(PDOException $e) {
				echo "Error al crear las listas de usuario dummy: " . $e->getMessage();
		}
	}

    // create booklists table
    public function createBooksInListsTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS books_in_lists (
                id_bookInList INT AUTO_INCREMENT PRIMARY KEY,
                id_list INT NOT NULL,
                isbn VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (id_list) REFERENCES lists(id_list) ON DELETE CASCADE,
                FOREIGN KEY (isbn) REFERENCES books(isbn) ON DELETE CASCADE,
                UNIQUE (id_list, isbn)
            )";
            $this->conn->exec($sql);
            // echo "Tabla de listas de libros creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de listas de libros: " . $e->getMessage();
        }
    }

    // create userfollowlists table
    public function createUserFollowListsTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS user_follow_lists (
                id_followList INT AUTO_INCREMENT PRIMARY KEY,
                id_user INT NOT NULL,
                id_list INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
                FOREIGN KEY (id_list) REFERENCES lists(id_list) ON DELETE CASCADE,
                UNIQUE (id_user, id_list)
            )";
            $this->conn->exec($sql);
            // echo "Tabla de listas de usuario seguidas creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de listas de usuario seguidas: " . $e->getMessage();
        }
    }

	public function createDefaultListFollows(){
		try {
			$stmt = $this->conn->prepare("SELECT * FROM user_follow_lists");
			$stmt->execute();
			$defaultListFollowsExist = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!$defaultListFollowsExist) {
				$stmt = $this->conn->prepare("SELECT id_user FROM users WHERE role = 'user'");
				$stmt->execute();
				$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt = $this->conn->prepare("SELECT id_list FROM lists WHERE list_name = 'Lista 1 (publica)'");
				$stmt->execute();
				$lista_1 = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt = $this->conn->prepare("SELECT id_list FROM lists WHERE list_name = 'Lista 3 (Si no hay lista 2 va bien)'");
				$stmt->execute();
				$lista_3 = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt = $this->conn->prepare("SELECT id_list FROM lists WHERE list_name = 'Lista 4'");
				$stmt->execute();
				$lista_4 = $stmt->fetch(PDO::FETCH_ASSOC);

				$stmt = $this->conn->prepare("INSERT INTO user_follow_lists (id_user, id_list) VALUES (:id_user, :id_list)");
				for ($i = 0; $i < count($users); $i++) {
					$stmt->bindParam(':id_user', $users[$i]['id_user']);
					$stmt->bindParam(':id_list', $lista_1['id_list']);
					$stmt->execute();
				}
				for ($i = 0; $i < (count($users) - 1); $i++) {
					$stmt->bindParam(':id_user', $users[$i]['id_user']);
					$stmt->bindParam(':id_list', $lista_4['id_list']);
					$stmt->execute();
				}
				for ($i = 0; $i < (count($users) - 2); $i++) {
					$stmt->bindParam(':id_user', $users[$i]['id_user']);
					$stmt->bindParam(':id_list', $lista_3['id_list']);
					$stmt->execute();
				}
				// echo "Listas de usuario seguidas por defecto creadas exitosamente.";
			} else {
				// echo "Las listas de usuario seguidas por defecto ya existen en la base de datos.";
			}
			} catch(PDOException $e) {
				echo "Error al crear las listas de usuario seguidas por defecto: " . $e->getMessage();
		}
	}

	public function createExtraBooks(){
		try {
			$stmt = $this->conn->prepare("SELECT * FROM books");
			$stmt->execute();
			$existingBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if (count($existingBooks) < 10) {
				$books = [
					['isbn' => '9788423342430', 'title' => 'El nombre del viento', 'author' => 'Patrick Rothfuss', 'genre' => 'Fantasía', 'editorial' => 'Destino', 'image' => 'img/el_nombre_del_viento.jpg'],
					['isbn' => '9788401334107', 'title' => 'El temor de un hombre sabio', 'author' => 'Patrick Rothfuss', 'genre' => 'Fantasía', 'editorial' => 'Destino', 'image' => 'img/el_temor_de_un_hombre_sabio.jpg'],
					['isbn' => '9788498382549', 'title' => 'La música del silencio', 'author' => 'Patrick Rothfuss', 'genre' => 'Fantasía', 'editorial' => 'Plaza & Janés', 'image' => 'img/la_musica_del_silencio.jpg'],
					['isbn' => '9788497935194', 'title' => 'Harry Potter y la piedra filosofal', 'author' => 'J.K. Rowling', 'genre' => 'Fantasía', 'editorial' => 'Salamandra', 'image' => 'img/harry_potter_y_la_piedra_filosofal.jpg'],
					['isbn' => '9780345803481', 'title' => '1984', 'author' => 'George Orwell', 'genre' => 'Ciencia ficción', 'editorial' => 'Penguin Books', 'image' => 'img/1984.jpg'],
					['isbn' => '9780307476463', 'title' => 'Juego de tronos', 'author' => 'George R.R. Martin', 'genre' => 'Fantasía épica', 'editorial' => 'Bantam Books', 'image' => 'img/juego_de_tronos.jpg'],
					['isbn' => '9780060558122', 'title' => 'El código Da Vinci', 'author' => 'Dan Brown', 'genre' => 'Misterio', 'editorial' => 'Doubleday', 'image' => 'img/el_codigo_da_vinci.jpg'],
					['isbn' => '9780140280197', 'title' => 'El gran Gatsby', 'author' => 'F. Scott Fitzgerald', 'genre' => 'Ficción', 'editorial' => 'Charles Scribner\'s Sons', 'image' => 'img/el_gran_gatsby.jpg'],
					['isbn' => '9780671027346', 'title' => 'Orgullo y prejuicio', 'author' => 'Jane Austen', 'genre' => 'Romance', 'editorial' => 'Thomas Egerton', 'image' => 'img/orgullo_y_prejuicio.jpg'],
					['isbn' => '9780316769532', 'title' => 'Matar a un ruiseñor', 'author' => 'Harper Lee', 'genre' => 'Novela', 'editorial' => 'J.B. Lippincott & Co.', 'image' => 'img/matar_a_un_ruisenor.jpg']
				];
				$stmt = $this->conn->prepare("INSERT INTO books (isbn, title, author, genre, editorial, image) VALUES (:isbn, :title, :author, :genre, :editorial, :image)");
				foreach($books as $book){
					$stmt->bindParam(':isbn', $book['isbn']);
					$stmt->bindParam(':title', $book['title']);
					$stmt->bindParam(':author', $book['author']);
					$stmt->bindParam(':genre', $book['genre']);
					$stmt->bindParam(':editorial', $book['editorial']);
					$stmt->bindParam(':image', $book['image']);
					$stmt->execute();
				}
			} else {
				// echo "Ya existen libros extra en la base de datos.";
			}				
		} catch(PDOException $e) {
			echo "Error al crear los libros extra: " . $e->getMessage();
		}
	}

	public function createBooksInLists(){
		try{
			$stmt = $this->conn->prepare("SELECT * FROM books_in_lists");
			$stmt->execute();
			$existingBooksInLists = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if (count($existingBooksInLists) < 10) {
				$stmt = $this->conn->prepare("SELECT id_list FROM lists WHERE list_name = 'Lista 1 (publica)'");
				$stmt->execute();
				$lista_1 = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt = $this->conn->prepare("SELECT id_list FROM lists WHERE list_name = 'Lista 3 (Si no hay lista 2 va bien)'");
				$stmt->execute();
				$lista_3 = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt = $this->conn->prepare("SELECT id_list FROM lists WHERE list_name = 'Lista 4'");
				$stmt->execute();
				$lista_4 = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt = $this->conn->prepare("SELECT isbn FROM books");
				$stmt->execute();
				$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$stmt = $this->conn->prepare("INSERT INTO books_in_lists (id_list, isbn) VALUES (:id_list, :isbn)");
				for ($i = 0; $i < count($books); $i++) {
					$stmt->bindParam(':id_list', $lista_1['id_list']);
					$stmt->bindParam(':isbn', $books[$i]['isbn']);
					$stmt->execute();
				}
				for ($i = 0; $i < count($books); $i++) {
					$stmt->bindParam(':id_list', $lista_4['id_list']);
					$stmt->bindParam(':isbn', $books[$i]['isbn']);
					$stmt->execute();
				}
				for ($i = 0; $i < count($books); $i++) {
					$stmt->bindParam(':id_list', $lista_3['id_list']);
					$stmt->bindParam(':isbn', $books[$i]['isbn']);
					$stmt->execute();
				}
		} else {
			// echo "Ya existen libros en listas en la base de datos.";
		}
		} catch(PDOException $e) {
			echo "Error al crear los libros en listas: " . $e->getMessage();
		}
	}
}

?>