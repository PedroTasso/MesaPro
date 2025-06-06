<?php
header('Content-Type: application/json');
require_once "config.php"; // Certifique-se de que este arquivo configura a conexão com o banco

$sql = "SELECT id, nome, preco FROM produtos";
$result = mysqli_query($link, $sql);
$produtos = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $produtos[] = $row;
    }
}

echo json_encode($produtos);