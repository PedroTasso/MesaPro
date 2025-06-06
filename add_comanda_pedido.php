<?php

session_start();
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera os dados enviados  
    $produto_id = isset($_POST['produto_id']) ? intval($_POST['produto_id']) : 0;
    $quantidade = isset($_POST['quantidade']) ? intval($_POST['quantidade']) : 0;
    $info = isset($_POST['info']) ? trim($_POST['info']) : "";
    $mesa_id = isset($_POST['mesa_id']) ? intval($_POST['mesa_id']) : 0;
    
    // Recupera o preço do produto
    $sql = "SELECT preco FROM produtos WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
         mysqli_stmt_bind_param($stmt, "i", $produto_id);
         mysqli_stmt_execute($stmt);
         mysqli_stmt_bind_result($stmt, $preco);
         if (!mysqli_stmt_fetch($stmt)) {
             echo json_encode(['sucesso' => false, 'mensagem' => 'Produto não encontrado.']);
             exit;
         }
         mysqli_stmt_close($stmt);
    } else {
         echo json_encode(['sucesso' => false, 'mensagem' => 'Erro na consulta do produto.']);
         exit;
    }
    
    // Insere o pedido com mesa_id na tabela comandapedidos
    $sql_insert = "INSERT INTO comandapedidos (mesa_id, produto_id, preco, quantidade, info) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql_insert)) {
         // "i" para mesa_id, "i" para produto_id, "d" para preco, "i" para quantidade, "s" para info
         mysqli_stmt_bind_param($stmt, "iidis", $mesa_id, $produto_id, $preco, $quantidade, $info);
         if (mysqli_stmt_execute($stmt)) {
             echo json_encode(['sucesso' => true]);
         } else {
             echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao inserir pedido.']);
         }
         mysqli_stmt_close($stmt);
    } else {
         echo json_encode(['sucesso' => false, 'mensagem' => 'Erro na preparação da query de inserção.']);
    }
    exit;
}

echo json_encode(['sucesso' => false, 'mensagem' => 'Requisição inválida.']);