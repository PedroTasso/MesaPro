<?php
// Inclui o arquivo de configuração do banco de dados
require_once "config.php";

if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $funcionarioId = mysqli_real_escape_string($link, $_POST['id']);

    // Prepara a query para excluir a mesa
    $sql = "DELETE FROM employees WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind do parâmetro
        mysqli_stmt_bind_param($stmt, "i", $funcionarioId);

        // Executa a query
        if (mysqli_stmt_execute($stmt)) {
            header("Location: gerente.php");
        } else {
            echo "Erro ao excluir o funcionário: " . mysqli_error($link);
        }

        // Fecha a statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Erro na preparação da query: " . mysqli_error($link);
    }
} else {
    echo "ID do funcionário inválido.";
}

$_POST = null;
// Fecha a conexão
mysqli_close($link);
?>