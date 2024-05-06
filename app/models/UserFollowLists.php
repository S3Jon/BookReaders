<?php

namespace models;
use PDO;

require_once "../app/config/Database.php";

use config\Database;
use PDOException;

class UFLModel
{
	private $conn;
	private $table_name = "user_follow_lists";

	public $id_followList;
	public $id_user;
	public $id_list;

	public function __construct()
	{
		$database = new Database();
		$this->conn = $database->getConnection();
	}

	public function followList($id_user, $id_list)
	{
		$query = "INSERT INTO " . $this->table_name . " (id_user, id_list) VALUES (:id_user, :id_list)";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id_user', $id_user);
		$stmt->bindParam(':id_list', $id_list);
		$stmt->execute();
	}

	public function unfollowList($id_user, $id_list)
	{
		$query = "DELETE FROM " . $this->table_name . " WHERE id_user = :id_user AND id_list = :id_list";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id_user', $id_user);
		$stmt->bindParam(':id_list', $id_list);
		$stmt->execute();
	}

	public function getFollowedLists($id_user)
	{
		$query = "SELECT * FROM " . $this->table_name . " WHERE id_user = :id_user";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id_user', $id_user);
		$stmt->execute();
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row;
	}

	public function isFollowing($id_user, $id_list)
	{
		$query = "SELECT COUNT(*) AS sigue_lista FROM " . $this->table_name . " WHERE id_user = :id_user AND id_list = :id_list";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id_user', $id_user);
		$stmt->bindParam(':id_list', $id_list);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		//soy más de devolver 0 o 1, pero así tiene más sentido
		return $row['sigue_lista'] > 0;
	}

	public function getFollowers($id_list)
	{
		$query = "SELECT COUNT(*) AS num_seguidores FROM " . $this->table_name . " WHERE id_list = :id_list";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id_list', $id_list);
		$stmt->execute();
		$followcount = $stmt->fetch(PDO::FETCH_ASSOC);
		return $followcount['num_seguidores'];
	}

	public function getMostFollowed()
	{
		//$tabla_listas = "lists";
		$query = "SELECT lists.id_list 
		FROM lists 
		LEFT JOIN user_follow_lists ON lists.id_list = user_follow_lists.id_list 
		WHERE lists.visibility = 'public' 
		AND lists.type IS NULL
		GROUP BY lists.id_list 
		ORDER BY COUNT(user_follow_lists.id_list) DESC 
		LIMIT 50;";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row;
	}
}