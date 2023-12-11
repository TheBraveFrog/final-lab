<?php

$sql = "SELECT * FROM manga_data WHERE 1=1";

$types = "";

$values = [];

$parameters = [];

foreach ($active_filters as $filter => $filter_values) {
    if (in_array($filter, ["year"])) {
        foreach ($filter_values as $value) {

            list($min, $max) = explode("-", $value, 2);
            $range_queries[] = "$filter BETWEEN ? AND ?";
            $types .= "dd";
            $parameters[] = $min;
            $parameters[] = $max;
        }
        if (count($range_queries) > 0) {
            $sql .= " AND (" . implode(" OR ", $range_queries) . ")";
        }
    } else {
        $in = str_repeat("?,", count($filter_values) - 1) . "?";
        $sql .= " AND $filter IN ($in)";
        $types .= str_repeat("s", count($filter_values));
        $parameters = array_merge($parameters, $filter_values);
    }
}

$statement = $connection->prepare($sql);
if ($statement === FALSE) {
    echo "Failed to prepare the statement: (" . $connection->errno . ") " . $connection->error;
    exit();
}

$statement->bind_param($types, ...$parameters);

if (!$statement->execute()) {
    echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
}

$result = $statement->get_result();

if ($result->num_rows > 0) {
    echo "<div class=\"row\">";
    while ($row = $result->fetch_assoc()) { ?>
        <div class="col-md-6 col-xl-4 mb-3">
            <div class="card px-0">
                <div class="card-header text-bg-dark">
                    <?php echo $row['title']; ?>
                </div>
                <div class="card-body">
                    <?php echo $row['genre']; ?>
                    <?php echo $row['year']; ?>
                </div>
            </div>
        </div>
    <?php }
    echo "</div>";
} else {
    echo "<p>No results found.</p>";
}

?>