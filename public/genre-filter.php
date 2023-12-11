<?php
require_once('/home/apedro3/data/connect.php');
$connection = db_connect();

$title = "Browse By ";
include('includes/header.php');


$filters = [
    "genre" => [
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
    ],
];

$active_filters = [];
foreach ($_GET as $filter => $values) {
    if (!is_array($values)) {
        $values = [$values];
    }
    $active_filters[$filter] = array_map("htmlspecialchars", $values);
}

?>

<main class="container">
    <section class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <h2 class="display-5">Filter the Data</h2>
            <p class="mb-5">Select any combination of the buttons below to filters the data.</p>

            <?php

            foreach ($filters as $filter => $options) {
                $heading = ucwords(str_replace(["_", "-"], " ", $filter));
                echo "<h3 class=\"fw-light\">" .
                    htmlspecialchars($heading) .
                    "</h3>";

                echo '<div class="btn-group mb-3" role="group" aria-label="' .
                    htmlspecialchars($filter) .
                    ' Filter Group">';
                foreach ($options as $value => $label) {
                    $is_active = in_array(
                        $value,
                        $active_filters[$filter] ?? []
                    );
                    $updated_filters = $active_filters;

                    if ($is_active) {
                        $updated_filters[$filter] = array_diff(
                            $updated_filters[$filter],
                            [$value]
                        );
                        if (empty($updated_filters[$filter])) {
                            unset($updated_filters[$filter]);
                        }
                    } else {
                        $updated_filters[$filter][] = $value;
                    }

                    $url =
                        $_SERVER["PHP_SELF"] .
                        "?" .
                        http_build_query($updated_filters);
                    echo '<a href="' .
                        htmlspecialchars($url) .
                        '" class="btn ' .
                        ($is_active ? "btn-success" : "btn-outline-success") .
                        '">' .
                        htmlspecialchars($label) .
                        "</a>";
                }
                echo "</div>";
            }

            if (!empty($active_filters)): ?>
                <hr>
                <div class="row">
                    <?php include("includes/filter_results.php"); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php

include('includes/footer.php');

db_disconnect($connection);

?>