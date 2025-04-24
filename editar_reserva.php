<?php
// Inclui o arquivo de configuração do banco de dados
require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $hora_reserva = $_POST['hora_reserva'];

    $sql = "UPDATE tables SET reservado_por = ?, tel_reseva = ?, hora_reserva = ? WHERE id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("sssi", $nome, $telefone, $hora_reserva, $id);

    if ($stmt->execute()) {
        echo "Reserva atualizada com sucesso!";
    } else {
        echo "Erro ao atualizar reserva: " . $stmt->error;
    }

    $stmt->close();
    $link->close();
}
?>
