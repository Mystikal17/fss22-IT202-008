<?php
// Include necessary files and check permissions
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

// Initialize variables
$id = se($_GET, "id", -1, false);
$team = [];

// Check if ID is valid
if ($id < 1) {
    flash("Invalid ID passed", "danger");
    die(header("Location: " . get_url("admin/list_teams.php")));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $league = se($_POST, "league", "", false);
    $teamName = se($_POST, "team_name", "", false);
    $coach = se($_POST, "coach", "", false);

    // Validate form data
    if (empty($league) || empty($teamName) || empty($coach)) {
        flash("Please fill out all required fields", "danger");
    } else {
        // Update team data in the database
        $db = getDB();
        $query = "UPDATE `Teams` SET `league` = :league, `team_name` = :teamName, `coach` = :coach WHERE `id` = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":league", $league);
        $stmt->bindParam(":teamName", $teamName);
        $stmt->bindParam(":coach", $coach);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            flash("Team updated successfully", "success");
        } else {
            flash("An error occurred while updating the team", "danger");
        }
    }
}

// Fetch team data from the database
if ($id > -1) {
    $db = getDB();
    $query = "SELECT `league`, `team_name`, `coach` FROM `Teams` WHERE `id` = :id";
    $stmt = $db->prepare($query);
    $stmt->execute([":id" => $id]);
    $team = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Define the form fields for updating team data
$form = [
    ["type" => "text", "name" => "league", "placeholder" => "League", "label" => "League", "value" => $team["league"], "rules" => ["required" => true]],
    ["type" => "text", "name" => "team_name", "placeholder" => "Team Name", "label" => "Team Name", "value" => $team["team_name"], "rules" => ["required" => true]],
    ["type" => "text", "name" => "coach", "placeholder" => "Coach", "label" => "Coach", "value" => $team["coach"], "rules" => ["required" => true]],
];

?>
<div class="container-fluid">
    <h3>Edit Team</h3>
    <div>
        <a href="<?php echo get_url("admin/list_teams.php"); ?>" class="btn btn-secondary">Back</a>
    </div>
    <form method="POST">
        <?php foreach ($form as $field) : ?>
            <?php render_input($field); ?>
        <?php endforeach; ?>
        <?php render_button(["text" => "Update Team", "type" => "submit"]); ?>
    </form>
</div>

<?php
// Include flash messages
require_once(__DIR__ . "/../../../partials/flash.php");
?>