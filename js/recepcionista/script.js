let reservaEditando = false;
let mesa = null;
let idMesa = null;

function reservarMesa(id) {
    reservaEditando = false;
    abrirModalReserva();

    // Obtém o elemento da mesa que foi clicada
    mesa = document.getElementById(id);
    idMesa = id;
}

function editarReserva(id) {
    reservaEditando = true;
    // Obtém o elemento da mesa que foi clicada
    mesa = document.getElementById(id);

    // Obtém os dados atuais da reserva
    let nome = mesa.querySelector(".cliente-reserva").textContent.trim();
    let telefone = mesa.querySelector(".cliente-telefone").textContent.trim();
    let horario_data = mesa.querySelector(".horario-reservado").textContent.trim();

    // Separa o horario e a data
    let [horario, data] = horario_data.split(" ");

    // Preenche os campos do formulário
    document.getElementById("customerName").value = nome;
    document.getElementById("customerPhone").value = telefone;
    document.getElementById("reservationTime").value = horario;
    document.getElementById("reservationDate").value = data;

    abrirModalReserva();
}

function ocuparMesa(id) {
    // Obtém o elemento da mesa que foi clicada
    mesa = document.getElementById(id);

    // Modifica a classe do título da mesa para indicar que está ocupada
    mesa.querySelector(".card-title span").setAttribute("class", "ribbon occupied");

    // Remove os elementos da reserva
    removerElementosReserva(mesa);

    // Adiciona o status de pedido
    mesa.querySelector(".card-body").innerHTML += `
        <p class="card-text">
            <i class="fi fi-rr-utensils"></i>
            <span class="card-itemtitle">Pedido: </span>
            <span class="order-status">Não registrado</span>
        </p>
    `;

    // Atualiza o segundo botão (o de "Reservar") e altera suas propriedades
    atualizarBotaoMesa(mesa, "Liberar", "liberarMesa", id);
    // Desabilita o botão ocupar
    mesa.querySelector(".card-buttons button:nth-child(1)").disabled = true;
}

function liberarMesa(id) {
    // Obtém o elemento da mesa que foi clicada
    mesa = document.getElementById(id);

    // Modifica a classe do título da mesa para indicar que está ocupada
    mesa.querySelector(".card-title span").setAttribute("class", "ribbon available");

    // Remove os elementos da reserva
    removerElementosReserva(mesa, [".order-status", ".waiting-time"]);

    // Atualiza o segundo botão (o de "Liberar") e altera suas propriedades
    atualizarBotaoMesa(mesa, "Reservar", "reservarMesa", id);

    // Habilita o botão ocupar
    mesa.querySelector(".card-buttons button:nth-child(1)").disabled = false;
}
/*
document.getElementById("newForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Evita recarregar a página

    let nome = document.getElementById("customerName").value;
    let telefone = document.getElementById("customerPhone").value;
    let horario = document.getElementById("reservationTime").value;
    let data = document.getElementById("reservationDate").value;

    // Verifica se os campos estão preenchidos
    if (!nome || !telefone || !horario || !data) {
        alert("Preencha todos os campos obrigatórios!");
        return;
    }

    if (reservaEditando) {
        mesa.querySelector(".cliente-reserva").textContent = nome;
        mesa.querySelector(".cliente-telefone").textContent = telefone;
        mesa.querySelector(".horario-reservado").textContent = `${horario} ${data}`;
    } else {
        mesa.querySelector(".card-body").innerHTML += `
            <p class="card-text">
                <i class="fi fi-rr-reservation-table"></i>
                <span class="card-itemtitle">Reservado: </span>
                <span class="cliente-reserva">${nome}</span>
            </p>
            <p class="card-text">
                <i class="fi fi-rr-circle-phone"></i>
                <span class="card-itemtitle">Telefone: </span>
                <span class="cliente-telefone">${telefone}</span>
            </p>
            <p class="card-text">
                <i class="fi fi-rr-calendar-clock"></i>
                <span class="card-itemtitle">Horário: </span>
                <span class="horario-reservado">${horario} ${data}</span>
            </p>
        `;
        
        // Modifica a classe do título da mesa para indicar que está ocupada
        mesa.querySelector(".card-title span").setAttribute("class", "ribbon reserved");

         // Atualiza o segundo botão (o de "Reservar") e altera suas propriedades
        atualizarBotaoMesa(mesa, "Editar", "editarReserva", idMesa);
    }

    fecharModal();
});*/

function atualizarBotaoMesa(mesa, texto, funcao, id) {
    let btn = mesa.querySelector(".card-buttons button:nth-child(2)");
    btn.textContent = texto;
    btn.setAttribute("onclick", `${funcao}('${id}')`);
}

function removerElementosReserva(mesa, seletores = [".cliente-reserva", ".cliente-telefone", ".horario-reservado"]) {
    let elementos = mesa.querySelectorAll(seletores.join(", "));
    elementos.forEach(elemento => elemento.closest(".card-text").remove());
}

function abrirModalReserva() {
    document.getElementById("modalOverlayReserva").style.display = "flex";
}

function fecharModal() {
    document.getElementById("modalOverlayReserva").style.display = "none";
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
