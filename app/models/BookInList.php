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

	public function getlistbooks($id_list){
		$query = "SELECT * FROM " . $this->table_name . " WHERE id_list = :id_list";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id_list', $id_list);
		$stmt->execute();
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row;
	}

	public function getBILCount($id_list){
		$query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE id_list = :id_list";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id_list', $id_list);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	//Llamarla desde la pagina de los libros; a menos que queramos añadir libros desde la lista con un buscador o algo?
	public function addBook($id_list, $isbn){
		$query = "INSERT INTO " . $this->table_name . " (id_list, isbn) VALUES (:id_list, :isbn)";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id_list', $id_list);
		$stmt->bindParam(':isbn', $isbn);
		$stmt->execute();
	}

	public function removeBook($id_list, $isbn){
		$query = "DELETE FROM " . $this->table_name . " WHERE id_list = :id_list AND isbn = :isbn";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id_list', $id_list);
		$stmt->bindParam(':isbn', $isbn);
		$stmt->execute();
		if ($stmt->rowCount() > 0){
			return true;
		} else {
			return false;
		}
	}

}
?>