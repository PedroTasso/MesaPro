<?php
//inicializa sessão
session_start();


if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php"); // Redireciona para a página de login se não estiver logado
    exit;
}

if ($_SESSION["funcao"] !== 1) {
    // Se não for gerente, redireciona ou exibe uma mensagem de acesso negado
    // Exemplo de redirecionamento:
    header("location: denied.php");
    exit;
    // Ou você pode simplesmente exibir uma mensagem:
    // echo "Acesso negado.";
}

// Inclui o arquivo de configuração do banco de dados
require_once "config.php";

// Define a variável para armazenar o nome do usuario
$user = "";

// Prepara e executa a consulta para obter o nome do gerente
$sql = "SELECT nome FROM employees WHERE id = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "i", $param_id);

    // Set parameters
    $param_id = $_SESSION["id"];

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        // Store result
        mysqli_stmt_store_result($stmt);

        // Check if user exists
        if (mysqli_stmt_num_rows($stmt) == 1) {
            // Bind result variables
            mysqli_stmt_bind_result($stmt, $user);

            // Fetch result
            if (mysqli_stmt_fetch($stmt)) {
                // Agora a variável $user contém o nome do gerente
            }
        } else {
            // Usuário não encontrado (erro inesperado)
            // Você pode adicionar tratamento de erro aqui
            echo "Erro: Usuário não encontrado.";
            exit;
        }
    } else {
        echo "Oops! Algo deu errado. Por favor, tente novamente mais tarde.";
        exit;
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

/************************************************************************
 * MESAS
 ************************************************************************/

// Consulta para obter todas as mesas do banco de dados
$sql_select = "SELECT id, numero, capacidade, hora_reserva, reservado_por, tel_reseva, status FROM tables ORDER BY numero";
$result = mysqli_query($link, $sql_select);
$mesas = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $mesas[] = $row;
    }
    mysqli_free_result($result);
} else {
    echo "Erro ao buscar mesas: " . mysqli_error($link);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garçom | MesaPro</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
</head>
<script>
    //Funçãopara ver comanda

    // Funçaõ para fechar comanda e liberar mesa
    // Função para abrir o modal de fechar comanda
function fecharComanda(mesaId) {
    document.getElementById('mesa_id_modal').value = mesaId;
    document.getElementById('modal-fechar-comanda').style.display = 'block';
}

// Função para fechar o modal
function fecharModalFecharComanda() {
    document.getElementById('modal-fechar-comanda').style.display = 'none';
}

// Envio do formulário via AJAX
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-fechar-comanda');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const mesaId = document.getElementById('mesa_id_modal').value;
            fetch('fechar_comanda.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'mesa_id=' + encodeURIComponent(id)
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    alert('Comanda finalizada com sucesso!');
                    window.location.reload();
                } else {
                    alert('Erro ao finalizar comanda: ' + data.mensagem);
                }
            })
            .catch(() => alert('Erro na requisição.'));
        });
    }
});
    // Fim
</script>
<body>
<header>
        <div class="menubar">
            <div>
                <h1>Restaurante X</h1>
            </div>
            <div class="menu-container-user">
                <button class="menu-button-user" aria-haspopup="true" aria-expanded="false">
                    <span class="user">Bem-vindo, <?php echo $user; ?>!</span>
                    <i class="fi fi-rr-circle-user"></i>
                </button>
                <div class="menu-dropdown-user" aria-label="Menu dropdown">
                    <form action="logout.php" method="post">
                        <button type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>
    <main>
        <!--Verificar mesas ocupdas-->
        <section id="pagina-mesa" class="card-container">
            <?php foreach ($mesas as $mesa): ?>
                <div id="mesa-<?= $mesa['id'] ?>" class="card">
                        <div class="card-body" >
                            <h2 class="card-title"> Mesa <?= $mesa['numero'] ?>
                                <span class="ribbon <?= ($mesa['status'] == 2) ? 'occupied' : 
                                (($mesa['status'] == 1) ? 'reserved' : 'available')?>"></span>
                            </h2>
                            <p class="card-text">
                                <i class="fi fi-rr-users"></i>
                                <span>Capacidade: <?= $mesa['capacidade'] ?> pessoas</span>
                            </p>
                            <p class="card-text">
                                <i class="fi fi-rr-clock"></i>
                                <span>Pedido: --</span>
                            </p>
                            <p class="card-text">
                                <i class="fi fi-rr-phone"></i>
                                <span>Telefone: <?= $mesa['tel_reseva'] ?></span>
                            </p>
                            <p class="card-text">
                                <i class="fi fi-rr-calendar"></i>
                                <span>Reservado por: <?= $mesa['reservado_por'] ?></span>
                            </p>
                            <p class="card-text">
                                <i class="fi fi-rr-clock"></i>
                                <span>Hora da reserva: <?= $mesa['hora_reserva'] ?></span>
                            </p>
                            </div>  
                        
                            <!-- Botão Ver Comanda (só ativo para mesas ocupadas) e direciona a inserir produtodos nas comandas -->
                            
                            <div class="card-buttons"> 
                            <button class="card-btn primary"onclick="verComanda(<?= $mesa['id'] ?>)" 
                            <?= $mesa['status'] != 2 ? 'disabled' : '' ?>>Ver Comanda </button>
                            

                            <!-- Botão Fechar Comanda (só ativo para mesas ocupadas) e libera mesa -->
                            <button id="" class="card-btn alternate"onclick="finalizarPedido(<?= $mesa['id'] ?>)" 
                            <?= $mesa['status'] != 2 ? 'disabled' : '' ?>>Fechar Comanda</button>
                            
                            
                            </div>
                        
                        
                        </div> 
                            
                            

                        </div>
                    </div>
                              
                </div>

            <?php endforeach; ?>
        </section>
    </main>

    <footer>
        <div class="footer-info">
            <p>&copy; 2025 Restaurante X. Todos os direitos reservados.</p>
        </div>
    </footer>
    <script src="js/garcom/garcom.js"></script>
    <script src="js/garcom/comandas.js"></script>
</body>
</html>