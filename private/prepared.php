<?php

// insert
$insert_statement = $connection->prepare("INSERT INTO manga_data (title, writer, synopsis, publisher, year, genre, art, rating) VALUES (?, ?, ?, ?, ?, ?, ?, ?);");

// update
$update_statement = $connection->prepare("UPDATE manga_data SET title = ?, writer = ?, synopsis = ?, publisher = ?, year = ?, genre = ?, rating = ? WHERE manga_id = ?;");

// delete
$delete_statement = $connection->prepare("DELETE FROM manga_data WHERE manga_id = ?;");

// select all
$select_statement = $connection->prepare("SELECT * FROM manga_data;");

// specific select
$specific_select_statement = $connection->prepare("SELECT * FROM manga_data WHERE manga_id = ?;");


?>