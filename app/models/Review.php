<?php

namespace models;
use PDO;

require_once "../app/config/Database.php";

use config\Database;
use PDOException;

class User
{  
    private $conn;
    private $table_name = "book_reviews";

    public $id_review;
    public $id_user;
    public $isbn;
    public $rating;
    public $comment;
    public $visibility;

    public function __construct()
    {
        $this->conn = (new Database())->getConnection();
    }

    public function createReview()
    {
        try {
            $query = "INSERT INTO $this->table_name (id_user, isbn, rating, comment, visibility) VALUES (:id_user, :isbn, :rating, :comment, :visibility)";
            $stmt = $this->conn->prepare($query);

            $this->id_user = htmlspecialchars(strip_tags($this->id_user));
            $this->isbn = htmlspecialchars(strip_tags($this->isbn));
            $this->rating = htmlspecialchars(strip_tags($this->rating));
            $this->comment = htmlspecialchars(strip_tags($this->comment));
            $this->visibility = htmlspecialchars(strip_tags($this->visibility));

            $stmt->bindParam(':id_user', $this->id_user);
            $stmt->bindParam(':isbn', $this->isbn);
            $stmt->bindParam(':rating', $this->rating);
            $stmt->bindParam(':comment', $this->comment);
            $stmt->bindParam(':visibility', $this->visibility);

            if ($stmt->execute()) {
                return true;
            }

            return false;

        } catch (PDOException $e) {
            echo "Error al crear reseña: " . $e->getMessage();
            die();
        }
    }

}

?>