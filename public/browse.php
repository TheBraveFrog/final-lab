<?php
require_once('/home/apedro3/data/connect.php');
$connection = db_connect();

$title = "Search";
include('includes/header.php');

include('../private/prepared.php');

$title_search = isset($_GET['title-search']) ? $_GET['title-search'] : '';
$writer_search = isset($_GET['writer-search']) ? $_GET['writer-search'] : '';

$genre = isset($_GET['genre']) ? $_GET['genre'] : [];
$publisher = isset($_GET['publisher']) ? $_GET['publisher'] : [];

$query = "SELECT title, writer, genre, publisher, manga_id FROM manga_data WHERE 1 = 1";
$parameters = [];
$types = '';

if (isset($_GET['submit'])) {
    if (!empty($search_query)) {
        $query .= " AND (title LIKE CONCAT('%', ?, '%') OR writer LIKE CONCAT('%', ?, '%'))";
        $parameters[] = $search_query;
        $parameters[] = $search_query;
        $types .= 'ss';
    }

    // title
    if (!empty($title_search)) {
        $query .= " AND title LIKE CONCAT('%', ?, '%')";
        $parameters[] = $title_search;
        $types .= 's';
    }

    // writer
    if (!empty($writer_search)) {
        $query .= " AND writer LIKE CONCAT('%', ?, '%')";
        $parameters[] = $writer_search;
        $types .= 's';
    }

    // publisher
    if (!empty($publisher)) {
        $placeholders = implode(',', array_fill(0, count($publisher), '?'));
        $query .= " AND publisher IN ($placeholders)";
        foreach ($publisher as $cont) {
            $parameters[] = $cont;
            $types .= 's';
        }
    }

    // genre
    if (!empty($genre)) {
        $placeholders = implode(',', array_fill(0, count($genre), '?'));
        $query .= " AND genre IN ($placeholders)";
        foreach ($genre as $cont) {
            $parameters[] = $cont;
            $types .= 's';
        }
    }

    if ($statement = $connection->prepare($query)) {
        if ($types) {
            $statement->bind_param($types, ...$parameters);
        }

        $statement->execute();
        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            echo '<table class="table table-bordered">
                    <thead>
                        <tr class="bg-dark text-light">
                            <th scope="col">Title</th>
                            <th scope="col">Writer</th>
                            <th scope="col">Publisher</th>
                            <th scope="col">Genre</th>
                            <th scope="col">View More</th>
                        </tr>
                    </thead>
                    <tbody>';

            while ($row = $result->fetch_assoc()) {
                extract($row);

                echo "
                    <tr>
                        <td>$title</td>
                        <td>$writer</td>           
                        <td>$publisher</td>
                        <td>$genre</td>
                        <td><a href=\"view.php?manga_id=".urlencode($manga_id)."\">View More</a></td>
                    </tr>";
            }

            echo '</tbody></table>';
        } else {
            echo "<p>Sorry, there are no records available that match your query.</p>";
        }
    }
}

// Search form
echo '<form action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="GET" class="mb-5 border border-success p-3 rounded">
        <a href="'.htmlspecialchars($_SERVER['PHP_SELF']).'" class="btn btn-warning p-2">Clear Search</a>

        <fieldset class="my-5">
            <legend class="fs-5">Search</legend>
            <div class="mb-3">
                <label for="search" class="form-label">Enter search term:</label>
                <input type="search" id="search" name="search" class="form-control" value="'.htmlspecialchars($search_query).'">
            </div>
        </fieldset>

        <!-- Other filters (writer, genre, publisher) can be retained if needed -->

        <!-- Submit -->
        <div class="mb-3">
            <input type="submit" id="submit" name="submit" value="Search" class="btn btn-success">
        </div>
    </form>';

include('includes/footer.php');
db_disconnect($connection);
?>
