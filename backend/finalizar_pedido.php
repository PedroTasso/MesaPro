<?php
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(['sucesso' => false, 'erro' => 'Método não permitido']);
    exit;
}

$mesa_id = isset($_POST['mesa_id']) ? intval($_POST['mesa_id']) : 0;
$forma_pagamento = isset($_POST['forma_pagamento']) ? $_POST['forma_pagamento'] : '';

if (!$mesa_id) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID da mesa não fornecido']);
    exit;
}

// Iniciar transação
mysqli_begin_transaction($link);

try {
    // Fechar todos os pedidos abertos para esta mesa
    $sql_update = "UPDATE pedidos SET status = 'fechado', forma_pagamento = ?, data_fechamento = NOW() WHERE mesa_id = ? AND status = 'aberto'";
    $stmt_update = mysqli_prepare($link, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "si", $forma_pagamento, $mesa_id);
    mysqli_stmt_execute($stmt_update);
    
    // Verificar se algum pedido foi afetado
    if (mysqli_affected_rows($link) <= 0) {
        throw new Exception("Nenhum pedido aberto encontrado para esta mesa");
    }
    
    // Atualizar status da mesa para disponível
    $sql_mesa = "UPDATE tables SET status = 0 WHERE id = ?"; // 0 = Disponível
    $stmt_mesa = mysqli_prepare($link, $sql_mesa);
    mysqli_stmt_bind_param($stmt_mesa, "i", $mesa_id);
    mysqli_stmt_execute($stmt_mesa);
    
    // Commit da transação
    mysqli_commit($link);
    
    echo json_encode(['sucesso' => true]);
    
} catch (Exception $e) {
    // Rollback em caso de erro
    mysqli_rollback($link);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}

// Fechar conexão
mysqli_close($link);
?>
