<?php
//inicializa sessão
session_start();


if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php"); // Redireciona para a página de login se não estiver logado
    exit;
}

if ($_SESSION["funcao"] !== 2) {
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

/************************************************************************
 * RESERVA
 ************************************************************************/

// Adicionar reservas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adicionar_reserva'])) {
    $id = mysqli_real_escape_string($link, $_POST['mesaId']);
    $status = 1; // 1 = reserved
    $nome = mysqli_real_escape_string($link, $_POST['customerName']);
    $telefone = mysqli_real_escape_string($link, $_POST['customerPhone']);
    $horario = mysqli_real_escape_string($link, $_POST['reservationTime']);
    $data = mysqli_real_escape_string($link, $_POST['reservationDate']);
    $hora_reserva = $data . ' ' . $horario;

    // Verifica se os campos estão preenchidos
    if (!empty($nome) && !empty($telefone) && !empty($horario) && !empty($data)) {
        $sql = "UPDATE tables SET status = ?, reservado_por = ?, tel_reseva = ?, hora_reserva = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "isssi", $status, $nome, $telefone, $hora_reserva, $id);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: recepcionista.php");
            } else {
                echo "Erro ao adicionar reserva: " . mysqli_error($link);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Erro na preparação da query: " . mysqli_error($link);
        }
    } else {
        echo "<script>alert('Preencha todos os campos obrigatórios!');</script>";
    }
}

// Close connection
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepcionista | MesaPro</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <script>
        /************************************************************************
         * RESERVAS
         ************************************************************************/

        function reservarMesaPhp(mesaId) {
            document.getElementById("mesaId").value = mesaId;
            document.getElementById("modalOverlayReserva").style.display = "flex";
        }

        function ocuparMesaPhp(mesaId) {
            fetch('ocupar_mesa.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'mesaId=' + mesaId,
            })
            .then(response => response.text())
            .then(data => {
                //alert(data); // Exibe a resposta do servidor
                // Recarrega a página para atualizar a lista de mesas
                window.location.reload();
            })
            .catch((error) => {
                console.error('Erro:', error);
            });
        }

        function liberarMesaPhp(mesaId) {
            fetch('liberar_mesa.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'mesaId=' + mesaId,
            })
            .then(response => response.text())
            .then(data => {
                //alert(data); // Exibe a resposta do servidor
                // Recarrega a página para atualizar a lista de mesas
                window.location.reload();
            })
            .catch((error) => {
                console.error('Erro:', error);
            });
        }

        function abrirModalEditarReserva(id) {
            fetch(`buscar_mesa.php?id=${id}`) 
                .then(response => response.json())
                .then(data => {
                    // Preenche os campos do formulário
                    document.getElementById('mesaId').value = data.id;
                    document.getElementById('customerName').value = data.reservado_por;
                    document.getElementById('customerPhone').value = data.tel_reseva;

                    // Separa o horario e a data
                    const [data_reservada, horario] = data.hora_reserva.split(" ");
                    document.getElementById('reservationTime').value = horario;
                    document.getElementById('reservationDate').value = data_reservada;

                    // Define o comportamento de envio do formulário
                    document.getElementById('reservationForm').onsubmit = function(event) {
                        salvarEdicaoReserva(event);
                    };

                    // Exibe o modal
                    document.getElementById('modalOverlayReserva').style.display = 'flex';
                })
                .catch(error => {
                    console.error('Erro ao buscar mesa:', error);
                });
        }

        function editarReservaPhp() {
            event.preventDefault(); // evita o recarregamento da página

            const id = document.getElementById('mesaId').value;
            const nome = document.getElementById('customerName').value;
            const telefone = document.getElementById('customerPhone').value;
            const horario = document.getElementById('reservationTime').value;
            const data_reservada = document.getElementById('reservationDate').value;

            fetch('editar_reserva.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}&nome=${nome}&telefone=${telefone}&hora_reserva=${data_reservada} ${horario}`
            })
            .then(response => response.text())
            .then(data => {
                //console.log(data); // opcional: mostrar resposta do servidor
                fecharModal(); // fecha o modal
                window.location.reload(); // recarrega a página para mostrar a atualização
            })
            .catch(error => {
                console.error('Erro ao editar reserva:', error);
            });
        }
    </script>
