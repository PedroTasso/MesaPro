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

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comanda | Restaurante X</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/comandas.css">
    <link rel='stylesheet'
        href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>

</head>

<script>

function abrirModalRegistrarPedido() {
    const select = document.getElementById('produto');
    // Limpa as opções anteriores
    select.innerHTML = '<option value="">Selecione</option>';
    
    fetch('get_produtos.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(produto => {
                // Cria a opção com o nome do produto e o id
                const option = new Option(produto.nome, produto.id);
                select.add(option);
            });
        })
        .catch(error => {
            console.error('Erro ao buscar produto:', error);
        });
    
    document.getElementById("modalOverlayReserva").style.display = "flex";
}

function carregarPedidos() {
    const params = new URLSearchParams(window.location.search);
    const mesa_id = params.get('mesa');
    
    fetch('get_comanda_pedidos.php?mesa_id=' + encodeURIComponent(mesa_id))
      .then(response => response.json())
      .then(data => {
         const lista = document.getElementById('lista-pedidos');
         lista.innerHTML = "";
         let total = 0;
         data.forEach(pedido => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${pedido.id}</td>
                            <td>${pedido.produto}</td>
                            <td>${parseFloat(pedido.preco).toFixed(2)}</td>
                            <td>${pedido.quantidade}</td>
                            <td>${pedido.info}</td>`;
            lista.appendChild(tr);
            total += parseFloat(pedido.preco) * parseInt(pedido.quantidade);
         });
         document.querySelector('.total').textContent = "Total: R$ " + total.toFixed(2);
      })
      .catch(error => console.error('Erro ao carregar pedidos:', error));
}

// Chama a função ao carregar a página
document.addEventListener('DOMContentLoaded', carregarPedidos);


    function fecharModal(idModal) {
        alert(idModal);
        document.getElementById(idModal).style.display = 'none';
    }

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
        <div class="background-pattern">
            <div class="comanda-container">
                <div class="comanda-header">
                    <h2 id="titulo-mesa">Mesa </h2>
                    <button class="btn" onclick="window.location.href='garcom.php'">⬅ Voltar para mesas</button>
                </div>
                <p><strong>🚍️ Capacidade:</strong> <span id="capacidade"></span> pessoas</p>
                <p><strong>🍽 Pedido:</strong> <span id="status-pedido"></span></p>
                <p><strong>⏳ Tempo de espera:</strong> <span id="tempo-espera">--</span> minutos</p>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produto</th>
                            <th>Preço (R$)</th>
                            <th>Quantidade</th>
                            <th>Informações</th>
                        </tr>
                    </thead>
                    <tbody id="lista-pedidos"></tbody>
                </table>

                <div class="total">Total: R$ 0,00</div>

                <div class="btn-group">
                    <button id="openModalBtn" class="btn" onclick="abrirModalRegistrarPedido()">Registrar
                        pedido</button>

                    <button id="FinalizarModal" class="btn" onclick="finalizarPedido()"> Finalizar pedido </button>
                </div>
            </div>
        </div>

        <!-- Modal Registrar Pedido -->
        <div id= "modalcomanda" class="modal">
            <div class="modal-content"  id="modalOverlayReserva">
             <button class="close-btn" onclick="fecharModalComanda()"><i class="fi fi-rr-cross"></i></button><form id="formRegistrar">
                    <label for="produto">Selecionar Item</label>
                    <select id="produto" required>

                        <option value="">Selecione</option>

                    </select>

                    <label for="quantidade">Quantidade</label>
                    <select id="quantidade">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select>

                    <label for="observacao">Observação</label>
                    <textarea id="observacao" rows="3" placeholder="Ex: sem cebola, sem mostarda..."></textarea>
                    <button type="submit" class="btn-adicionar">Adicionar</button>
                    <!-- criar tabela e função par pedidos -->
                </form>

            </div>
        </div>
        </div>
        </div>

    </main>
    <footer>
        <div class="footer-info">
            <p>&copy; 2025 Restaurante X. Todos os direitos reservados.</p>
            <p><a href="#">Contato</a> | <a href="#">Sobre</a></p>
        </div>
    </footer>

    <script src="js/garcom/garcom.js"></script>
    <script src="js/garcom/comandas.js"></script>


</body>

</html>