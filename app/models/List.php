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

	public function getPublicLists()
	{
		try {
			//temporalmente ordernar por fecha de creacion, luego se ordenara por seguidores
			$query = 'SELECT * FROM ' . $this->table . ' WHERE visibility = "public" ORDER BY created_at DESC';
			$stmt = $this->conn->prepare($query);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo $e->getMessage();
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
			echo "Error al borrar la lista"; $e->getMessage();
		}
	}

	public function createList($id_user, $list_name, $visibility) //createList($id_user, $list_name, $visibility)
	{
		try {
			$query = 'INSERT INTO ' . $this->table . ' (id_user, list_name, visibility) VALUES (:id_user, :list_name, :visibility)';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', 1); //default 1, cambiar a futuro
			$stmt->bindParam(':list_name', $list_name);
			$stmt->bindParam(':visibility', $visibility);
			$stmt->execute();
			return true;
		} catch (PDOException $e) {
			echo "Error al crear la lista"; $e->getMessage();
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

	//WIP
	/*
	public function getBooksInList($id_list)
	{
		try {
			$query = 'SELECT [WIP- BOOKLIST] FROM ' . $this->table . ' WHERE id_list = :id_list';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	*/
}