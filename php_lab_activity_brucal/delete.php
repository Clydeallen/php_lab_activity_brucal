<?php
require_once "dbcon.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if ($id) {
    $statement = $conn->prepare("DELETE FROM users WHERE id = ?");
    $statement->bind_param("i", $id);
    $statement->execute();
    $statement->close();
}

header("Location: index.php?msg=deleted");
exit;
?>