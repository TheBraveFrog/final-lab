<?php

require_once('/home/apedro3/data/connect.php');
$connection = db_connect();

$title = "Add a Manga Series";
include("includes/header.php");

require_once('../private/prepared.php');

include('includes/functions.php');

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
$message = '';

// Form Handling
if (isset($_POST['submit'])) {

    $proceed = TRUE;

    $title = isset($_POST['title']) ? $_POST['title'] : '';

    $synopsis = isset($_POST['synopsis']) ? $_POST['synopsis'] : '';

    $writer = isset($_POST['writer']) ? $_POST['writer'] : '';

    $year = isset($_POST['year']) ? $_POST['year'] : '';

    $rating = isset($_POST['rating']) ? $_POST['rating'] : '';

    $publisher = isset($_POST['publisher']) ? $_POST['publisher'] : '';

    $genre = isset($_POST['genre']) ? $_POST['genre'] : '';

    $art = '';

    $file_name_new = '';


    if (strlen($title) < 2 || strlen($title) > 60) {
        $proceed = FALSE;
        $message .= "<p>Please enter an manga title that is shorter than 60 characters.</p>";
    } elseif (str_contains($title, "'" or str_contains($title, '"'))) {
        $title = mysqli_real_escape_string($connection, $title);
    }

    if (strlen($synopsis) < 50 || strlen($synopsis) > 850) {
        $proceed = FALSE;
        $message .= "<p>Please enter an manga synopsis that is between 50 and 850 characters.</p>";
    } elseif (str_contains($synopsis, "'" or str_contains($synopsis, '"'))) {
        $synopsis = mysqli_real_escape_string($connection, $synopsis);
    }

    if (strlen($writer) < 2 || strlen($writer) > 35) {
        $proceed = FALSE;
        $message .= "<p>Please enter a manga author name that is between 2 and 35 characters.</p>";
    } elseif (str_contains($writer, "'" or str_contains($writer, '"'))) {
        $writer = mysqli_real_escape_string($connection, $writer);
    }

    if (strlen($year) != 4) {
        $proceed = FALSE;
        $message .= "<p>Year must be 4 characters in length.</p>";
    }

    if (strlen($rating) <= 0 || strlen($rating) > 10) {
        $proceed = FALSE;
        $message .= "<p>Rating must be between 0 and 10.</p>";
    }

    if ($proceed == TRUE) {

        if (isset($_FILES['img-file']) && !empty($_FILES['img-file'])) {
            $file = $_FILES['img-file'];
            $file_name = $_FILES['img-file']['name'];
            $file_temp_name = $_FILES['img-file']['tmp_name'];
            $file_size = $_FILES['img-file']['size'];
            $file_error = $_FILES['img-file']['error'];

            // grab the uploaded file's extension.
            $file_extension = explode('.', $file_name);
            $file_extension = strtolower(end($file_extension));

            $allowed = array('jpg', 'jpeg', 'png', 'webp');

            if (in_array($file_extension, $allowed) && $file_error === 0 && $file_size < 2000000) {
                $file_name_new = uniqid('', true) . "." . $file_extension;
                $file_destination = 'images/full/' . $file_name_new;

                // Check to see if the directory exists; if not, make it.
                if (!is_dir('images/full/')) {
                    mkdir('images/full/', 0777, true);
                }

                if (!is_dir('images/thumbs')) {
                    mkdir('images/thumbs/', 0777, true);
                }

                // Check if the file already exists
                if (!file_exists($file_destination)) {
                    // Move the uploaded file to the directory.
                    move_uploaded_file($file_temp_name, $file_destination);

                    // Check the image dimensions. 
                    list($width_original, $height_original) = getimagesize($file_destination);

                    $resizedWidth = 750;
                    $resizedHeight = 750;

                    $resizedImage = imagecreatetruecolor($resizedWidth, $resizedHeight);

                    $thumb = imagecreatetruecolor(256, 256);

                    // Calculate the shorter side / smaller size between width and height.
                    $smaller_size = min($width_original, $height_original);

                    // Calculate the starting point for cropping the image.
                    $x_coordinate = ($width_original > $smaller_size) ? ($width_original - $smaller_size) / 2 : 0;
                    $y_coordinate = ($height_original > $smaller_size) ? ($height_original - $smaller_size) / 2 : 0;

                    // Create image based on the filetype we grabbed earlier.
                    switch ($file_extension) {
                        case 'jpeg':
                        case 'jpg':
                            $src_image = imagecreatefromjpeg($file_destination);
                            break;
                        case 'png':
                            $src_image = imagecreatefrompng($file_destination);
                            break;
                        case 'webp':
                            $src_image = imagecreatefromwebp($file_destination);
                            break;
                        default:
                            // Invalid Type
                            $message .= "<p>This file type is not supported.</p>";
                            exit;
                    }

                    // Resize the image
                    imagecopyresampled($resizedImage, $src_image, 0, 0, 0, 0, $resizedWidth, $resizedHeight, $width_original, $height_original);

                    imagejpeg($resizedImage, 'images/full_resized/' . $file_name_new, 100);

                    // Crop and resize the user-uploaded image.
                    imagecopyresampled($thumb, $src_image, 0, 0, (int) $x_coordinate, (int) $y_coordinate, 256, 256, (int) $smaller_size, (int) $smaller_size);


                    // Save the thumbnail to the server.
                    imagejpeg($thumb, 'images/thumbs/' . $file_name_new, 100);

                    // Free up some server resources by destroying the working object.
                    imagedestroy($resizedImage);
                    imagedestroy($thumb);
                    imagedestroy($src_image);
                    $art = 'images/full_resized/' . $file_name_new;

                    if (insert_manga($title, $writer, $synopsis, $publisher, $year, $genre, $art, $rating, $file_name_new)) {

                        $title = $writer = $synopsis = $publisher = $year = $genre = $art = $rating = '';

                    }

                    if ($connection->error) {
                        $message = '<p>There was a problem: ' . $connection->error . '</p>';
                    } else {
                        $message = '<p>Manga added successfully!</p>';
                        ;
                    }
                } else {
                    $message = '<p>There was a problem: ' . $connection->error . '</p>';
                }

            }
        }
    }
}


