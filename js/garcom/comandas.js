// Obtém referências aos elementos do DOM
const modalcomanda = document.getElementById('modalcomanda');
//const openBtn = document.getElementById('openModalBtn');
const listaPedidos = document.getElementById('lista-pedidos');
const totalElement = document.querySelector('.total');
const urlParams = new URLSearchParams(window.location.search);
// Aqui o parâmetro é "mesa" na URL, que representa o id da mesa
const mesa_id = urlParams.get("mesa");
let pedidos = [];

let elemento = document.getElementById("openModalBnt");
if (elemento) {
    elemento.onclick = function() {
        alert("Clicado!");
    };
} else {
    console.error("Elemento não encontrado!");
}

document.addEventListener("DOMContentLoaded", function() {
    let elemento = document.getElementById("openModalBnt");
    if (elemento) {
        elemento.onclick = function() {
            alert("Clicado!");
        };
    }
});



// Função para abrir o modal de comanda
openBtn.onclick = () => modalcomanda.style.display = 'flex';


// Função para fechar o modal de comanda
function fecharModalComanda() {
  modalcomanda.style.display = 'none';
}

// Fecha o modal clicando fora da área (no fundo escuro)
modalcomanda.onclick = function(event) {
  if (event.target === modalcomanda) {
    fecharModalComanda();
  }
};

// Envio do formulário para adicionar um pedido
document.getElementById('formRegistrar').onsubmit = function(e) {
  e.preventDefault();

  const produto_id = document.getElementById('produto').value;
  const quantidade = document.getElementById('quantidade').value;
  const info = document.getElementById('observacao').value;

  // Envia os dados via POST usando o id da mesa (mesa_id)
  fetch('add_comanda_pedido.php', {
         method: 'POST',
         headers: {'Content-Type': 'application/x-www-form-urlencoded'},
         body: 'produto_id=' + encodeURIComponent(produto_id) +
               '&quantidade=' + encodeURIComponent(quantidade) +
               '&info=' + encodeURIComponent(info) +
               '&mesa_id=' + encodeURIComponent(mesa_id)
  })
  .then(response => response.json())
  .then(data => {
         if (data.sucesso) {
             alert('Pedido adicionado com sucesso!');
             // Aqui você pode atualizar a tabela de pedidos se necessário.
         } else {
             alert('Erro ao adicionar pedido: ' + data.mensagem);
         }
  })
  .catch(error => {
         console.error('Erro:', error);
         alert('Erro na requisição.');
  });
  
  // Fecha o modal após enviar o pedido
  fecharModalComanda();
};

// Função para atualizar a tabela de pedidos (caso necessário)
function atualizarTabela() {
  listaPedidos.innerHTML = "";
  let totalFinal = 0;
  pedidos.forEach(p => {
    totalFinal += parseFloat(p.preco) * p.quantidade;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${p.id}</td>
      <td>${p.nome}</td>
      <td>${parseFloat(p.preco).toFixed(2)}</td>
      <td>${p.quantidade}</td>
      <td>${p.observacao ? "📝 " + p.observacao : ""}</td>
    `;
    listaPedidos.appendChild(tr);
  });
  totalElement.innerText = "Total: R$ " + totalFinal.toFixed(2);
}

// Função para finalizar o pedido
function finalizarPedido() {
  if (pedidos.length === 0) {
    alert("⚠️ Nenhum pedido registrado para esta mesa.");
    return;
  }
  
  const formaPagamento = document.getElementById('pagamento').value;
  
  const dadosPedido = {
    mesa_id: mesa_id,
    itens: pedidos,
    forma_pagamento: formaPagamento,
    total: calcularTotal()
  };
  
  fetch('/MesaPro/backend/finalizar_pedido.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(dadosPedido)
  })
  .then(response => response.json())
  .then(data => {
    if (data.sucesso) {
      // Atualiza o status da mesa para disponível (0 = Disponível)
      return fetch(`/MesaPro/backend/atualizar_status_mesa.php`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${mesa_id}&status=0`
      });
    } else {
      throw new Error(data.erro || 'Erro ao finalizar pedido');
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.sucesso) {
      alert("✅ Pedido finalizado com sucesso! Mesa liberada.");
      window.location.href = 'garcom.php';
    } else {
      throw new Error(data.erro || 'Erro ao atualizar status da mesa');
    }
  })
  .catch(error => {
    console.error('Erro:', error);
    alert("❌ Ocorreu um erro ao finalizar o pedido: " + error.message);
  });
}

// Função auxiliar para calcular o total do pedido
function calcularTotal() {
  return pedidos.reduce((total, item) => {
    return total + (parseFloat(item.preco) * item.quantidade);
  }, 0);
}

// Carrega os dados da mesa utilizando o id (mesa_id)
if (mesa_id) {
  fetch(`/MesaPro/backend/obter_mesa.php?mesa_id=${mesa_id}`)
    .then(res => res.json())
    .then(dados => {
      // Atualiza o título com o número da mesa vindo do banco de dados
      document.getElementById("titulo-mesa").textContent = `Mesa ${mesa_id}`;
      // Exibe a capacidade conforme o valor do BD
      document.getElementById("capacidade").textContent = dados.capacidade;
      
      // Para "Pedido", gera um número aleatório que é armazenado no localStorage (único por mesa)
      let pedidoCount = localStorage.getItem("pedidoCount_" + mesa_id);
      if (!pedidoCount) {
         pedidoCount = Math.floor(Math.random() * 100) + 1;
         localStorage.setItem("pedidoCount_" + mesa_id, pedidoCount);
      }
      document.getElementById("status-pedido").textContent = pedidoCount;
      
      // Define o tempo de espera fixo em 30 minutos
      document.getElementById("tempo-espera").textContent = 30;
    })
    .catch(error => {
      console.error("Erro ao carregar dados da mesa:", error);
    });
}

// Carrega os produtos do banco de dados
window.onload = () => {
  fetch('/MesaPro/get_produtos.php')
    .then(response => response.json())
    .then(data => {
      const select = document.getElementById('produto');
      select.innerHTML = '<option value="">Selecione</option>';
      data.forEach(item => {
        const option = document.createElement('option');
        option.value = `${item.id}|${item.nome}|${item.preco}`;
        option.textContent = item.nome;
        select.appendChild(option);
      });
    })
    .catch(error => {
      console.error('Erro ao carregar produtos:', error);
    });
};