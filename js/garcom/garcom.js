function abrirModalReserva() {
    document.getElementById("modalOverlayReserva").style.display = "flex";
}


document.addEventListener('DOMContentLoaded', function() {
    const menuButtonUser = document.querySelector('.menu-button-user');
    const menuDropdownUser = document.querySelector('.menu-dropdown-user');

    menuButtonUser.addEventListener('click', function() {
        menuDropdownUser.classList.toggle('show');
        menuButtonUser.setAttribute('aria-expanded', menuDropdownUser.classList.contains('show'));
    });
    
    document.addEventListener('click', function(event) {
        if (!menuButtonUser.contains(event.target) && !menuDropdownUser.contains(event.target)) {
            menuDropdownUser.classList.remove('show');
            menuButtonUser.setAttribute('aria-expanded', 'false');
        }
    });
});



// Função para redirecionar para a página de comanda
function verComanda(numeroMesa) {
    // Armazena a mesa selecionada
    localStorage.setItem('mesaSelecionada', numeroMesa);
    
    // Redireciona para comandas.html com o parâmetro
    window.location.href = `comandas.php?mesa=${numeroMesa}`;
}

// Funções de navegação
function paginaMesas() {
    // Se já está na página de mesas, não faz nada
    if (!window.location.href.includes('comandas.php')) {
        window.location.href = 'garcom.php';
    }
}

