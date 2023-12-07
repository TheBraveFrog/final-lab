<?php

require_once('/home/apedro3/data/connect.php');
$connection = db_connect();

require_once('../private/prepared.php');

$title = "Deletion Confirmation";
include("includes/header.php");

include("includes/functions.php");

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$title = isset($_GET["title"]) ? $_GET["title"] :"";
$message = '';

if (isset($_GET['manga_id']) && is_numeric($_GET['manga_id']) && $_GET['manga_id'] > 0) {
    $manga_id = $_GET['manga_id'];
} else {
    $message = "<p>Please return to the 'delete' page and select an option from the table.</p>";
    $attraction_id = NULL;
}

if (isset($_POST['confirm'])) {
    $hidden_id = $_POST['hidden_id'];
    delete_manga($hidden_id);

    $message = "<p>Your manga was deleted from the database.</p>";
}

?>

<main>
    <section>
        <h1 class="fw-light text-center">Deletion Confirmation</h1>
        
        <?php if ($message) : ?>
            <div class="alert alert-danger text-center" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif;
        
        if ($manga_id != NULL) : ?>
            <p class="text-danger lead mb-5 text-center">Are you sure that you want to delete <?php echo $title; ?>?</p>
        

        <form action="<?php echo ($_SERVER['PHP_SELF']); ?>" method="POST">
            <!-- Hidden Value -->
            <input type="hidden" id="hidden_id" name="hidden_id" value="<?php echo $manga_id; ?>">

            <!-- Submit -->
            <input type="submit" class="btn btn-danger d-block mx-auto" name="confirm" id="confirm" value="Yes, I'm sure.">
        </form>
        
        <?php endif; ?>

    </section>
</main>

<?php

include('includes/footer.php');

?>