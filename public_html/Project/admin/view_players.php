<?php
require(__DIR__ . "/../../../partials/nav.php");

if (is_logged_in(true)) {
    error_log("session Date: " . var_export($_SESSION, true));
}
//fss22 4/30/24
$id = se($_GET, "id", -1, false);

$player = [];
if ($id > -1) {
    // Fetch player data
    $db = getDB();
    $query = "SELECT name, age, position, nationality, created_at, updated_at FROM `Players` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $player = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching player record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
} else {
    flash("Invalid ID passed", "danger");
    die(header("Location: " . get_url("admin/list_players.php")));
}

foreach ($player as $key => $value) {
    if (is_null($value)) {
        $player[$key] = "N/A";
    }
}
?>

<div class="container-fluid">
    <h3>Player: <?php echo $player['name'] ?? 'Unknown'; ?></h3>
    <div>
        <a href="<?php echo get_url("admin/list_players.php"); ?>" class="btn btn-secondary">Back</a>
    </div>
    <div class="card mx-auto" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title">Player Details</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Name: <?php echo $player['name'] ?? 'Unknown'; ?></li>
                <li class="list-group-item">Nationality: <?php echo $player['nationality'] ?? 'Unknown'; ?></li>
                <li class="list-group-item">Age: <?php echo $player['age'] ?? 'Unknown'; ?></li>
                <li class="list-group-item">Created At: <?php echo $player['created_at'] ?? 'Unknown'; ?></li>
                <li class="list-group-item">Updated At: <?php echo $player['updated_at'] ?? 'Unknown'; ?></li>
            </ul>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>