<?php
require_once('/home/apedro3/data/connect.php');
$connection = db_connect();

$title = "Your Manga List";
include('includes/header.php');

include('includes/functions.php');

if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $query = "SELECT id FROM catalogue_admin WHERE username = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $username);

    if($stmt->execute()) {
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];

            $list_query = "SELECT id, title, writer, image_filename FROM user_manga_list WHERE user_id = ?";
            $list_stmt = $connection->prepare($list_query);
            $list_stmt->bind_param("i", $user_id);

            if($list_stmt->execute()) {
                $list_result = $list_stmt->get_result();

                echo "<h2>Your Manga List</h2>";

                if($list_result->num_rows > 0) {
                    echo "<ul class=list-group>";
                    while($row = $list_result->fetch_assoc()) {
                        echo "<li class=list-group-item><img src='images/full_resized/{$row['image_filename']}' alt='{$row['title']}' /></li>";
                        echo "<li class=list-group-item>Title: {$row['title']}</li>";
                        echo "<li class=list-group-item>Writer: {$row['writer']}</li>";
                        echo "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "Your manga list is empty.";
                }
            } else {
                echo "Error retrieving manga list: ".$list_stmt->error;
            }

            $list_stmt->close();
        } else {
            echo "User not found.";
        }
    } else {
        echo "Error getting user information: ".$stmt->error;
    }

    $stmt->close();
} else {
    echo "User not logged in.";
}

db_disconnect($connection);
?>