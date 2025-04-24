<?php
// Inclui o arquivo de configuração do banco de dados
require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $funcao = $_POST['funcao'];

    $query = "UPDATE employees SET nome = ?, telefone = ?, email = ?, funcao = ?";
    $params = [$nome, $telefone, $email, $funcao];
    $types = "ssss";

    if (!empty($_POST['password'])) {
        $senha = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query .= ", senha = ?";
        $params[] = $senha;
        $types .= "s";
    }

    $query .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $link->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo "Funcionário atualizado com sucesso.";
    } else {
        echo "Erro ao atualizar funcionário: " . $stmt->error;
    }

    $stmt->close();
    $link->close();
}
?>
