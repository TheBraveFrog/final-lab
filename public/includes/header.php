<?php 

session_start();

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php echo $title; ?>
    </title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body class="container p-5">
    <header class="text-center">
        <nav class="mb-5">
            <ul class="nav me-auto">
            <li class="nav-item"><a href="index.php" class="nav-link link-light link-body-emphasis px-2">Back to Home </a>
                </li>
                <li class="nav-item"><a href="#" class="nav-link link-light link-body-emphasis px-2">Browse by: </a>
                </li>
                <li class="nav-item"><a href="publisher-filter.php"
                        class="nav-link link-light link-body-emphasis px-2">Browse By Publisher</a></li>
                <li class="nav-item"><a href="decade-filter.php"
                        class="nav-link link-light link-body-emphasis px-2">Browse By Decade</a></li>
                <li class="nav-item"><a href="search.php"
                        class="nav-link link-light link-body-emphasis px-2">Advanced Search</a></li>
            </ul>


            <?php if (isset($_SESSION['username'])): ?>
                
                <ul class="nav">
                    <li class="nav-item p-2"><a href="logout.php" class="btn btn-dark">Log Out</a></li>
                    <li class="nav-item p-2"><a href="view_list.php" class="btn btn-dark">View Your Manga List</a></li>
                    <li class="nav-item p-2"><a href="add.php" class="btn btn-dark">Add</a></li>
                    <li class="nav-item p-2"><a href="edit.php" class="btn btn-dark">Edit</a></li>
                    <li class="nav-item p-2"><a href="delete.php" class="btn btn-dark">Delete</a></li>
                </ul>

            <?php else: ?>
                <ul class="nav">
                    <li class="nav-item p-2"><a href="login.php" class="btn btn-success">Log In</a></li>
                </ul>

            <?php endif; ?>
        </nav>
    </header>