?>

<main>
    <section>
        <h1 class="fw-light">Add Manga to the Database</h1>
        <p class="text-muted mb-5">To add any manga to our database, simply fill out the form below and hit 'save'.</p>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST"
            enctype="multipart/form-data">

            <!-- User Message -->
            <?php if (!empty($message)): ?>
                <div class="message text-danger">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="title" class="form-label">Manga Title</label>
                <input type="text" id="title" name="title" class="form-control" value="<?php
                if (isset($_POST['title']))
                    echo htmlspecialchars($_POST['title']); ?>">
            </div>

            <div class="mb-3">
                <label for="writer" class="form-label">Writer</label>
                <input type="text" id="writer" name="writer" class="form-control" value="<?php
                if (isset($_POST['writer']))
                    echo htmlspecialchars($_POST['writer']); ?>">
            </div>

            <div class="mb-3">
                <label for="publisher" class="form-label">Publisher</label>
                <select name="publisher" id="publisher" class="form-select">
                    <?php

                    $publisher = [
                        'Viz' => 'Viz',
                        'Yen Press' => 'Yen Press',
                        'Kodansha' => 'Kodansha',
                        'Dark Horse' => 'Dark Horse',
                        'Shueisha' => 'Shueisha',
                        'TOKYOPOP' => 'TOKYOPOP',
                    ];

                    foreach ($publisher as $key => $value) {
                        if ($user_publisher == $key || $existing_publisher == $key) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        }

                        echo "<option value=\"$key\" $selected>$value</option>";
                    }

                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="synopsis" class="form-label">Synopsis</label>
                <textarea name="synopsis" id="synopsis" cols="30" rows="10" class="form-control"><?php
                if (isset($_POST['synopsis'])) {
                    echo htmlspecialchars($_POST['synopsis']);
                }
                ?></textarea>
            </div>

            <div class="mb-3">
                <label for="genre" class="form-label">Genre</label>
                <select name="genre" id="genre" class="form-select">
                    <?php

                    $genre = [
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

                    foreach ($genre as $key => $value) {
                        if ($user_genre == $key || $existing_genre == $key) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        }

                        echo "<option value=\"$key\" $selected>$value</option>";
                    }

                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="year" class="form-label">Year of Release</label>
                <input type="number" id="year" name="year" class="form-control" min="1900" max="2024" value="<?php
                if ($user_year != "") {
                    echo $user_year;
                } else {
                    echo $existing_year;
                }
                ?>">
            </div>

            <div class="mb-3">
                <label for="rating" class="form-label">My Anime List Rating (out of 10)</label>
                <input type="number" id="rating" name="rating" min="0" max="10" class="form-control" value="<?php
                if ($user_rating != "") {
                    echo $user_rating;
                } else {
                    echo $existing_rating;
                }
                ?>">
            </div>

            <div class="mb-3">
                <label for="img-file">Image File</label>
                <input type="file" id="img-file" name="img-file" class="form-control" accept=".jpg, .jpeg, .png, .webp"
                    required>
            </div>

            <input type="submit" value="Save" name="submit" class="btn btn-success">
        </form>
    </section>
</main>

<?php

include('includes/footer.php');

?>