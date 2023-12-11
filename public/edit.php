<?php
require_once('/home/apedro3/data/connect.php');
$connection = db_connect();

require_once('../private/prepared.php');

include('includes/functions.php');

$title = "Edit a Manga";
include('includes/header.php');

if(!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$existing_title = "";
$existing_writer = "";
$existing_synopsis = "";
$existing_genre = "";
$existing_publisher = "";
$existing_rating = "";
$existing_year = "";
$existing_art = "";

if(isset($manga_id) && is_numeric($manga_id) && $manga_id > 0) {
    $manga = select_manga_by_id($manga_id);

    if($manga) {
        $existing_title = $manga['title'];
        $existing_writer = $manga['writer'];
        $existing_synopsis = $manga['synopsis'];
        $existing_genre = $manga['genre'];
        $existing_publisher = $manga['publisher'];
        $existing_rating = $manga['rating'];
        $existing_year = $manga['year'];
    } else {
        $message .= "Sorry, there are no records available that match your query.";
    }
}


if(isset($_GET['manga_id'])) {
    $manga_id = $_GET['manga_id'];
} elseif(isset($_POST['manga_id'])) {
    $manga_id = $_POST['manga_id'];
} else {
    $manga_id = "";
}

$message = "";
$update_message = "";

$user_title = isset($_POST['submit']) ? trim($_POST['title']) : "";
$user_writer = isset($_POST['submit']) ? $_POST['writer'] : "";
$user_synopsis = isset($_POST['submit']) ? trim($_POST['synopsis']) : "";
$user_genre = isset($_POST['submit']) ? trim($_POST['genre']) : "";
$user_publisher = isset($_POST['submit']) ? trim($_POST['publisher']) : "";
$user_year = isset($_POST['submit']) ? trim($_POST['year']) : "";
$user_rating = isset($_POST['submit']) ? trim($_POST['rating']) : "";

if(isset($manga_id)) {
    if(is_numeric($manga_id) && $manga_id > 0) {

        $manga = select_manga_by_id($manga_id);

        if($manga) {
            $existing_title = $manga['title'];
            $existing_writer = $manga['writer'];
            $existing_synopsis = $manga['synopsis'];
            $existing_genre = $manga['genre'];
            $existing_publisher = $manga['publisher'];
            $existing_rating = $manga['rating'];
            $existing_year = $manga['year'];
        } else {
            $message .= "Sorry, there are no records available that match your query.";
        }
    }

}

if(isset($_POST['submit'])) {

    $proceed = TRUE;

    $user_title = filter_var($user_title, FILTER_SANITIZE_STRING);
    $user_manga_title = mysqli_real_escape_string($connection, $user_title);
    if(strlen($user_title) < 2 || strlen($user_title) > 60) {
        $proceed = FALSE;
        $update_message .= "<p>Please enter an manga name that is shorter than 60 characters.</p>";
    }

    $user_writer = filter_var($user_writer, FILTER_SANITIZE_STRING);

    $user_synopsis = filter_var($user_synopsis, FILTER_SANITIZE_STRING);
    if(strlen($user_synopsis) < 12 || strlen($user_synopsis) > 450) {
        $proceed = FALSE;
        $update_message .= "<p>Please enter an manga synopsis that is between 12 and 450 characters.</p>";
    }


    $user_genre = filter_var($user_genre, FILTER_SANITIZE_STRING);

    $user_publisher = filter_var($user_publisher, FILTER_SANITIZE_STRING);

    $user_year = filter_var($user_year, FILTER_SANITIZE_STRING);
    if(strlen($user_year) < 0 || strlen($user_year) > 110) {
        $proceed = FALSE;
        $update_message .= "<p>Please enter an manga year that is between 0 and 110mph.</p>";
    }

    $user_rating = filter_var($user_rating, FILTER_SANITIZE_NUMBER_INT);
    if(strlen($user_rating) < 0 || strlen($user_rating) > 5) {
        $proceed = FALSE;
        $update_message .= "";
    }

    if($proceed == TRUE) {

        update_manga($user_title, $user_writer, $user_synopsis, $user_publisher, $user_year, $user_genre, $user_rating, $manga_id);

        if($connection->error) {
            $update_message .= '<p>There was a problem updating the manga: '.$connection->error.'</p>';
        } else {
            $message .= "<p>$user_title updated successfully!</p>";
        }

        $manga_id = "";
    }
}

?>

<main>
    <section>
        <h1 class="fw-light text-center mt-5">Edit A Manga</h1>
        <p class="text-muted mb-5">To edit a record in our database, click 'Edit' beside the row you would like to
            change. Next, add your updated values into the form and hit 'Save'.</p>

        <?php if($message != ""): ?>
            <div class="alert alert-info" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="list">

            <?php
            $mangas = get_all_manga();

            if(count($mangas) > 0) {
                echo "<table  class=\"table table-bordered table-hover\">";
                echo "<tr>";
                echo "<th scope=\"col\">Cover</th>";
                echo "<th scope=\"col\">Title</th>";
                echo "<th scope=\"col\">Writer</th>";
                echo "<th scope=\"col\">Synopsis</th>";
                echo "<th scope=\"col\">Genre</th>";
                echo "<th scope=\"col\">Publisher</th>";
                echo "<th scope=\"col\">Year</th>";
                echo "<th scope=\"col\">Rating</th>";
                echo "<th scope=\"col\">Edit</th>";
                echo "</tr>";
                foreach($mangas as $manga) {
                    extract($manga);
                    echo "<tr>
                    <td><img src=\"$art\" class=\"card-img-top\"></td>
                    <td>$title</td>
                    <td>$writer</td>
                    <td>$synopsis</td>
                    <td>$genre</td>
                    <td>$publisher</td>
                    <td>$year</td>
                    <td>$rating</td>
                        <td><a href=\"edit.php?manga_id=$manga_id\">Edit</a></td>
                    </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Sorry there are no records available that match your query</p>";
            }
            ?>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="exampleModalLabel">
                            Edit
                            <?php echo $existing_title; ?>
                        </h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <form action="edit.php" method="POST">

                            <?php if(isset($update_message)): ?>
                                <div class="message text-danger">
                                    <?php echo $update_message; ?>
                                </div>
                            <?php endif; ?>


                            <div class="mb-3">
                                <label for="title" class="form-label">Manga Title</label>
                                <input type="text" id="title" name="title" class="form-control" value="<?php
                                if($user_title != "") {
                                    echo $user_title;
                                } else {
                                    echo $existing_title;
                                }
                                ?>">
                            </div>

                            <div class="mb-3">
                                <label for="publisher" class="form-label">Publisher</label>
                                <select name="publisher" id="publisher" class="form-select">
                                    <?php

                                    $publisher_select = [
                                        'Viz' => 'Viz',
                                        'Yen Press' => 'Yen Press',
                                        'Kodansha' => 'Kodansha',
                                        'Dark Horse' => 'Dark Horse',
                                        'Shueisha' => 'Shueisha',
                                        'TOKYOPOP' => 'TOKYOPOP',
                                    ];

                                    foreach($publisher_select as $publisher => $value) {
                                        if($user_publisher == $publisher || $existing_publisher == $value) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }

                                        echo "<option value=\"$publisher\" $selected>$value</option>";
                                    }

                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="synopsis" class="form-label">Synopsis</label>
                                <textarea name="synopsis" id="synopsis" cols="30" rows="10" class="form-control"><?php
                                if($user_synopsis != "") {
                                    echo $user_synopsis;
                                } else {
                                    echo $existing_synopsis;
                                }
                                ?></textarea>

                            </div>

                            <div class="mb-3">
                                <label for="genre" class="form-label">Genre</label>
                                <select name="genre" id="genre" class="form-select">
                                    <?php

                                    $genre_select = [
                                        'Action' => 'Action',
                                        'Adventure' => 'Adventure',
                                        'Coming-of-age' => 'Coming-of-age',
                                        'Drama' => 'Drama',
                                        'Fantasy' => 'Fantasy',
                                        'Horror' => 'Horror',
                                        'Mystery' => 'Mystery',
                                        'Romance' => 'Romance',
                                        'Seinen' => 'Seinen',
                                        'Shounen' => 'Shounen',
                                        'Sports' => 'Sports',
                                    ];

                                    foreach($genre_select as $genre => $value) {
                                        if($user_genre == $genre || $existing_genre == $genre) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }

                                        echo "<option value=\"$value\" $selected>$value</option>";
                                    }

                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="writer" class="form-label">Writer</label>
                                <input type="text" id="writer" name="writer" class="form-control" value="<?php
                                if($user_writer != "") {
                                    echo $user_writer;
                                } else {
                                    echo $existing_writer;
                                }
                                ?>">
                            </div>


                            <div class="mb-3">
                                <label for="year" class="form-label">Year</label>
                                <input type="number" id="year" name="year" class="form-control" value="<?php
                                if($user_year != "") {
                                    echo $user_year;
                                } else {
                                    echo $existing_year;
                                }
                                ?>">
                            </div>

                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating (out of 10)</label>
                                <input type="number" id="rating" name="rating" min="0" max="10" class="form-control"
                                    value="<?php
                                    if($user_rating != "") {
                                        echo $user_rating;
                                    } else {
                                        echo $existing_rating;
                                    }
                                    ?>">
                            </div>


                            <input type="hidden" name="manga_id" value="<?php echo $manga_id; ?>">

                            <input type="submit" value="Save" name="submit" class="btn btn-success">
                        </form>
                    </div>
                </div>
            </div>
        </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
    crossorigin="anonymous"></script>

<?php if($manga_id): ?>

    <script>
        var myModal = new bootstrap.Modal(document.getElementById("exampleModal"), {});

        document.onreadystatechange = function () {
            myModal.show();
        };
    </script>

<?php endif; ?>

</body>

</html>

<?php

// Close the connection.
db_disconnect($connection);

?>