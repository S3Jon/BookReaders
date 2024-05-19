<?php

namespace models;
use PDO;

require_once "../app/config/Database.php";

use config\Database;
use PDOException;

class UFUmodel
{
	private $conn;
	private $table_name = "user_follow_users";

	public $id_followUser;
	public $id_user;
	public $id_followed;

	public function __construct()
	{
		$database = new Database();
		$this->conn = $database->getConnection();
	}

	public function followUser($id_user, $id_followed)
	{
		try {
			$query = "INSERT INTO " . $this->table_name . " (id_user, id_followed) VALUES (:id_user, :id_followed)";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->bindParam(':id_followed', $id_followed);
			$stmt->execute();
		} catch (PDOException $e) {
			echo "Error al seguir usuario: " . $e->getMessage();
			die();
		}
	}

	public function unfollowUser($id_user, $id_followed)
	{
		try {
			$query = "DELETE FROM " . $this->table_name . " WHERE id_user = :id_user AND id_followed = :id_followed";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->bindParam(':id_followed', $id_followed);
			$stmt->execute();
		} catch (PDOException $e) {
			echo "Error al dejar de seguir usuario: " . $e->getMessage();
			die();
		}
	}

	public function getFollowedUsers($id_user)
	{
		try {
			$query = "
				SELECT u.* 
				FROM user_follow_users ufu
				JOIN users u ON ufu.id_followed = u.id_user
				WHERE ufu.id_user = :id_user
			";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al obtener usuarios seguidos: " . $e->getMessage();
			die();
		}
	}

	public function getFollowers($id_user)
	{
		try {
			$query = "
				SELECT u.* 
				FROM user_follow_users ufu
				JOIN users u ON ufu.id_user = u.id_user
				WHERE ufu.id_followed = :id_user
			";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al obtener seguidores: " . $e->getMessage();
			die();
		}
	}
}