<?php

require_once '../app/config/Database.php';

$database = new \config\Database();

// Create database
$database->createDatabase();

// Create tables
$database->createUsersTable();

$database->createFollowersTable();

$database->createBooksTable();

$database->createGenresTable();

$database->createReviewsTable();

$database->createListsTable();

$database->createBooksInListsTable();

$database->createUserFollowListsTable();

// Create default admin user
$database->createDefaultAdminUser();

// Default lists
$database->insertDummyLists();

// Default genres
$database->insertDefaultGenres();

// Extra users
$database->createExtraUsers();

// Extra follows
$database->createDefaultListFollows();

// Extra books
$database->createExtraBooks(); //merge with createExtraBooks

// Books in lists
$database->createBooksInLists();

$database->closeConnection();

?>
