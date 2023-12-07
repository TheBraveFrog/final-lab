<?php

function count_manga() {
    global $connection;
    $sql = "SELECT COUNT(*) FROM manga_data";
    $results = mysqli_query($connection, $sql);
    $count = mysqli_fetch_row($results);
    return $count[0];

}

function find_manga($sort_order, $limit = 0, $offset = 0) {
    global $connection;
    $sql = "SELECT manga_id, title, writer, art FROM manga_data";

    if($limit > 0) {
        $sql .= " LIMIT ".$limit;
    }

    if($offset > 0) {
        $sql .= " OFFSET ".$offset;
    }

    $result = $connection->query($sql);
    return $result;
}

function find_manga_filter($sort_by, $sort_order, $limit = 0, $offset = 0) {
    global $connection;

    $sort_by = mysqli_real_escape_string($connection, $sort_by);
    $sort_order = strtoupper($sort_order);

    $allowed_columns = ['title', 'writer'];
    $allowed_orders = ['ASC', 'DESC'];

    if(!in_array($sort_by, $allowed_columns) || !in_array($sort_order, $allowed_orders)) {
        echo "<p>Invalid sorting column or order.</p>";
        return false;
    }

    $sql = "SELECT * FROM manga_data";

    if(!empty($sort_by) && !empty($sort_order)) {
        $sql .= " ORDER BY $sort_by $sort_order";
    }

    if($limit > 0) {
        $sql .= " LIMIT ".$limit;
    }
    if($offset > 0) {
        $sql .= " OFFSET ".$offset;
    }

    $result = $connection->query($sql);

    return $result;
}

function get_sort_order($column) {
    $currentSortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : '';

    $currentSortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

    $newSortOrder = ($currentSortBy === $column && $currentSortOrder === 'ASC') ? 'DESC' : 'ASC';

    return $newSortOrder;
}

function handle_database_error($statement) {
    global $connection;
    die("Error in: ".$statement.". Error details: ".$connection->error);
}

function get_all_manga_rand() {
    global $connection;

    global $select_statement;

    $select_statement = $connection->prepare("SELECT * FROM manga_data ORDER BY RAND() LIMIT 1");

    if(!$select_statement->execute()) {
        handle_database_error("fetching manga");
    } else {
        $result = $select_statement->get_result();
        $manga = [];
        while($row = $result->fetch_assoc()) {
            $manga[] = $row;
        }
        return $manga;
    }
}

function select_manga_by_id($manga_id) {
    global $connection;
    global $specific_select_statement;

    $specific_select_statement->bind_param("i", $manga_id);

    if(!$specific_select_statement->execute()) {
        handle_database_error("selecting manga by ID");
    }

    $result = $specific_select_statement->get_result();
    $specific_manga = $result->fetch_assoc();

    return $specific_manga;
}

function get_all_manga() {
    global $connection;
    global $select_statement;

    // error handling
    if(!$select_statement->execute()) {
        handle_database_error("fetching manga");
    } else {
        $result = $select_statement->get_result();
        $manga = [];
        while($row = $result->fetch_assoc()) {
            $manga[] = $row;
        }
        return $manga;
    }
}
function insert_manga($title, $writer, $synopsis, $publisher, $year, $genre, $art, $rating, $image_filename) {
    global $connection;
    global $insert_statement;

    $query = "INSERT INTO manga_data (title, writer, synopsis, publisher, year, genre, art, rating, image_filename) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if($insert_statement = $connection->prepare($query)) {
        $insert_statement->bind_param("ssssissis", $title, $writer, $synopsis, $publisher, $year, $genre, $art, $rating, $image_filename);

        if($insert_statement->execute()) {
        } else {
            handle_database_error("Error inserting manga: ".$insert_statement->error);
        }

        $insert_statement->close();
    } else {
        handle_database_error("Error preparing statement: ".$connection->error);
    }
}



function update_manga($title, $writer, $synopsis, $publisher, $year, $genre, $rating, $manga_id) {
    global $connection;
    global $update_statement;

    $update_statement->bind_param("ssssisii", $title, $writer, $synopsis, $publisher, $year, $genre, $rating, $manga_id);

    if ($update_statement->execute()) {
        return true;
    } else {
        echo "Error executing update query: " . $update_statement->error;
        return false;
    }
}


function delete_manga($manga_id) {
    global $connection;
    global $delete_statement;

    $delete_statement->bind_param("i", $manga_id);

    if(!$delete_statement->execute()) {
        handle_database_error("deleting manga");
    }
}


?>