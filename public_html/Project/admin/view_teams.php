<?php
require(__DIR__ . "/../../../partials/nav.php");

if (is_logged_in(true)) {
    error_log("session Date: " . var_export($_SESSION, true));
}

$id = se($_GET, "id", -1, false);
//fss22 4/30/24
$team = [];
if ($id > -1) {
    // Fetch team data
    $db = getDB();
    $query = "SELECT team_name, coach, league, created_at, updated_at FROM `Teams` WHERE id = :id";
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

foreach ($team as $key => $value) {
    if (is_null($value)) {
        $team[$key] = "N/A";
    }
}
?>

<div class="container-fluid">
    <h3>Team: <?php echo $team['team_name'] ?? 'Unknown'; ?></h3>
    <div>
        <a href="<?php echo get_url("admin/list_teams.php"); ?>" class="btn btn-secondary">Back</a>
    </div>
    <div class="card mx-auto" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title">Team Details</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Name: <?php echo $team['team_name'] ?? 'Unknown'; ?></li>
                <li class="list-group-item">Coach: <?php echo $team['coach'] ?? 'Unknown'; ?></li>
                <li class="list-group-item">League: <?php echo $team['league'] ?? 'Unknown'; ?></li>
                <li class="list-group-item">Created At: <?php echo $team['created_at'] ?? 'Unknown'; ?></li>
                <li class="list-group-item">Updated At: <?php echo $team['updated_at'] ?? 'Unknown'; ?></li>
            </ul>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>