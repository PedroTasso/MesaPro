<?php
require_once '../config.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $sql = "UPDATE tables SET status = 'Disponível' WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["sucesso" => true]);
        } else {
            echo json_encode(["erro" => "Falha ao atualizar status."]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["erro" => "Erro na preparação da consulta."]);
    }
} else {
    echo json_encode(["erro" => "ID da mesa não fornecido."]);
}

mysqli_close($link);
?>
