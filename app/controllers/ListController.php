<?php

namespace controllers;
use models\ListModel;

class ListController
{
	private $listModel;

	public function __construct(ListModel $list)
	{
		$this->listModel = new $list;
	}

	public function getPublicLists()
	{
		return $this->listModel->getPublicLists();
	}

	public function getListById($id_list)
	{
		if ($id_list == null) {
			echo "El ID de la lista no puede estar vacío";
			return false;
		}
		return $this->listModel->getListById($id_list);
	}

	public function deleteList($id_list)
	{
		if ($id_list == null) {
			echo "El ID de la lista no puede estar vacío";
			return false;
		}
		return $this->listModel->deleteList($id_list);
	}

	public function createList($id_user, $list_name, $visibility)
	{
		if ($id_user == null || $list_name == null || $visibility == null) {
			echo "Los campos no pueden estar vacíos";
			return false;
		}
		return $this->listModel->createList($id_user, $list_name, $visibility);
	}

	public function getUserBasicLists($id_user)
	{
		if ($id_user == null) {
			echo "El ID de usuario no puede estar vacío";
			return false;
		}
		return $this->listModel->getUserBasicLists($id_user);
	}

	public function getUserLists($id_user)
	{
		if ($id_user == null) {
			echo "El ID de usuario no puede estar vacío";
			return false;
		}
		return $this->listModel->getUserLists($id_user);
	}

	public function createBasicLists($id_user)
	{
		if ($id_user == null) {
			echo "El ID de usuario no puede estar vacío";
			return false;
		}
		return $this->listModel->createBasicLists($id_user);
	}

	public function exploreLists($id_list)
	{
		return $this->listModel->getListById($id_list);
	}
	
	public function getListName($id_list)
	{
		return $this->listModel->getListName($id_list);
	}

	public function getListOwnerID($id_list)
	{
		return $this->listModel->getListOwnerID($id_list);
	}

	public function getMostFollowed()
	{
		return $this->listModel->getMostFollowed();
	}

	public function updateList($id_list, $list_name, $list_description, $visibility)
	{
		return $this->listModel->updateList($id_list, $list_name, $list_description, $visibility);
	}
}