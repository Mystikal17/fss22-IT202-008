<?php
//fss22 4/4/30
require(__DIR__ . "/../../../partials/nav.php");

if (is_logged_in(true)) {
    error_log("session Date: " . var_export($_SESSION, true));
}

// build search form
$form = [
    ["type" => "text", "name" => "name", "placeholder" => "Player Name", "label" => "Player Name", "include_margin" => false],
    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false],
];

$query = "SELECT id, Name FROM Players WHERE 1=1";
$params = [];

// Add conditions based on form input
if (!empty($_GET["name"])) {
    $query .= " AND name LIKE :name";
    $params[":name"] = "%" . $_GET["name"] . "%";
}

// Sort and order
$sort = isset($_GET["sort"]) ? $_GET["sort"] : "name";
$order = isset($_GET["order"]) ? $_GET["order"] : "asc";

// Handle alphabetical sorting
if (isset($_GET["sort_alphabet"])) {
    $query .= " ORDER BY name ASC";
} else {
    $query .= " ORDER BY $sort $order";
}

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
            <div class="col">
                <?php render_button(["text" => "Search", "type" => "submit", "text" => "Filter"]); ?>
                <?php render_button(["text" => "Sort Alphabetically", "type" => "submit", "name" => "sort_alphabet"]); ?>
                <a href="?clear" class="btn btn-secondary">Clear</a>
            </div>
        </div>
    </form>
    <?php render_table($table); ?>
</div>

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>