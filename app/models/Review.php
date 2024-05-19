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

	public function getReviewsByUser($id_user)
	{
		try {
			$query = "SELECT * FROM $this->table_name WHERE id_user = :id_user";
			$stmt = $this->conn->prepare($query);

			$this->id_user = htmlspecialchars(strip_tags($id_user));
			$stmt->bindParam(':id_user', $this->id_user);

			$stmt->execute();

			return $stmt;
		} catch (PDOException $e) {
			echo "Error al obtener reseñas: " . $e->getMessage();
			die();
		}
	}

	public function getPublicReviewsByUser($id_user)
	{
		try {
			$query = "SELECT * FROM $this->table_name WHERE id_user = :id_user AND visibility = 'public'";
			$stmt = $this->conn->prepare($query);

			$this->id_user = htmlspecialchars(strip_tags($id_user));
			$stmt->bindParam(':id_user', $this->id_user);

			$stmt->execute();

			return $stmt;
		} catch (PDOException $e) {
			echo "Error al obtener reseñas: " . $e->getMessage();
			die();
		}
	}

	public function getBookAverageRating($isbn)
	{
		try {
			$query = "SELECT AVG(rating) as average_rating FROM $this->table_name WHERE isbn = :isbn";
			$stmt = $this->conn->prepare($query);

			$this->isbn = htmlspecialchars(strip_tags($isbn));
			$stmt->bindParam(':isbn', $this->isbn);

			$stmt->execute();

			return $stmt;
		} catch (PDOException $e) {
			echo "Error al obtener reseñas: " . $e->getMessage();
			die();
		}
	}

	public function getReview($id_review)
	{
		try {
			$query = "SELECT * FROM $this->table_name WHERE id_review = :id_review";
			$stmt = $this->conn->prepare($query);

			$this->id_review = htmlspecialchars(strip_tags($id_review));
			$stmt->bindParam(':id_review', $this->id_review);

			$stmt->execute();

			return $stmt;
		} catch (PDOException $e) {
			echo "Error al obtener reseña: " . $e->getMessage();
			die();
		}
	}

	public function getReviewsByBook($isbn)
	{
		try {
			$query = "SELECT * FROM $this->table_name WHERE isbn = :isbn";
			$stmt = $this->conn->prepare($query);

			$this->isbn = htmlspecialchars(strip_tags($isbn));
			$stmt->bindParam(':isbn', $this->isbn);

			$stmt->execute();

			return $stmt;
		} catch (PDOException $e) {
			echo "Error al obtener reseñas: " . $e->getMessage();
			die();
		}
	}



}

?>