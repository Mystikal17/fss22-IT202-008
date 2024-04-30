<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

// Handle the toggle first so select pulls fresh data
if (isset($_POST["player_id"])) {
    $player_id = se($_POST, "player_id", "", false);
    if (!empty($player_id)) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE Players SET is_active = !is_active WHERE id = :pid");
        try {
            $stmt->execute([":pid" => $player_id]);
            flash("Updated Player", "success");
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    }
}

$query = "SELECT id, name, age, position, nationality, if(is_active, 'active', 'disabled') as 'Active' FROM Players";
$params = null;
$search = "";
if (isset($_POST["player"])) {
    $search = se($_POST, "player", "", false);
    $query .= " WHERE name LIKE :player";
    $params = [":player" => "%$search%"];
}
$query .= " ORDER BY updated_at DESC LIMIT 10";

$db = getDB();
$stmt = $db->prepare($query);
$players = [];

try {
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        $players = $results;
    } else {
        flash("No matches found", "warning");
    }
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
}

$table = ["data" => $players, "post_self_form" => ["name" => "player_id", "label" => "Toggle", "classes" => "btn btn-secondary"]];

?>

<div class="container-fluid">
    <h1>List Players</h1>
    <form method="POST">
        <?php render_input(["type" => "search", "name" => "player", "placeholder" => "Player Filter", "value" => $search]); ?>
        <?php render_button(["text" => "Search", "type" => "submit"]); ?>
    </form>
    <?php render_table($table); ?>

    <script>
        let forms = [...document.forms];
        forms.shift();
        let search = "<?php se($search); ?>";
        for (let form of forms) {
            let ele = document.createElement("input");
            ele.type = "hidden";
            ele.name = "player";
            ele.value = search;
            form.appendChild(ele);
        }
    </script>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>