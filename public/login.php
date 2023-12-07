<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('/home/apedro3/data/connect.php');
$connection = db_connect();

$title = "Login";
include("includes/header.php");

include('../private/login-process.php');

$message = '';

if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

?>

<main class="container mt-5">
    <section class="row justify-content-center">
        <div class="col-md-8 col-xl-6">
            <h1>Login</h1>
            <p class="lead">To access the administrative area for this application, please login down below.</p>

            <?php if ($message != NULL) : ?>
                <div class="alert alert-info" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <!-- Username -->
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <!-- Submit -->
                <input type="submit" class="btn btn-success mt-4" id="login" name="login" value="Login">
            </form>
        </div>
    </section>
</main>