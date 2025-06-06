<?php

define('DB_SERVER', 'localhost:3306');
define('DB_USER', 'root');
define('DB_PASS', ''); // Deixe em branco se não houver senha
define('DB_NAME', 'mesapro');

$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug: " . $output . "' );</script>";
}

if ($link === false) {
    debug_to_console("Falha");

    die("ERROR: Could not connect. " . mysqli_connect_error());
}

?>