<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

// Handle the toggle first so select pulls fresh data
if (isset($_POST["team_id"])) {
    $team_id = se($_POST, "team_id", "", false);
    if (!empty($team_id)) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE Teams SET is_active = !is_active WHERE id = :tid");
        try {
            $stmt->execute([":tid" => $team_id]);
            flash("Updated Team", "success");
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    }
}

$query = "SELECT id, league, team_name, coach, if(is_active, 'active', 'disabled') as 'Active' FROM Teams";
$params = null;
$search = "";
if (isset($_POST["team"])) {
    $search = se($_POST, "team", "", false);
    $query .= " WHERE team_name LIKE :team";
    $params = [":team" => "%$search%"];
}
$query .= " ORDER BY updated_at DESC LIMIT 10";

$db = getDB();
$stmt = $db->prepare($query);
$teams = [];

try {
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        $teams = $results;
    } else {
        flash("No matches found", "warning");
    }
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
}

$table = ["data" => $teams, "post_self_form" => ["name" => "team_id", "label" => "Toggle", "classes" => "btn btn-secondary"]];

?>

<div class="container-fluid">
    <h1>List Teams</h1>
    <form method="POST">
        <?php render_input(["type" => "search", "name" => "team", "placeholder" => "Team Filter", "value" => $search]); ?>
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
            ele.name = "team";
            ele.value = search;
            form.appendChild(ele);
        }
    </script>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>