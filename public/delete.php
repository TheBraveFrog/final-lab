<?php
require_once('/home/apedro3/data/connect.php');
$connection = db_connect();

$title = "Delete Page";
include('includes/header.php');

include('includes/functions.php');

include('../private/prepared.php');

$file_name_new = isset($_GET['file_name_new']) ? $_GET['file_name_new'] : '';

$per_page = 15;

$total_count = count_manga();

$total_pages = ceil($total_count / $per_page);

$current_page = (int) ($_GET['page'] ?? 1);

if ($current_page < 1 || $current_page > $total_pages || !is_int($current_page)) {
    $current_page = 1;
}

$offset = $per_page * ($current_page - 1);

?>

<main class="container">
    <section class="row justify-content-between my-5">

        <!-- Introduction -->
        <div class="col-md-10 col-lg-8 col-xxl-6 mb-4">
            <h2 class="display-4">Delete a Series</h2>
            <p>To delete a series from our database just press the 'Delete' button beside the entry.</p>
        </div>

    <!-- Table of Records -->
    <section>

        <?php

        if (isset($_GET['sort_by'])) {
            $sort_by = $_GET['sort_by'];
            $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';
            $result = find_manga_filter($sort_by, $sort_order, $per_page, $offset);
            ;

            if ($connection->error) {
                echo "Connection Error: " . $connection->error;
            } elseif ($result === false) {
                echo "Error in find_manga_filter function.";
            } else {
                "Number of Rows: " . $result->num_rows;

                if ($result->num_rows > 0) {
                    echo '<table class="table table-bordered">
            <thead>
                    <th scope="col">Cover</th>
                    <th scope="col"><a href="?sort_by=title&sort_order=' . get_sort_order('title') . '">Title</a></th>
                    <th scope="col"><a href="?sort_by=writer&sort_order=' . get_sort_order('writer') . '">Writer</a></th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
            <tbody>';
                    while ($row = $result->fetch_assoc()) {
                        extract($row);

                        $manga_id = $row['manga_id'];
                       $thumbnail_path = str_replace("full_resized", "thumbs", $art);
                        echo "<tr>
                            <td><img src=\"$thumbnail_path\" class=\"card-img-top\"></td>
                            <td>$title</td>
                            <td>$writer</td>
                            <td><a href=\"delete-confirmation.php?manga_id=" . urlencode($manga_id) . "&title=" . urlencode($title) . "\" class=\"btn btn-danger\">Delete</a></td>
                        </tr>";

                    }

                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo "<p>No records found.</p>";
                }
            }
        } else {
            $result = find_manga($per_page, $offset);

            if ($connection->error) {
                echo $connection->error;
            } elseif ($result->num_rows > 0) {
                echo '<table class="table table-bordered ">
                             <thead>
                                <tr class="bg-dark text-light">
                                    <th scope="col">Cover</th>
                                    <th scope="col"><a href="?sort_by=title">Title</th>
                                    <th scope="col"><a href="?sort_by=writer">Writer</th>
                                    <th scope="col">Delete</th>
                                </tr>
                             </thead>
                             <tbody>
    
                        ';

                while ($row = $result->fetch_assoc()) {
                    extract($row);
                    $manga_id = $row['manga_id'];
                   $thumbnail_path = str_replace("full_resized", "thumbs", $art);
                    echo "<tr>
                    <td><img src=\"$thumbnail_path\" class=\"card-img-top\"></td>
                    <td>" . htmlspecialchars($title) . "</td>
                    <td>" . htmlspecialchars($writer) . "</td>
                    <td><a href=\"delete-confirmation.php?manga_id=" . urlencode($manga_id) . "&title=" . urlencode($title) . "\" class=\"btn btn-danger\">Delete</a></td>
                    </tr>";
                
                }
                echo '</tbody>';
                echo '</table>';
            }
        }
        ?>

        <nav aria-label="Page Number">
            <ul class="pagination justify-content-center">
                <?php if ($current_page > 1): ?>
                    <li class="page-item">
                        <a href="index.php?page=<?php echo $current_page - 1; ?>" class="page-link">Previous</a>
                    </li>
                <?php endif;
                $gap = false;

                $window = 1;

                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i > 1 + $window && $i < $total_pages - $window && abs($i - $current_page) > $window) {
                        if (!$gap): ?>
                            <li class="page-item">
                                <span class="page-link">
                                    ...
                                </span>
                            </li>
                        <?php endif;

                        $gap = true;
                        continue;
                    }

                    $gap = false;

                    if ($current_page == $i): ?>
                        <li class="page-item active">
                            <a href="index.php?page=<?php echo $i; ?>" class="page-link">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item">
                            <a href="index.php?page=<?php echo $i; ?>" class="page-link">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endif;
                }
                ?>

                <?php if ($current_page < $total_pages): ?>
                    <li class="page-item">
                        <a href="index.php?page=<?php echo $current_page + 1; ?>" class="page-link">Next</a>
                    </li>
                <?php endif; ?>

            </ul>
        </nav>

        </div>
    </section>
</main>
<?php

include('includes/footer.php');

db_disconnect($connection);

?>