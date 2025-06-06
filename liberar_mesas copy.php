<?php
$id = $_GET['id'] ?? null;

if ($id) {
    $conn = new mysqli("localhost", "usuario", "senha", "banco");
    $conn->set_charset("utf8");

    $sql = "UPDATE mesas SET status = 'Disponível' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "Mesa liberada";
} else {
    echo "ID da mesa não fornecido";
}
?>
