<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once '../config.php'; // ajuste o caminho conforme necessário

if (isset($_GET['mesa_id'])) {
    $mesa = intval($_GET['mesa_id']);
    // Exemplo: suponha que a tabela "tables" tenha colunas: numero, capacidade, status, tempo_espera
    $sql = "SELECT capacidade, status, tempo_espera FROM tables WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $mesa);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $capacidade, $status, $tempo_espera);
        if (mysqli_stmt_fetch($stmt)) {
            echo json_encode([
                'capacidade' => $capacidade,
                'status' => $status,
                'tempo_espera' => $tempo_espera
            ]);
        } else {
            echo json_encode(['erro' => 'Mesa não encontrada']);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['erro' => 'Erro na preparação da query']);
    }
} else {
    echo json_encode(['erro' => 'Parâmetro mesa não informado']);
}