<?php
require_once('/home/apedro3/data/connect.php');
$connection = db_connect();

$title = "Add Manga to Your Read List";
include('includes/header.php');

if (isset($_SESSION['username']) && isset($_POST['add_to_list']) && isset($_POST['manga_id'])) {
    $username = $_SESSION['username'];
    $manga_id = $_POST['manga_id'];

    $query = "SELECT id FROM catalogue_admin WHERE username = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $username);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];

            $manga_query = "SELECT title, writer, image_filename FROM manga_data WHERE manga_id = ?";
            $manga_stmt = $connection->prepare($manga_query);
            $manga_stmt->bind_param("i", $manga_id);

            if ($manga_stmt->execute()) {
                $manga_result = $manga_stmt->get_result();

                if ($manga_result->num_rows > 0) {
                    $manga_data = $manga_result->fetch_assoc();
                    $title = $manga_data['title'];
                    $writer = $manga_data['writer'];
                    $image_filename = $manga_data['image_filename'];

                    $list_query = "INSERT INTO user_manga_list (user_id, manga_id, title, writer, image_filename) VALUES (?, ?, ?, ?, ?)";
                    $list_stmt = $connection->prepare($list_query);
                    $list_stmt->bind_param("iisss", $user_id, $manga_id, $title, $writer, $image_filename);

                    if ($list_stmt->execute()) {
                        (header('Location: view_list.php'));
                    } else {
                        echo "Error adding manga to the list: " . $list_stmt->error;
                    }

                    $list_stmt->close();
                } else {
                    echo "Manga not found.";
                }
            } else {
                echo "Error retrieving manga information: " . $manga_stmt->error;
            }

            $manga_stmt->close();
        } else {
            echo "User not found.";
        }
    } else {
        echo "Error getting user information: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

db_disconnect($connection);
?>
