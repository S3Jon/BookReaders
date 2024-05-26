<?php

namespace models;
use PDO;

require_once "../app/config/Database.php";

use config\Database;
use PDOException;

class BILModel
{  
    private $conn;
    private $table_name = "books_in_lists";

    // Propiedades del usuario
    public $id_bookInList;
	public $id_list;
	public $isbn;

    //Constructor to connect to the database
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

	public function getListBooksInfo($id_list)
	{
		try {
			$query = "
				SELECT books.*, books_in_lists.id_list, COALESCE(AVG(book_reviews.rating), 0) AS average_rating
				FROM " . $this->table_name ."
				JOIN books ON books_in_lists.isbn = books.isbn
				LEFT JOIN book_reviews ON books.isbn = book_reviews.isbn
				WHERE books_in_lists.id_list = :id_list
				GROUP BY books.isbn, books_in_lists.id_list
				ORDER BY books_in_lists.isbn ASC";
	
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al recuperar los libros de la lista: " . $e->getMessage();
		}
	}

	public function getBILCount($id_list){
		try{
			$query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE id_list = :id_list";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			return $row;
		} catch (PDOException $e){
			echo "Error al cargar # de libros en la lista: " . $e->getMessage();
			die();
		}
	}

	//Llamarla desde la pagina de los libros; a menos que queramos añadir libros desde la lista con un buscador o algo?
	public function addBook($id_list, $isbn){
		try {
			$query = "INSERT INTO " . $this->table_name . " (id_list, isbn) VALUES (:id_list, :isbn)";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->bindParam(':isbn', $isbn);
			$stmt->execute();
		} catch (PDOException $e){
			echo "Error al añadir libro a la lista: " . $e->getMessage();
			die();
		}
	}

	//Igual renombrar a removeBookFromList o algo asi
	public function removeBook($id_list, $isbn){
		try {
			$query = "DELETE FROM " . $this->table_name . " WHERE id_list = :id_list AND isbn = :isbn";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->bindParam(':isbn', $isbn);
			$stmt->execute();

			return true;
		} catch (PDOException $e){
			echo "Error al eliminar libro de la lista: " . $e->getMessage();
			die();
		}
	}

}
?>