<?php
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../partials/flash.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

// Process form data for adding a player
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "add") {
    $playerName = se($_POST, "name", "", false);
    $position = se($_POST, "position", "", false);
    $age = se($_POST, "age", "", false);
    $nationality = se($_POST, "nationality", "", false);

    // Validate form data
    if (empty($playerName) || empty($position) || empty($age) || empty($nationality)) {
        flash("Please fill out all required fields for adding a player", "danger");
    } else {
        // Insert player data into the database
        $db = getDB();
        $query = "INSERT INTO `Players` (`name`, `position`, `age`, `nationality`) VALUES (:name, :position, :age, :nationality)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ":name" => $playerName,
            ":position" => $position,
            ":age" => $age,
            ":nationality" => $nationality
        ]);
        flash("Player added successfully", "success");
    }
}

// Render the form for adding players
?>
<div class="container-fluid">
    <h3>Add Soccer Player</h3>
    <form method="POST">
        <?php render_input(["type" => "text", "name" => "name", "placeholder" => "Player Name", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "text", "name" => "position", "placeholder" => "Position", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "number", "name" => "age", "placeholder" => "Age", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "text", "name" => "nationality", "placeholder" => "Nationality", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "hidden", "name" => "action", "value" => "add"]); ?>
        <?php render_button(["text" => "Add Player", "type" => "submit"]); ?>
    </form>
</div>

<?php
// Include flash messages
require_once(__DIR__ . "/../../../partials/flash.php");
?>
<script>
    // JavaScript function to handle button click and redirect
    function redirectToAddPlayer() {
        window.location.href = '<?php echo get_url("admin/add_players.php"); ?>';
    }
</script>