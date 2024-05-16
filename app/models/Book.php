<?php

namespace models;
use PDO;

require_once "../app/config/Database.php";

use config\Database;
use PDOException;

class Book
{  
    private $conn;
    private $table_name = "books";

    // Propiedades del libro
    public $id_book;
    public $isbn;
    public $title;
    public $author;
    public $genre;
    public $editorial;
    public $image;
    public $rating;
    // public $tags; //TODO implementar tags

    // Constructor para conectarse a la base de datos
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Método para crear un nuevo libro
    public function createBook() //$isbn, $title, $author, $genre, $editorial, $image
    {
        try {
            // verificamos si el ISBN ya existe
            $query = "SELECT * FROM " . $this->table_name . " WHERE isbn = :isbn";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':isbn', $isbn);
            $stmt->execute();
            if($stmt->rowCount() > 0){
                return false;
            }

            // Creamos la consulta para insertar los datos
            $query = "INSERT INTO " . $this->table_name . " (isbn, title, author, genre, editorial, image) VALUES (:isbn, :title, :author, :genre, :editorial, :image)";
            
            // Preparamos la sentencia SQL para insertar los datos
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':isbn', $this->isbn);
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':author', $this->author);
            $stmt->bindParam(':genre', $this->genre);
            $stmt->bindParam(':editorial', $this->editorial);
            $stmt->bindParam(':image', $this->image);

            if($stmt->execute()){
                return true;
            }

            return false;

        }catch(PDOException $e){
            echo "Error al crear un libro: " . $e->getMessage();
            die();
        }
    }

    // Método para obtener todos los libros
    public function getBooks()
    {
        try {
            $query = "SELECT b.*, AVG(r.rating) AS rating
                    FROM " . $this->table_name . " b
                    LEFT JOIN book_reviews r ON b.isbn = r.isbn
                    GROUP BY b.id_book";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener todos los libros: " . $e->getMessage();
            die();
        }
    }


    // Método para obtener un libro por su ID
    public function getBookById($id_book)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id_book = :id_book";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_book', $id_book);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener un libro por su ID: " . $e->getMessage();
            die();
        }
    }

    // Método para actualizar un libro
    public function updateBook($id_book, $isbn, $title, $author, $genre, $editorial, $image)
    {
        try {
            // Creamos la consulta para actualizar los datos
            $query = "UPDATE " . $this->table_name . " SET isbn = :isbn, title = :title, author = :author, genre = :genre, editorial = :editorial, image = :image WHERE id_book = :id_book";
            
            // Preparamos la sentencia SQL para actualizar los datos
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':id_book', $id_book);
            $stmt->bindParam(':isbn', $isbn);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':author', $author);
            $stmt->bindParam(':genre', $genre);
            $stmt->bindParam(':editorial', $editorial);
            $stmt->bindParam(':image', $image);

            if($stmt->execute()){
                return true;
            }

            return false;

        }catch(PDOException $e){
            echo "Error al actualizar un libro: " . $e->getMessage();
            die();
        }
    }

    // Método para eliminar un libro
    public function deleteBook($isbn)
    {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE isbn = :isbn";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':isbn', $isbn);

            if($stmt->execute()){
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            echo "Error al eliminar un libro: " . $e->getMessage();
            die();
        }
    }

    // Método para buscar libros por su título
    public function searchBooksByTitle($title)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE title LIKE :title";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':title', '%' . $title . '%');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al buscar libros por su título: " . $e->getMessage();
            die();
        }
    }

    // Método para buscar libros por su autor
    public function searchBooksByAuthor($author)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE author LIKE :author";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':author', '%' . $author . '%');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al buscar libros por su autor: " . $e->getMessage();
            die();
        }
    }

    // Método para buscar libros por su género
    public function searchBooksByGenre($genre)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE genre LIKE :genre";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':genre', '%' . $genre . '%');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al buscar libros por su género: " . $e->getMessage();
            die();
        }
    }

    // Método para buscar libros por su editorial
    public function searchBooksByEditorial($editorial)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE editorial LIKE :editorial";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':editorial', '%' . $editorial . '%');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al buscar libros por su editorial: " . $e->getMessage();
            die();
        }
    }

    // Método para buscar libros por su ISBN
    public function searchBooksByIsbn()
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE isbn LIKE :isbn";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':isbn', '%' . $this->isbn . '%');
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al buscar libros por su ISBN: " . $e->getMessage();
            die();
        }
    }

    // Método para buscar libros por su título y autor
    public function searchBooksByTitleAndAuthor($title, $author)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE title LIKE :title AND author LIKE :author";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':title', '%' . $title . '%');
            $stmt->bindValue(':author', '%' . $author . '%');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al buscar libros por su título y autor: " . $e->getMessage();
            die();
        }
    }

    // // Método para crear reviews de un libro
    // public function createReview($idUser, $isbn, $rating, $comment, $visibility)
    // {
    //     try {
    //         // Creamos la consulta para insertar los datos
    //         $query = "INSERT INTO reviews (idUser, isbn, rating, comment, visibility) VALUES (:idUser, :isbn, :rating, :comment, :visibility)";
            
    //         // Preparamos la sentencia SQL para insertar los datos
    //         $stmt = $this->conn->prepare($query);

    //         // Sanitizamos los datos
    //         $idUser = htmlspecialchars(strip_tags($idUser));
    //         $isbn = htmlspecialchars(strip_tags($isbn));
    //         $rating = htmlspecialchars(strip_tags($rating));
    //         $comment = htmlspecialchars(strip_tags($comment));
    //         $visibility = htmlspecialchars(strip_tags($visibility));

    //         // Vinculamos los datos
    //         $stmt->bindParam(':idUser', $idUser);
    //         $stmt->bindParam(':isbn', $isbn);
    //         $stmt->bindParam(':rating', $rating);
    //         $stmt->bindParam(':comment', $comment);
    //         $stmt->bindParam(':visibility', $visibility);

    //         // Ejecutamos la consulta
    //         if($stmt->execute()){
    //             return true;
    //         }

    //         return false;

    //     }catch(PDOException $e){
    //         echo "Error al crear una review: " . $e->getMessage();
    //         die();
    //     }
    // }

    // // Método para obtener las reviews de un libro
    // public function getReviews($isbn)
    // {
    //     try {
    //         $query = "SELECT * FROM reviews WHERE isbn = :isbn";
    //         $stmt = $this->conn->prepare($query);
    //         $stmt->bindParam(':isbn', $isbn);
    //         $stmt->execute();
    //         return $stmt->fetchAll(PDO::FETCH_ASSOC);
    //     } catch (PDOException $e) {
    //         echo "Error al obtener las reviews de un libro: " . $e->getMessage();
    //         die();
    //     }
    // }

    // // Método para obtener las reviews de un libro por su ID
    // public function getReviewsById($id_review)
    // {
    //     try {
    //         $query = "SELECT * FROM reviews WHERE id_review = :id_review";
    //         $stmt = $this->conn->prepare($query);
    //         $stmt->bindParam(':id_review', $id_review);
    //         $stmt->execute();
    //         return $stmt->fetch(PDO::FETCH_ASSOC);
    //     } catch (PDOException $e) {
    //         echo "Error al obtener las reviews de un libro por su ID: " . $e->getMessage();
    //         die();
    //     }
    // }

    // // Método para actualizar una review
    // public function updateReview($id_review, $idUser, $isbn, $rating, $comment, $visibility)
    // {
    //     try {
    //         // Creamos la consulta para actualizar los datos
    //         $query = "UPDATE reviews SET idUser = :idUser, isbn = :isbn, rating = :rating, comment = :comment, visibility = :visibility WHERE id_review = :id_review";
            
    //         // Preparamos la sentencia SQL para actualizar los datos
    //         $stmt = $this->conn->prepare($query);

    //         $stmt->bindParam(':id_review', $id_review);
    //         $stmt->bindParam(':idUser', $idUser);
    //         $stmt->bindParam(':isbn', $isbn);
    //         $stmt->bindParam(':rating', $rating);
    //         $stmt->bindParam(':comment', $comment);
    //         $stmt->bindParam(':visibility', $visibility);

    //         if($stmt->execute()){
    //             return true;
    //         }

    //         return false;

    //     }catch(PDOException $e){
    //         echo "Error al actualizar una review: " . $e->getMessage();
    //         die();
    //     }
    // }

    // // Método para eliminar una review
    // public function deleteReview($id_review)
    // {
    //     try {
    //         $query = "DELETE FROM reviews WHERE id_review = :id_review";
    //         $stmt = $this->conn->prepare($query);
    //         $stmt->bindParam(':id_review', $id_review);

    //         if($stmt->execute()){
    //             return true;
    //         }
            
    //         return false;
    //     } catch (PDOException $e) {
    //         echo "Error al eliminar una review: " . $e->getMessage();
    //         die();
    //     }
    // }

    // // Método para obtener las reviews de un libro por su visibilidad
    // public function getReviewsByVisibility($isbn, $visibility)
    // {
    //     try {
    //         $query = "SELECT * FROM reviews WHERE isbn = :isbn AND visibility = :visibility";
    //         $stmt = $this->conn->prepare($query);
    //         $stmt->bindParam(':isbn', $isbn);
    //         $stmt->bindParam(':visibility', $visibility);
    //         $stmt->execute();
    //         return $stmt->fetchAll(PDO::FETCH_ASSOC);
    //     } catch (PDOException $e) {
    //         echo "Error al obtener las reviews de un libro por su visibilidad: " . $e->getMessage();
    //         die();
    //     }
    // }
}