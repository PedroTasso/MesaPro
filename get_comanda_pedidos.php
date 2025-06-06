<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

session_start();
require_once "config.php";


if(isset($_GET['mesa_id'])){
    $mesa_id = intval($_GET['mesa_id']);
    $sql = "SELECT cp.id, p.nome as produto, cp.preco, cp.quantidade, cp.info
            FROM comandapedidos cp
            JOIN produtos p ON cp.produto_id = p.id
            WHERE cp.mesa_id = ?";
    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $mesa_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $pedidos = [];
        while($row = mysqli_fetch_assoc($result)){
            $pedidos[] = $row;
        }
        echo json_encode($pedidos);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>