<?php

require_once('/home/apedro3/data/connect.php');
$connection = db_connect();

$title = "Manga Info";
include('includes/header.php');

include('includes/functions.php');

$manga_id = $_GET['manga_id'] ? $_GET['manga_id'] : "No Manga";

?>

<main class="container">
    <section class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <?php

            if($manga_id === "No Manga") {
                echo "<p>Manga not found.</p>";
            } else {
                $query = "SELECT * FROM manga_data WHERE manga_id = ?;";
                $statement = mysqli_prepare($connection, $query);
                mysqli_stmt_bind_param($statement, "s", $manga_id);
                mysqli_stmt_execute($statement);
                $result = mysqli_stmt_get_result($statement);

                if(!$result) {
                    die("Query failed: ".mysqli_error($connection));
                } else {
                    $row = mysqli_fetch_assoc($result);

                    if(!$row) {
                        echo "<p>Manga not found.</p>";
                    } else {
                        $title = $row['title'];
                        echo '<h2 class="display-4">'.$title.' info.</h2>';

                        ?>

                        <!-- Card Output -->
                        <div class="card">
                            <!-- Card Header -->
                                <h3 class="card-title fw-light fs-5">
                                    <?php $title = $row['title']; ?>
                                </h3>

                                <?php
                                    echo "<div>
                                    <img src=\"$row[art]\" class=\"card-img-top\"></td>
                                    </div>"
                                ?>

                                <!-- Card Body -->
                                <div class="card-body">
                            <div class="">
                                    <!-- writer -->
                                    <p class="card-text">
                                        <span class="fw-bold">Writer</span>:
                                        <?php echo $row['writer']; ?>
                                    </p>

                                    <!-- synopsis -->
                                    <p class="card-text">
                                        <span class="fw-bold">Synopsis</span>:
                                        <?php echo $row['synopsis']; ?>
                                    </p>

                                    <!-- publisher -->
                                    <p class="card-text">
                                        <span class="fw-bold">Publisher</span>:
                                        <?php echo $row['publisher']; ?>
                                    </p>

                                    <!-- year -->
                                    <p class="card-text">
                                        <span class="fw-bold">Year</span>:
                                        <?php echo $row['year']; ?>
                                    </p>

                                    <!-- genre -->
                                    <p class="card-text">
                                        <span class="fw-bold">Genre</span>:
                                        <?php echo $row['genre']; ?>
                                    </p>

                                    <!-- rating -->
                                    <p class="card-text">
                                        <span class="fw-bold">Rating</span>:
                                        <?php echo $row['rating']; ?>
                                    </p>

                                    <button class="btn btn-primary" type='submit' name='add_to_list'>Add to List</button>

                                </div>
                            </div>

                            <?php
                    }
                }
            }
            ?>

                <a href="index.php" class="btn btn-success mt-5">Back to Index</a>
            </div>
    </section>
</main>

<?php

include('includes/footer.php');

db_disconnect($connection);

?>