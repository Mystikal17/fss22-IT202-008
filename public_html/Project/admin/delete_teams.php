<?php
//fss22 4/30/24
session_start();
require(__DIR__ . "/../../../lib/functions.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

$id = se($_GET, "id", -1, false);
if ($id < 1) {
    flash("Invalid ID passed to delete", "danger");
    die(header("Location: " . get_url("admin/list_teams.php")));
}

$db = getDB();
$query = "DELETE FROM `Teams` WHERE id = :id";
try {
    $stmt = $db->prepare($query);
    $stmt->execute([":id" => $id]);
    flash("Deleted record with ID $id", "success");
} catch (PDOException $e) {
    error_log("Error deleting team with ID $id: " . $e->getMessage());
    flash("Error deleting record", "danger");
}

die(header("Location: " . get_url("admin/list_teams.php")));
?>