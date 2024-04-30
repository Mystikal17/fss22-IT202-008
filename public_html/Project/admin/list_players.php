<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (is_logged_in(true)) {
    error_log("session Date: " . var_export($_SESSION, true));
}


// build search form
$form = [
    ["type" => "text", "name" => "name", "placeholder" => "Player Name", "label" => "Player Name", "include_margin" => false],

    //["type" => "text", "name" => "position", "placeholder" => "Position", "label" => "Position", "include_margin" => false],

    //["type" => "number", "name" => "age_min", "placeholder" => "Min Age", "label" => "Min Age", "include_margin" => false],
    //["type" => "number", "name" => "age_max", "placeholder" => "Max Age", "label" => "Max Age", "include_margin" => false],

    //["type" => "text", "name" => "nationality", "placeholder" => "Nationality", "label" => "Nationality", "include_margin" => false],

    //["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["name" => "Name", "position" => "Position", "age" => "Age"], "include_margin" => false],
    //["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "Ascending", "desc" => "Descending"], "include_margin" => false],

    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false],
];

$query = "SELECT id, Name FROM Players WHERE 1=1";
//$query = "SELECT id, name, position, age, nationality FROM Players WHERE 1=1";
$params = [];

// Add conditions based on form input
if (!empty($_GET["name"])) {
    $query .= " AND name LIKE :name";
    $params[":name"] = "%" . $_GET["name"] . "%";
}

if (!empty($_GET["position"])) {
    $query .= " AND position LIKE :position";
    $params[":position"] = "%" . $_GET["position"] . "%";
}

if (!empty($_GET["age_min"])) {
    $query .= " AND age >= :age_min";
    $params[":age_min"] = $_GET["age_min"];
}

if (!empty($_GET["age_max"])) {
    $query .= " AND age <= :age_max";
    $params[":age_max"] = $_GET["age_max"];
}

if (!empty($_GET["nationality"])) {
    $query .= " AND nationality LIKE :nationality";
    $params[":nationality"] = "%" . $_GET["nationality"] . "%";
}

// Sort and order
$sort = isset($_GET["sort"]) ? $_GET["sort"] : "name";
$order = isset($_GET["order"]) ? $_GET["order"] : "asc";
$query .= " ORDER BY $sort $order";

// Limit
$limit = isset($_GET["limit"]) ? $_GET["limit"] : 10;
$query .= " LIMIT $limit";

$db = getDB();
$stmt = $db->prepare($query);
$results = [];

try {
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching players: " . $e->getMessage());
    flash("An error occurred while fetching players", "danger");
}

$table = [
    "data" => $results,
    "title" => "Players",
    "ignored_columns" => ["id"],
    "edit_url" => get_url("admin/edit_players.php"),
    "delete_url" => get_url("admin/delete_players.php"),
    "view_url" => get_url("admin/view_players.php"),
];

?>

<div class="container-fluid">
    <h3>List Players</h3>
    <form method="GET">
        <div class="row mb-3" style="align-items: flex-end;">
            <?php foreach ($form as $k => $v) : ?>
                <div class="col">
                    <?php render_input($v); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Filter"]); ?>
        <a href="?clear" class="btn btn-secondary">Clear</a>
    </form>
    <?php render_table($table); ?>
</div>

<?php
// note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>