<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

$id = se($_GET, "id", -1, false);

$player = ['Players'];
if ($id > -1) {
    // Fetch player data with name, position, age, and nationality
    $db = getDB();
    $query = "SELECT name, position, age, nationality, created, modified FROM `Players` WHERE id = :id";
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

// Handle missing data
foreach ($player as $key => $value) {
    if (is_null($value)) {
        $player[$key] = "N/A";
    }
}
?>

<div class="container-fluid">
    <h3>Player: <?php echo $player, 'name','Unknown'; ?></h3>
    <div>
        <a href="<?php echo get_url("admin/list_players.php"); ?>" class="btn btn-secondary">Back</a>
    </div>
    <div class="card mx-auto" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title">Player Details</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Name: <?php se($player, 'name', 'Unknown'); ?></li>
                <li class="list-group-item">Position: <?php se($player, 'position', 'Unknown'); ?></li>
                <li class="list-group-item">Age: <?php se($player, 'age', 'Unknown'); ?></li>
                <li class="list-group-item">Nationality: <?php se($player, 'nationality', 'Unknown'); ?></li>
            </ul>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>