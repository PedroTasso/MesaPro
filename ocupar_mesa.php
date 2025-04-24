<?php
require_once "config.php";

if (isset($_POST['mesaId']) && is_numeric($_POST['mesaId'])) {

    $id = mysqli_real_escape_string($link, $_POST['mesaId']);
    $status = 2; // 2 = occupied
    $reservado_por = "-";
    $tel_reserva = "-";
    $hora_reserva = "-";

    $sql = "UPDATE tables SET status = ?, reservado_por = ?, tel_reseva = ?, hora_reserva = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "isssi", $status, $reservado_por, $tel_reserva, $hora_reserva, $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "Mesa ocupada com sucesso!";
        } else {
            echo "Erro ao ocupar mesa: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Erro na preparação da query: " . mysqli_error($link);
    }
}

mysqli_close($link);
?>
