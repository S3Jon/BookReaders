<?php

namespace models;
use PDO;

require_once "../app/config/Database.php";

use config\Database;
use PDOException;

class Review
{  
    private $conn;
    private $table_name = "book_reviews";

    // Propiedades de la reseña
    public $id_review;
    public $id_user;
    public $isbn;
    public $rating;
    public $comment;
    public $visibility;

    //Constructor to connect to the database
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new review
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET id_user=:id_user, isbn=:isbn, rating=:rating, comment=:comment, visibility=:visibility";
        $stmt = $this->conn->prepare($query);

        // Sanitize the data
        $this->id_user = htmlspecialchars(strip_tags($this->id_user));
        $this->isbn = htmlspecialchars(strip_tags($this->isbn));
        $this->rating = htmlspecialchars(strip_tags($this->rating));
        $this->comment = htmlspecialchars(strip_tags($this->comment));
        $this->visibility = htmlspecialchars(strip_tags($this->visibility));

        // Bind the data
        $stmt->bindParam(':id_user', $this->id_user);
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->bindParam(':rating', $this->rating);
        $stmt->bindParam(':comment', $this->comment);
        $stmt->bindParam(':visibility', $this->visibility);

        // Execute the query
        if($stmt->execute()){
            return true;
        } else {
            return false;
        }
    }

    // Check if a review already exists
    public function reviewExists($id_user, $isbn)
    {
        try {
            // Prepare the query to check if a review already exists
            $stmt = $this->conn->prepare("SELECT * FROM book_reviews WHERE id_user = :id_user AND isbn = :isbn");
            $stmt->bindParam(':id_user', $id_user);
            $stmt->bindParam(':isbn', $isbn);
            $stmt->execute();

            // Get the number of rows of execution results
            $num = $stmt->rowCount();

            // If the number of rows is greater than 0, the review already exists
            if($num > 0){
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al comprobar si la reseña ya existe: " . $e->getMessage();
            die();
        }
    }

    // Get all reviews for a book
    public function getReviews($isbn)
    {
        try {
            // Prepare the query to get all reviews for a book
            $stmt = $this->conn->prepare("SELECT * FROM book_reviews WHERE isbn = :isbn");
            $stmt->bindParam(':isbn', $isbn);
            $stmt->execute();

            // Get all rows of execution results as associative array
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener reseñas por ISBN: " . $e->getMessage();
            die();
        }
    }

}
?>