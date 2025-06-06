<?php
require_once "config.php";

$mesa_id = $_GET["mesa_id"];

$sql = "SELECT p.id, pr.nome, p.quantidade, pr.preco, p.observacao 
        FROM comanda_pedidos p 
        JOIN produtos pr ON p.produto_id = pr.id 
        WHERE p.mesa_id = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $mesa_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $pedidos = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $pedidos[] = $row;
    }

    echo json_encode(["sucesso" => true, "pedidos" => $pedidos]);
} else {
    echo json_encode(["sucesso" => false, "mensagem" => "Erro ao buscar pedidos"]);
}
