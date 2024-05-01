<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<h1>Home</h1>
<?php

if (is_logged_in(true)) {
    //echo "Welcome home, " . get_username();
    //comment this out if you don't want to see the session variables
    error_log("Session data: " . var_export($_SESSION, true));
}
?>

<button class="btn btn-primary" onclick="window.location.href='/Project/admin/add_teams.php'">Add Team</button>
<button class="btn btn-primary" onclick="window.location.href='/Project/admin/add_players.php'">Add Player</button>
<button class="btn btn-primary" onclick="window.location.href='/Project/admin/list_players.php'">List of Players</button>
<button class="btn btn-primary" onclick="window.location.href='/Project/admin/list_teams.php'">List of Teams</button>

<?php
require(__DIR__ . "/../../partials/flash.php");
?>