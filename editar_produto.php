<?php
// Inclui o arquivo de configuração do banco de dados
require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $tipo = $_POST['tipo_id'];
    $categoria = $_POST['categoria_id'];
    $preco = str_replace(',', '.', $_POST['preco']);

    $sql = "UPDATE produtos SET nome = ?, tipo_id = ?, categoria_id = ?, preco = ? WHERE id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("siidi", $nome, $tipo, $categoria, $preco, $id);

    if ($stmt->execute()) {
        echo "Produto atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar produto: " . $stmt->error;
    }

    $stmt->close();
    $link->close();
}
?>
