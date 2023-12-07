<?php

if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if($username && $password) {

        $statement = $connection->prepare('SELECT * FROM catalogue_admin WHERE LOWER(username) = LOWER(?);');

        $statement->bind_param('s', $username);
        $statement->execute();

        if ($statement->error) {
            die('Error: ' . $statement->error);
        }

        $result = $statement->get_result();

        if($result->num_rows === 1) {
            $row = $result->fetch_assoc();

                if (password_verify($password, $row['hashed_pass'])) {
                    session_regenerate_id();
                    
                    $_SESSION['username'] = $row['username'];

                    $_SESSION['last_login'] = time();

                    $_SESSION['login_expires'] = strtotime("+1 day midnight");

                    // Redirect the user
                    header("Location: index.php");
                } else {
                    $message = "<p class=\"text-warning\">Invalid username or password.</p>";
                }
        } else {
            $message = "<p class=\"text-warning\">Both fields are required.</p>";
        }
    }
}
?>