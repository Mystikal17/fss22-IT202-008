<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

$id = se($_GET, "id", -1, false);

$team = ['Teams'];
if ($id > -1) {
    // Fetch team data with name, league, and coach
    $db = getDB();
    $query = "SELECT name, league, coach FROM `Teams` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $team = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching team record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
} else {
    flash("Invalid ID passed", "danger");
    die(header("Location: " . get_url("admin/list_teams.php")));
}

// Handle missing data
foreach ($team as $key => $value) {
    if (is_null($value)) {
        $team[$key] = "N/A";
    }
}
?>

<div class="container-fluid">
    <h3>Team: <?php echo $team['name'] ?? 'Unknown'; ?></h3>
    <div>
        <a href="<?php echo get_url("admin/list_teams.php"); ?>" class="btn btn-secondary">Back</a>
    </div>
    <div class="card mx-auto" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title">Team Details</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Name: <?php echo $team['name'] ?? 'Unknown'; ?></li>
                <li class="list-group-item">League: <?php echo $team['league'] ?? 'Unknown'; ?></li>
                <li class="list-group-item">Coach: <?php echo $team['coach'] ?? 'Unknown'; ?></li>
            </ul>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>