function paginaComanda() {
    // Redireciona para a última mesa visualizada ou mostra alerta
    const mesaSalva = localStorage.getItem('mesaSelecionada');
    if (mesaSalva) {
        window.location.href = `comandas.php?mesa=${mesaSalva}`;
    } else {
        alert('Selecione uma mesa primeiro');
    }
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
                body: 'mesa_id=' + encodeURIComponent(mesaId)
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

//final da função

document.addEventListener("DOMContentLoaded", () => {
    fetch("buscar_mesa.php")
        .then(response => response.text())
        .then(text => {
            let mesas = [];
            try {
                mesas = text ? JSON.parse(text) : [];
            } catch(e) {
                console.error("Erro ao parsear JSON:", e);
            }
            const container = document.getElementById("pagina-mesa");
            //container.innerHTML = "";

            mesas.forEach(mesa => {
                const card = document.createElement("div");
                card.className = "card";
                card.id = `mesa-${mesa.id}`;

                // Define a classe da fita de status
                let ribbonClass = "";
switch (mesa.status) {
    case 1:
        ribbonClass = "available";
        break;
    case 2:
        ribbonClass = "reserved";
        break;
    case 3:
        ribbonClass = "occupied";
        break;
    default:
        ribbonClass = "";
}

                // Botão "Ver Comanda" só habilita se estiver Ocupada
                const botaoVer = mesa.status === "Ocupada"
                    ? `<button class="card-btn primary" onclick="verComanda('${mesa.id}')">Ver Comanda</button>`
                    : `<button class="card-btn primary" disabled>Ver Comanda</button>`;

                card.innerHTML = `
                    <div class="card-body">
                        <h2 class="card-title">
                            Mesa ${mesa.numero}
                            <span class="ribbon ${ribbonClass}"></span>
                        </h2>
                        <p class="card-text">
                            <i class="fi fi-rr-users"></i>
                            <span class="card-itemtitle">Capacidade: </span>
                            <span class="capacity">${mesa.capacidade}</span> pessoas
                        </p>
                        <p class="card-text">
                            <i class="fi fi-rr-utensils"></i>
                            <span class="card-itemtitle">Status: </span>
                            <span>${mesa.status}</span>
                        </p>
                    </div>
                    <div class="card-buttons">
                        ${botaoVer}
                        <button class="card-btn alternate" onclick="fecharConta('${mesa.numero}')">Fechar Conta</button>
                    </div>
                `;

                container.appendChild(card);
            });
        })
        .catch(error => { console.error("Erro ao buscar mesas:", error);}
    );

//------------------------


// Variável global para armazenar os pedidos da mesa
let pedidos = [];

/**
 * Função para buscar os pedidos registrados para uma mesa.
 * Retorna uma Promise com o array de pedidos.
 */

const urlParams = new URLSearchParams(window.location.search);
const mesaId = urlParams.get("mesa");

if (mesaId) {
    fetch(`/MesaPro/backend/obter_comanda.php?mesa_id=${mesaId}`)
        .then(response => response.json())
        .then(data => console.log(data))
        .catch(error => console.error("Erro ao buscar pedidos:", error));
} else {
    console.error("mesa_id está indefinido!");
}
if (typeof mesaId !== "undefined" && mesaId !== null) {
    // Fazer a requisição
} else {
    console.error("mesa_id não foi definido corretamente!");
}

function buscarPedidos(mesaId) {
  return fetch(`/MesaPro/get_comanda_pedidos.php?mesa_id=${mesaId}`)
    .then(response => response.json());
}

/**
 * Função para finalizar o pedido da mesa.
 * Essa função pode ser chamada por qualquer botão que deseja fechar o pedido.
 */
function finalizarPedido(mesaId) {
  // Buscar os pedidos da mesa
  buscarPedidos(mesaId)
    .then(data => {
      // Verifica se existem pedidos registrados
      if (data && data.length > 0) {
        pedidos = data;  // atualiza a variável global
        // Calcula o total do pedido
        const total = pedidos.reduce((acc, item) => {
          return acc + (parseFloat(item.preco) * item.quantidade);
        }, 0);
        // Solicita a forma de pagamento (substitua por um modal se preferir)
        const formaPagamento = prompt("Informe a forma de pagamento (ex.: Dinheiro, Cartão):");
        if (!formaPagamento) {
          alert("Forma de pagamento é obrigatória!");
          return;
        }
        // Monta os dados do pedido
        const dadosPedido = {
          mesa_id: mesaId,
          itens: pedidos,
          forma_pagamento: formaPagamento,
          total: total
        };

        // Envia os dados para finalizar o pedido
        fetch('/MesaPro/backend/finalizar_pedido.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify(dadosPedido)
        })
        .then(response => response.json())
        .then(data => {
          if (data.sucesso) {
            // Atualiza o status da mesa para "disponível" (por exemplo, status = 0)
            return fetch('/MesaPro/backend/atualizar_status_mesa.php', {
              method: 'POST',
              headers: {'Content-Type': 'application/x-www-form-urlencoded'},
              body: `id=${mesaId}&status=0`
            });
          } else {
            throw new Error(data.mensagem || "Erro ao finalizar pedido.");
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.sucesso) {
            alert("✅ Pedido finalizado com sucesso e mesa liberada!");
            window.location.reload();
          } else {
            throw new Error(data.mensagem || "Erro ao atualizar status da mesa.");
          }
        })
        .catch(error => {
          console.error("Erro ao finalizar pedido:", error);
          alert("Erro ao finalizar pedido: " + error.message);
        });
      } else {
        alert("⚠️ Nenhum pedido registrado para esta mesa.");
      }
    })
    .catch(error => {
      console.error("Erro ao buscar pedidos:", error);
      alert("Erro ao buscar pedidos: " + error.message);
    });
}

// Torna a função acessível globalmente
window.finalizarPedido = finalizarPedido;

//---------------------------




//-------------------

document.addEventListener('DOMContentLoaded', function() {
    const menuButtonUser = document.querySelector('.menu-button-user');
    const menuDropdownUser = document.querySelector('.menu-dropdown-user');

    menuButtonUser.addEventListener('click', function() {
        menuDropdownUser.classList.toggle('show');
        menuButtonUser.setAttribute('aria-expanded', menuDropdownUser.classList.contains('show'));
    });
    
    document.addEventListener('click', function(event) {
        if (!menuButtonUser.contains(event.target) && !menuDropdownUser.contains(event.target)) {
            menuDropdownUser.classList.remove('show');
            menuButtonUser.setAttribute('aria-expanded', 'false');
        }
    });
});

});
