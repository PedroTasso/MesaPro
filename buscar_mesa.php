<?php
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "SELECT id, numero, capacidade, hora_reserva, reservado_por, tel_reseva, status  FROM tables WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            echo json_encode($row);
        } else {
            echo json_encode(["erro" => "Mesa não encontrada"]);
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($link);
?>
