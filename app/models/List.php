<?php

namespace models;
use PDO;

require_once "../app/config/Database.php";

use config\Database;
use PDOException;

class ListModel //List está reservado por PHP
{
	private $conn;
	private $table = 'lists';

	public $id_list;
	public $id_user;
	public $list_name;
	// public $type;	   //le importa al usuario?
	public $visibility;
	public $created_at;
	//public $updated_at;  //no se si es necesario

	// Conn
	public function __construct()
	{
		$db = new Database();
		$this->conn = $db->getConnection();
	}

	//Actualmente en desuso; mirar cómo reciclar o si borrar
	public function getPublicLists()
	{
		try {
			//temporalmente ordernar por fecha de creacion, luego se ordenara por seguidores
			//type IS NULL para que no muestre listas básicas, las creadas por usuarios tendrán type NULL
			$query = 'SELECT * FROM ' . $this->table . ' WHERE visibility = "public" AND type IS NULL ORDER BY created_at DESC'; 
			$stmt = $this->conn->prepare($query);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al cargar listas publicas: " . $e->getMessage();
		}
	}

	public function deleteList($id_list)
	{
		try {
			$query = 'DELETE FROM ' . $this->table . ' WHERE id_list = :id_list';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->execute();
			return true;
		} catch (PDOException $e) {
			echo "Error al borrar la lista: " . $e->getMessage();
		}
	}

	public function createList($id_user, $list_name, $visibility) //createList($id_user, $list_name, $visibility)
	{
		try {
			$query = 'INSERT INTO ' . $this->table . ' (id_user, list_name, visibility) VALUES (:id_user, :list_name, :visibility)';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user); //default 1, cambiar a futuro
			$stmt->bindParam(':list_name', $list_name);
			$stmt->bindParam(':visibility', $visibility);
			$stmt->execute();
			return true;
		} catch (PDOException $e) {
			echo "Error al crear la lista: " . $e->getMessage();
		}
	}

	public function getListById($id_list)
	{
		try {
			$query = 'SELECT * FROM ' . $this->table . ' WHERE id_list = :id_list';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al recuperar los datos de la lista: " . $e->getMessage();
		}

	}

	public function getUserBasicLists($id_user)
	{
		try {
			$query = 'SELECT * FROM ' . $this->table . ' WHERE id_user = :id_user AND type IS NOT NULL';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al recuperar las listas básicas del usuario: " . $e->getMessage();
		}
	}

	//esta devuelve todas sus listas; list.php ya mira si es publica/privada y el que la mira es el propietario
	public function getUserLists($id_user)
	{
		try {
			$query = 'SELECT * FROM ' . $this->table . ' WHERE id_user = :id_user AND type IS NULL';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al recuperar las listas del usuario: " . $e->getMessage();
		}
	}

	public function createBasicLists($id_user)
	{
		$visDB = 'public';
		$typeDB = ['favorite', 'read', 'want_to_read', 'reading', 'dropped'];
		$listNameDB = ['Libros favoritos', 'Leídos', 'Por Leer', 'Leyendo', 'Abandonados'];
		try {
			$query = 'INSERT INTO ' . $this->table . ' (id_user, list_name, type, visibility) VALUES (:id_user, :list_name, :type, :visibility)';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->bindParam(':visibility', $visDB);
			for ($i = 0; $i < count($typeDB); $i++) {
				$stmt->bindParam(':list_name', $listNameDB[$i]);
				$stmt->bindParam(':type', $typeDB[$i]);
				$stmt->execute();
			}
			return true;
		} catch (PDOException $e) {
			echo "Error al crear las listas básicas: " . $e->getMessage();
		}
	}

	public function getListName($id_list)
	{
		try {
			$query = 'SELECT list_name FROM ' . $this->table . ' WHERE id_list = :id_list';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al recuperar el nombre de la lista: " . $e->getMessage();
		}
	}

	public function getListOwnerID($id_list)
	{
		try {
			$query = 'SELECT id_user FROM ' . $this->table . ' WHERE id_list = :id_list';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al recuperar el propietario de la lista: " . $e->getMessage();
		}
	}

	public function getMostFollowed()
	{
		try {
			$query = "SELECT lists.*, COUNT(user_follow_lists.id_list) AS followersNum
					  FROM lists 
					  LEFT JOIN user_follow_lists ON lists.id_list = user_follow_lists.id_list 
					  WHERE lists.visibility = 'public' 
					  AND lists.type IS NULL
					  GROUP BY lists.id_list 
					  ORDER BY COUNT(user_follow_lists.id_list) DESC 
					  LIMIT 50";
			$stmt = $this->conn->prepare($query);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $rows;
		} catch (PDOException $e) {
			echo "Error al obtener listas más seguidas: " . $e->getMessage();
			die();
		}
	}
	

	//Para un futuro
	/* 
	public function getListByUID($id_user)
	{
		try {
			$query = 'SELECT * FROM ' . $this->table . ' WHERE id_user = :id_user';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	*/
}