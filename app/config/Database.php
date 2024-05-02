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

    // create followers table
    public function createFollowersTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS followers (
                id_follower INT AUTO_INCREMENT PRIMARY KEY,
                id_user INT NOT NULL,
                id_followed INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (id_user) REFERENCES users(id_user),
                FOREIGN KEY (id_followed) REFERENCES users(id_user),
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
                type ENUM('favorite', 'read', 'want_to_read','reading','dropped') DEFAULT NULL,
                visibility ENUM('public','private') DEFAULT 'private',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (id_user) REFERENCES users(id_user)
            )";
            $this->conn->exec($sql);
            // echo "Tabla de listas de usuario creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de listas de usuario: " . $e->getMessage();
        }
    }

	public function insertDummyLists(){
		try {
			$stmt = $this->conn->prepare("SELECT * FROM lists WHERE list_name = 'ADMIN_DUMMY_LIST'");
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
			}catch(PDOException $e) {
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
                FOREIGN KEY (id_list) REFERENCES lists(id_list),
                FOREIGN KEY (isbn) REFERENCES books(isbn),
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
                FOREIGN KEY (id_user) REFERENCES users(id_user),
                FOREIGN KEY (id_list) REFERENCES lists(id_list),
                UNIQUE (id_user, id_list)
            )";
            $this->conn->exec($sql);
            // echo "Tabla de listas de usuario seguidas creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de listas de usuario seguidas: " . $e->getMessage();
        }
    }
}

?>