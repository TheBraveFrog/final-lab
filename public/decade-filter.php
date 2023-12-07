<?php
require_once('/home/apedro3/data/connect.php');
$connection = db_connect();

$title = "Browse By ";
include('includes/header.php');


$filters = [
    "genre" => [
        "Shounen" => "1950's-1960's",
        "Seinen" => "1960's-1970's",
        "Shojo" => "1970's-1980's",
        "1980-1990" => "1980's-1990's",
        "1990-2000" => "1990's-2000's",
        "2000-2010" => "2000's-2010's",
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

            if (!empty($active_filters)) : ?>
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