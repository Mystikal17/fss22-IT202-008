<?php
//fss22 4/30/24
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../partials/flash.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["league"]) && isset($_POST["team_name"]) && isset($_POST["coach"])) {
    // Retrieve form data for team creation
    $league = se($_POST, "league", "", false);
    $teamName = se($_POST, "team_name", "", false);
    $coach = se($_POST, "coach", "", false);

    // Validate form data for team creation
    if (empty($league) || empty($teamName) || empty($coach)) {
        flash("Please fill out all required fields for team creation", "danger");
    } else {
        // Check if team with the same name already exists
        $db = getDB();
        $checkQuery = "SELECT COUNT(*) AS teamCount FROM `Teams` WHERE `team_name` = :teamName";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->execute([":teamName" => $teamName]);
        $teamCount = $checkStmt->fetch(PDO::FETCH_ASSOC)["teamCount"];

        if ($teamCount > 0) {
            flash("Team with the same name already exists", "danger");
        } else {
            // Insert data into Teams table
            $insertQuery = "INSERT INTO `Teams` (`league`, `team_name`, `coach`) VALUES (:league, :teamName, :coach)";
            $stmt = $db->prepare($insertQuery);
            $stmt->execute([
                ":league" => $league,
                ":teamName" => $teamName,
                ":coach" => $coach
            ]);
            flash("Team added successfully", "success");
        }
    }
}

?>

<div class="container-fluid">
    <h1>Add Team</h1>
    <form method="POST">
        <?php render_input(["id" => "league", "name" => "league", "label" => "League", "rules" => ["required" => true]]); ?>
        <?php render_input(["id" => "team_name", "name" => "team_name", "label" => "Team Name", "rules" => ["required" => true]]); ?>
        <?php render_input(["id" => "coach", "name" => "coach", "label" => "Coach", "rules" => ["required" => true]]); ?>
        <?php render_button(["text" => "Add Team", "type" => "submit"]); ?>
    </form>
</div>

<?php

require_once(__DIR__ . "/../../../partials/flash.php");
?>