<?php

require_once '../app/config/Database.php';

$database = new \config\Database();

// Create database
$database->createDatabase();

// Create tables
$database->createUsersTable();

$database->createFollowersTable();

$database->createBooksTable();

$database->createReviewsTable();

$database->createListsTable();

$database->createBooksInListsTable();

// Create default admin user
$database->createDefaultAdminUser();

$database->closeConnection();

?>
