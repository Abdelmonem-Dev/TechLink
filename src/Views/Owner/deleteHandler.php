<?php

if (isset($_GET['table']) && isset($_GET['column']) && isset($_GET['id'])) {
    $table = preg_replace('/[^a-zA-Z_]/', '', $_GET['table']); // Sanitize table name
    $column = preg_replace('/[^a-zA-Z_]/', '', $_GET['column']); // Sanitize column name
    $id = (int) $_GET['id']; // Sanitize the ID

    if ($id > 0 && !empty($table) && !empty($column)) {
        // Use deleteHandler with dynamic table and column names
        $conditions = [$column => 'id'];
        $parameters = ['id' => $id];

        $deleted = deleteHandler($table, $conditions, $parameters);

        if ($deleted) {
            // Record deleted successfully
            header("Location: Dashboard.php?success=1");
            exit();
        } else {
            // Error deleting record
            header("Location: Dashboard.php?error=1");
            exit();
        }
    } else {
        // Invalid parameters
        header("Location: Dashboard.php?error=invalid_params");
        exit();
    }
} else {
    // Redirect if parameters are missing
    header("Location: Dashboard.php?error=missing_params");
    exit();
}
