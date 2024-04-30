<?php
//fss22 4/30/24
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../partials/flash.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

// Process form data for adding a team
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["league"]) && isset($_POST["team_name"]) && isset($_POST["coach"])) {
    // Retrieve form data for team creation
    $league = se($_POST, "league", "", false);
    $teamName = se($_POST, "team_name", "", false);
    $coach = se($_POST, "coach", "", false);

    // Validate form data for team creation
    if (empty($league) || empty($teamName) || empty($coach)) {
        flash("Please fill out all required fields for team creation", "danger");
    } else {
        // Insert data into Teams table
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Teams (league, team_name, coach) VALUES (:league, :teamName, :coach)");
        try {
            $stmt->bindParam(":league", $league);
            $stmt->bindParam(":teamName", $teamName);
            $stmt->bindParam(":coach", $coach);
            if ($stmt->execute()) {
                flash("Team added successfully", "success");
            } else {
                flash("An error occurred while adding the team", "danger");
            }
        } catch (PDOException $e) {
            flash("An error occurred while adding the team", "danger");
            error_log(var_export($e->errorInfo, true));
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