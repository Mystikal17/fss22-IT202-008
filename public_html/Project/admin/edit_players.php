<?php
//fss22 4/30/24
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>

<?php
// Initialize variables
$id = se($_GET, "id", -1, false);
$player = [];

// Check if ID is valid
if ($id < 1) {
    flash("Invalid ID passed", "danger");
    die(header("Location: " . get_url("admin/list_players.php")));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $playerName = se($_POST, "name", "", false);
    $position = se($_POST, "position", "", false);
    $age = se($_POST, "age", -1, false);
    $nationality = se($_POST, "nationality", "", false);

    // Validate form data
    if (empty($playerName) || empty($position) || $age < 0 || empty($nationality)) {
        flash("Please fill out all required fields", "danger");
    } else {
        // Update player data in the database
        $db = getDB();
        $query = "UPDATE `Players` SET `name` = :name, `position` = :position, `age` = :age, `nationality` = :nationality WHERE `id` = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":name", $playerName);
        $stmt->bindParam(":position", $position);
        $stmt->bindParam(":age", $age);
        $stmt->bindParam(":nationality", $nationality);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            flash("Player updated successfully", "success");
        } else {
            flash("An error occurred while updating the player", "danger");
        }
    }
}

// Fetch player data from the database
if ($id > -1) {
    $db = getDB();
    $query = "SELECT `name`, `position`, `age`, `nationality` FROM `Players` WHERE `id` = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $player = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch player data
        if (!$player) {
            flash("Player not found", "danger");
            die(header("Location:" . get_url("admin/list_players.php")));
        }
    } catch (PDOException $e) {
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
        die(header("Location:" . get_url("admin/list_players.php")));
    }
}

// Define the form fields for updating player data
$form = [
    ["type" => "text", "name" => "name", "placeholder" => "Player Name", "label" => "Player Name", "value" => $player["name"], "rules" => ["required" => true]],
    ["type" => "text", "name" => "position", "placeholder" => "Position", "label" => "Position", "value" => $player["position"], "rules" => ["required" => true]],
    ["type" => "number", "name" => "age", "placeholder" => "Age", "label" => "Age", "value" => $player["age"], "rules" => ["required" => true]],
    ["type" => "text", "name" => "nationality", "placeholder" => "Nationality", "label" => "Nationality", "value" => $player["nationality"], "rules" => ["required" => true]],
];

?>
<div class="container-fluid">
    <h3>Edit Player</h3>
    <div>
        <a href="<?php echo get_url("admin/list_players.php"); ?>" class="btn btn-secondary">Back</a>
    </div>
    <form method="POST">
        <?php foreach ($form as $k => $v) {

            render_input($v);
        } ?>
        <?php render_button(["text" => "Update Player", "type" => "submit"]); ?>
    </form>
</div>

<?php
// Include flash messages
require_once(__DIR__ . "/../../../partials/flash.php");
?>