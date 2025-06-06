<?php
require_once '../config.php';

if (!isset($_GET['mesa'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Mesa não especificada']);
    exit;
}

$mesa_id = intval($_GET['mesa']);

// Obter itens da comanda (adapte de acordo com sua estrutura de banco de dados)
$sql = "SELECT pi.id, p.nome, pi.quantidade, pi.preco_unitario as preco, pi.observacao 
       FROM pedidos_itens pi 
       JOIN pedidos pe ON pi.pedido_id = pe.id 
       JOIN produtos p ON pi.produto_id = p.id 
       WHERE pe.mesa_id = ? AND pe.status = 'aberto'"; // Supondo que pedidos têm um status "aberto"

$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $mesa_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

echo json_encode([
    'sucesso' => true,
    'mesa_id' => $mesa_id,
    'items' => $items
]);

mysqli_close($link);
?>