</head>
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
            <!--<div>
                <span>Usuário</span>
                <i class="fi fi-rr-circle-user"></i>
                <form action="logout.php" method="post">
                    <button type="submit">Logout</button>
                </form>
            </div>-->
        </div>
    </header>
    <main>
        <section id="pagina-mesa" class="card-container">
            <?php if (empty($mesas)): ?>
                <p>Nenhuma mesa cadastrada.</p>
            <?php else: ?>
                <?php foreach ($mesas as $mesa): ?>
                    <?php
                        $statusClass = '';
                        switch ($mesa['status']) {
                            case 0:
                                $statusClass = 'available';
                                break;
                            case 1:
                                $statusClass = 'reserved';
                                break;
                            case 2:
                                $statusClass = 'occupied';
                                break;
                            default:
                                $statusClass = 'unknown'; 
                        }
                    ?>
                    <div id="mesa-<?php echo $mesa['id']; ?>" class="card">
                        <div class="card-body">
                            <h2 class="card-title">
                                Mesa <?php echo htmlspecialchars($mesa['numero']); ?>
                                <span class="ribbon <?php echo $statusClass; ?>"></span>
                            </h2>
                            <p class="card-text">
                                <i class="fi fi-rr-users"></i>
                                <span class="card-itemtitle">Capacidade: </span><?php echo htmlspecialchars($mesa['capacidade']); ?> pessoas
                            </p>
                            <?php if($mesa['status'] == 1): ?>
                                <p class="card-text">
                                    <i class="fi fi-rr-reservation-table"></i>
                                    <span class="card-itemtitle">Reservado: </span>
                                    <span class="cliente-reserva"><?php echo $mesa['reservado_por']; ?></span>
                                </p>
                                <p class="card-text">
                                    <i class="fi fi-rr-circle-phone"></i>
                                    <span class="card-itemtitle">Telefone: </span>
                                    <span class="cliente-telefone"><?php echo $mesa['tel_reseva']; ?></span>
                                </p>
                                <?php 
                                    $horaReserva = strtotime($mesa['hora_reserva']);
                                    $dataFormatada = date('d/m/Y', $horaReserva);
                                    $horaFormatada = date('H:i', $horaReserva);
                                ?>
                                <p class="card-text">
                                    <i class="fi fi-rr-calendar-clock"></i>
                                    <span class="card-itemtitle">Horário: </span>
                                    <span class="horario-reservado"><?php echo $dataFormatada . ' às ' . $horaFormatada; ?></span>
                                </p>
                            <?php elseif($mesa['status'] == 2): ?>
                                <p class="card-text">
                                    <i class="fi fi-rr-utensils"></i>
                                    <span class="card-itemtitle">Pedido: </span>
                                    <span class="order-status">Não registrado</span>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="card-buttons">
                            <?php if($mesa['status'] == 1): ?>
                                <button class="card-btn primary" onclick="ocuparMesaPhp(<?php echo $mesa['id']; ?>)">Ocupar</button>
                                <button class="card-btn alternate" onclick="abrirModalEditarReserva(<?php echo $mesa['id']; ?>)">Editar</button>
                            <?php elseif($mesa['status'] == 2): ?>
                                <button class="card-btn primary" onclick="ocuparMesaPhp(<?php echo $mesa['id']; ?>)" disabled>Ocupar</button>
                                <button class="card-btn alternate" onclick="liberarMesaPhp(<?php echo $mesa['id']; ?>)">Liberar</button>
                            <?php else: ?>
                                <button class="card-btn primary" onclick="ocuparMesaPhp(<?php echo $mesa['id']; ?>)">Ocupar</button>
                                <button class="card-btn alternate" onclick="reservarMesaPhp(<?php echo $mesa['id']; ?>)">Reservar</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <!--<div id="mesa-1" class="card">
                <div class="card-body">
                    <h2 class="card-title">
                        Mesa 1
                        <span class="ribbon available"></span>
                    </h2>
                    <p class="card-text">
                        <i class="fi fi-rr-users"></i>
                        <span class="card-itemtitle">Capacidade: </span>
                        <span class="capacity">4</span> pessoas
                    </p>
                </div>
                <div class="card-buttons">
                    <button class="card-btn primary" onclick="ocuparMesa('mesa-1')">Ocupar</button>
                    <button class="card-btn alternate" onclick="reservarMesa('mesa-1')">Reservar</button>
                </div>
            </div>

            <div id="mesa-2" class="card">
                <div class="card-body">
                    <h2 class="card-title">
                        Mesa 2
                        <span class="ribbon occupied"></span>
                    </h2>
                    <p class="card-text">
                        <i class="fi fi-rr-users"></i>
                        <span class="card-itemtitle">Capacidade: </span>
                        <span class="capacity">4</span> pessoas
                    </p>
                    <p class="card-text">
                        <i class="fi fi-rr-utensils"></i>
                        <span class="card-itemtitle">Pedido: </span>
                        <span class="order-status">Aguardando preparo</span>
                    </p>
                    <p class="card-text">
                        <i class="fi fi-rr-clock-three"></i>
                        <span class="card-itemtitle">Tempo de espera: </span>
                        <span class="waiting-time">20</span> minutos
                    </p>
                </div>
                <div class="card-buttons">
                    <button class="card-btn primary" onclick="ocuparMesa('mesa-2')" disabled>Ocupar</button>
                    <button class="card-btn alternate" onclick="liberarMesa('mesa-2')">Liberar</button>
                </div>
            </div>

            <div id="mesa-3" class="card">
                <div class="card-body">
                    <h2 class="card-title">
                        Mesa 3
                        <span class="ribbon occupied"></span>
                    </h2>
                    <p class="card-text">
                        <i class="fi fi-rr-users"></i>
                        <span class="card-itemtitle">Capacidade: </span>
                        <span class="capacity">4</span> pessoas
                    </p>
                    <p class="card-text">
                        <i class="fi fi-rr-utensils"></i>
                        <span class="card-itemtitle">Pedido: </span>
                        <span class="order-status">Não registrado</span>
                    </p>
                </div>
                <div class="card-buttons">
                    <button class="card-btn primary" onclick="ocuparMesa('mesa-3')" disabled>Ocupar</button>
                    <button class="card-btn alternate" onclick="liberarMesa('mesa-3')">Liberar</button>
                </div>
            </div>

            <div id="mesa-4" class="card">
                <div class="card-body">
                    <h2 class="card-title">
                        Mesa 4
                        <span class="ribbon reserved"></span>
                    </h2>
                    <p class="card-text">
                        <i class="fi fi-rr-users"></i>
                        <span class="card-itemtitle">Capacidade: </span>
                        <span class="capacity">4</span> pessoas
                    </p>
                    <p class="card-text">
                        <i class="fi fi-rr-reservation-table"></i>
                        <span class="card-itemtitle">Reservado: </span>
                        <span class="cliente-reserva">Lorena Rayssa</span>
                    </p>
                    <p class="card-text">
                        <i class="fi fi-rr-circle-phone"></i>
                        <span class="card-itemtitle">Telefone: </span>
                        <span class="cliente-telefone">(75) 99697-8119</span>
                    </p>
                    <p class="card-text">
                        <i class="fi fi-rr-calendar-clock"></i>
                        <span class="card-itemtitle">Horário: </span>
                        <span class="horario-reservado">19:30 2024-09-23</span>
                    </p>
                </div>
                <div class="card-buttons">
                    <button class="card-btn primary" onclick="ocuparMesa('mesa-4')">Ocupar</button>
                    <button class="card-btn alternate" onclick="editarReserva('mesa-4')">Editar</button>
                </div>
            </div>

            <div id="mesa-5" class="card">
                <div class="card-body">
                    <h2 class="card-title">
                        Mesa 5
                        <span class="ribbon reserved"></span>
                    </h2>
                    <p class="card-text">
                        <i class="fi fi-rr-users"></i>
                        <span class="card-itemtitle">Capacidade: </span>
                        <span class="capacity">4</span> pessoas
                    </p>
                    <p class="card-text">
                        <i class="fi fi-rr-reservation-table"></i>
                        <span class="card-itemtitle">Reservado: </span>
                        <span class="cliente-reserva ">Igor Yago</span>
                    </p>
                    <p class="card-text">
                        <i class="fi fi-rr-circle-phone"></i>
                        <span class="card-itemtitle">Telefone: </span>
                        <span class="cliente-telefone">(11) 98693-5511</span>
                    </p>
                    <p class="card-text">
                        <i class="fi fi-rr-calendar-clock"></i>
                        <span class="card-itemtitle">Horário: </span>
                        <span class="horario-reservado ">19:30 2024-09-23</span>
                    </p>
                </div>
                <div class="card-buttons">
                    <button class="card-btn primary" onclick="ocuparMesa('mesa-5')">Ocupar</button>
                    <button class="card-btn alternate" onclick="editarReserva('mesa-5')">Editar</button>
                </div>
            </div>

            <div id="mesa-6" class="card">
                <div class="card-body">
                    <h2 class="card-title">
                        Mesa 6
                        <span class="ribbon available"></span>
                    </h2>
                    <p class="card-text">
                        <i class="fi fi-rr-users"></i>
                        <span class="card-itemtitle">Capacidade: </span>
                        <span class="capacity">4</span> pessoas
                    </p>
                </div>
                <div class="card-buttons">
                    <button class="card-btn primary" onclick="ocuparMesa('mesa-6')">Ocupar</button>
                    <button class="card-btn alternate" onclick="reservarMesa('mesa-6')">Reservar</button>
                </div>
            </div>

            <div id="mesa-7" class="card">
                <div class="card-body">
                    <h2 class="card-title">
                        Mesa 7
                        <span class="ribbon occupied"></span></h2>
                    <p class="card-text">
                        <i class="fi fi-rr-users"></i>
                        <span class="card-itemtitle">Capacidade: </span>
                        <span class="capacity">4</span> pessoas
                    </p>
                    <p class="card-text">
                        <i class="fi fi-rr-utensils"></i>
                        <span class="card-itemtitle">Pedido: </span>
                        <span class="order-status">Em preparo</span>
                    </p>
                </div>
                <div class="card-buttons">
                    <button class="card-btn primary" onclick="ocuparMesa('mesa-7')" disabled>Ocupar</button>
                    <button class="card-btn alternate" onclick="liberarMesa('mesa-7')">Liberar</button>
                </div>
            </div>-->

            <div class="modal-overlay" id="modalOverlayReserva">
                <div class="modal">
                    <div class="modal-header">
                        <h2><i class="fi fi-rr-reservation-table"></i> Reserva</h2>
                        <button class="close-button" onclick="fecharModal()">
                            <i class="fi fi-rr-cross"></i>
                        </button>
                    </div>
                    <div class="modal-content">
                        <form id="reservationForm" method="post">
                            <input type="hidden" name="mesaId" id="mesaId">
                            <div class="form-group">
                                <label for="customerName">Nome</label>
                                <input type="text" name="customerName" id="customerName">
                            </div>
                            <div class="form-group">
                                <label for="customerPhone">Telefone</label>
                                <input type="text" name="customerPhone" id="customerPhone">
                            </div>
                            <div class="form-group">
                                <label for="reservationTime">Horario</label>
                                <input type="time" name="reservationTime" id="reservationTime">
                            </div>
                            <div class="form-group">
                                <label for="reservationDate">Dia</label>
                                <input type="date" name="reservationDate" id="reservationDate">
                            </div>
                            <div class="form-buttons">
                                <button class="card-btn primary" type="submit"  name="adicionar_reserva">Salvar</button>
                                <button class="card-btn alternate" type="reset">Limpar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer>
        <div class="footer-info">
            <p>&copy; 2025 Restaurante X. Todos os direitos reservados.</p>
            <p><a href="#">Contato</a> | <a href="#">Sobre</a></p>
        </div>
    </footer>
    <script src="js/recepcionista.js"></script>
</body>
</html>