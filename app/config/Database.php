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

	// create genres table
	public function createGenresTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS book_genres (
                id_genre INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) UNIQUE NOT NULL
            )";
            $this->conn->exec($sql);
            // echo "Tabla de géneros creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de géneros: " . $e->getMessage();
        }
    }

	// create reviews table
    public function createReviewsTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS book_reviews (
                id_review INT AUTO_INCREMENT PRIMARY KEY,
                id_user INT NOT NULL,
                isbn VARCHAR(255) NOT NULL,
                rating INT NOT NULL,
                comment TEXT NOT NULL,
                visibility ENUM('public','private') DEFAULT 'private',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (id_user) REFERENCES users(id_user),
                FOREIGN KEY (isbn) REFERENCES books(isbn)
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
				list_description VARCHAR(300),
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

	public function insertDefaultLists(){
		try {
			$stmt = $this->conn->prepare("SELECT * FROM lists");
			$stmt->execute();
			$dummyListsExist = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!$dummyListsExist) {
				$adminID = 1;
				$dummyListsNames = ['Lista 1 (publica)', 'Lista 2 (privada)', 'Lista 3 (Si no hay lista 2 va bien)', 'Lista 4', 'ADMIN_DUMMY_LIST'];
				$dummyListsDescriptions = ['Lista pública de prueba', 'Lista privada de prueba', 'Lista pública de prueba', 'Lista privada de prueba', 'Lista de prueba del admin'];
				$dummyListsVisibility = ['public', 'private', 'public', 'public', 'private'];

				$stmt = $this->conn->prepare("INSERT INTO lists (id_user, list_name, list_description, visibility) VALUES (:id_user, :list_name, :list_description, :visibility)");
				$stmt->bindParam(':id_user', $adminID); //default 1, porque solo vamos a tener admin asegurado
				for($i = 0; $i < count($dummyListsNames); $i++){
					$stmt->bindParam(':list_name', $dummyListsNames[$i]);
					$stmt->bindParam(':list_description', $dummyListsDescriptions[$i]);
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

	// create userfollowuser table
	public function createUserFollowUsersTable(){
		try {
			$sql = "CREATE TABLE IF NOT EXISTS user_follow_users (
				id_follow INT AUTO_INCREMENT PRIMARY KEY,
				id_user INT NOT NULL,
				id_followed INT NOT NULL,
				created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
				FOREIGN KEY (id_followed) REFERENCES users(id_user) ON DELETE CASCADE,
				UNIQUE (id_user, id_followed)
			)";
			$this->conn->exec($sql);
			// echo "Tabla de usuarios seguidos creada exitosamente.";
		} catch(PDOException $e) {
			echo "Error al crear la tabla de usuarios seguidos: " . $e->getMessage();
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

	// create default books
	public function createDefaultBooks(){
		try {

			$stmt = $this->conn->prepare("SELECT * FROM books");
			$stmt->execute();
			$existingBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if($existingBooks){
				// echo "Ya existen libros en la base de datos.";
				return;
			}

			$sql_queries = [
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS123456789', 'The Girl He Never Noticed', 'Neilani Alejandrino', 'Wattpadd', 'uploads/The_Girl_He_Never_Noticed.jpg', '[\"Romance\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS234567890', 'A different virus: Heartfire', 'Crystal Scherer', 'Wattpadd', 'uploads/A_Different_Virus.jpg', '[\"Science Fiction\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS345678901', 'Rapture', 'Athena', 'Wattpadd', 'uploads/Rapture.jpg', '[\"Fantasy\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS456789012', 'Night Shift', 'Annie Crown', 'Wattpadd', 'uploads/Night_Shift.jpg', '[\"Thriller\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS567890123', 'The mistery fighter', 'Aleksandra Elin', 'Wattpadd', 'uploads/The_Mistery_Fighter.jpg', '[\"Mystery\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS678901234', 'Plunder', 'R. S. Kovach', 'Wattpadd', 'uploads/Plunder.jpg', '[\"Adventure\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS789012345', 'Of Rust and Gold', 'Aliston J. Drake', 'Wattpadd', 'uploads/Of_Rust_And_gold.jpg', '[\"Historical Fiction\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS890123456', 'The long way back', 'Winifred Bates', 'Wattpadd', 'uploads/The_Long_Way_Back.jpg', '[\"Drama\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS901234567', 'Orc wars: Uprising', 'Ghost Lord', 'Wattpadd', 'uploads/Orc_Wars_Uprising.jpg', '[\"Fantasy\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS012345678', 'Four walls', 'M. C. Roman', 'Wattpadd', 'uploads/Four_Walls.jpg', '[\"Romance\"]')",
			];
	
			// Ejecutar las consultas SQL
			foreach ($sql_queries as $query) {
				$this->conn->exec($query);
			}

		} catch(PDOException $e) {
			echo "Error al añadir los libros por defecto: " . $e->getMessage();
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

	// insert default genres
	public function insertDefaultGenres(){
		try {
			$sql_queries = [
				"INSERT INTO book_genres (name) VALUES ('Romance') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Science Fiction') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Fantasy') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Thriller') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Mystery') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Adventure') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Historical Fiction') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Drama') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Horror') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Biography') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Science') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Self-Help') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Cooking') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Travel') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('History') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Children') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Religion') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Aliens') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Vampires') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Werewolves') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Zombies') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Post-Apocalyptic') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Dystopian') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Time Travel') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Superheroes') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Magic') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Dragons') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Witches') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Fairies') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Angels') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Demons') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Adventures') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Academic') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Adolescence') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Adult') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Adult-fiction') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Anime') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Art') ON DUPLICATE KEY UPDATE id_genre = id_genre",
			];

			// Ejecutar las consultas SQL
			foreach ($sql_queries as $query) {
				$this->conn->exec($query);
			}
		} catch(PDOException $e) {
			echo "Error al añadir los géneros por defecto: " . $e->getMessage();
		}
	}

	public function createUserFollows() {
		try {
			$stmt = $this->conn->prepare("SELECT COUNT(*) as follow_count FROM user_follow_users");
			$stmt->execute();
			$UFUexists = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($UFUexists['follow_count'] < 10) {
				$stmt = $this->conn->prepare("SELECT id_user FROM users WHERE role != 'admin'");
				$stmt->execute();
				$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$stmt = $this->conn->prepare("INSERT INTO user_follow_users (id_user, id_followed) VALUES (:id_user, :id_followed)");

				$stmt->bindParam(':id_user', $id_user);
				$stmt->bindParam(':id_followed', $id_followed);

				foreach ($users as $user) {
					foreach ($users as $followed) {
						if ($user['id_user'] != $followed['id_user']) {
							$id_user = $user['id_user'];
							$id_followed = $followed['id_user'];
							$stmt->execute();
						}
					}
				}
			} else {
				// echo "Ya existen usuarios seguidos en la base de datos.";
			}
		} catch (PDOException $e) {
			echo "Error al crear los usuarios seguidos: " . $e->getMessage();
		}
	}

	public function createReviews(){
		try {
			$stmt = $this->conn->prepare("SELECT * FROM book_reviews");
			$stmt->execute();
			$existingReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (count($existingReviews) < 10) {
				$stmt = $this->conn->prepare("SELECT id_user FROM users WHERE role = 'user'");
				$stmt->execute();
				$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt = $this->conn->prepare("SELECT isbn FROM books");
				$stmt->execute();
				$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$stmt = $this->conn->prepare("INSERT INTO book_reviews (id_user, isbn, rating, comment, visibility) VALUES (:id_user, :isbn, :rating, :comment, :visibility)");
				$comments = [
					"Me ha encantado este libro, lo recomiendo a todo el mundo.",
					"Un libro muy interesante, aunque el final me ha dejado un poco frío.",
					"Un libro que no me ha gustado nada, no lo recomendaría.",
					"Un libro que me ha sorprendido gratamente, lo recomendaría a todo el mundo.",
					"Un libro que me ha dejado indiferente, ni fu ni fa.",
					"Un libro que me ha parecido muy aburrido, no lo recomendaría.",
					"Un libro que me ha parecido muy interesante, lo recomendaría a todo el mundo.",
					"Un libro que me ha parecido muy malo, no lo recomendaría.",
					"Un libro que me ha parecido muy bueno, lo recomendaría a todo el mundo.",
					"Un libro que me ha parecido muy regular, no lo recomendaría."
				];
				$review_visibility = 'public';
				$review_sc = [1, 2, 3, 4, 5];
				foreach ($users as $user) {
					foreach ($books as $book) {
						$stmt->bindParam(':id_user', $user['id_user']);
						$stmt->bindParam(':isbn', $book['isbn']);
						$stmt->bindParam(':rating', $review_sc[rand(0, 4)]);
						$stmt->bindParam(':comment', $comments[rand(0, 9)]);
						$stmt->bindParam(':visibility', $review_visibility);
						$stmt->execute();
					}
				}
			} else {
				// echo "Ya existen reseñas en la base de datos.";
			}
		}catch(PDOException $e) {
			echo "Error al crear las reseñas: " . $e->getMessage();
		}
	}

	// create usrebooks table
	public function createUserBooksTable(){
		try {
			$sql = "CREATE TABLE IF NOT EXISTS user_books (
				id_user INT NOT NULL,
				isbn VARCHAR(255) NOT NULL,
				tags VARCHAR(255),
				created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				FOREIGN KEY (id_user) REFERENCES users(id_user),
				FOREIGN KEY (isbn) REFERENCES books(isbn),
				PRIMARY KEY (id_user, isbn)
			)";
			$this->conn->exec($sql);
			// echo "Tabla de libros de usuario creada exitosamente.";
		} catch(PDOException $e) {
			echo "Error al crear la tabla de libros de usuario: " . $e->getMessage();
		}
	}
}
?>