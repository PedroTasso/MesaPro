let addTable = document.getElementById("add-table");
let mesa = document.getElementById("pagina-mesa");
let cardapio = document.getElementById("pagina-cardapio");
let func = document.getElementById("pagina-funcionarios");
let qtde_mesas = 8;

function paginaMesa() {
    cardapio.style.display = "none";
    func.style.display = "none";
    mesa.style.display = "flex";
}

function paginaCardapio() {
    cardapio.style.display = "block";
    func.style.display = "none";
    mesa.style.display = "none";
}

function paginaFuncionario() {
    cardapio.style.display = "none";
    func.style.display = "block";
    mesa.style.display = "none";
}

function adicionarMesa() {
    let novaMesa = document.createElement("div");
    novaMesa.className = "card";
    novaMesa.setAttribute("id", `mesa-${qtde_mesas}`);

    novaMesa.innerHTML = `
        <div class="card-body">
            <h2 class="card-title">
                Mesa ${qtde_mesas}
                <span class="ribbon available"></span>
            </h2>
            <p class="card-text">
                <i class="fi fi-rr-users"></i>
                <span class="card-itemtitle">Capacidade: </span>4 pessoas
            </p>
        </div>
        <div class="card-buttons">
            <button class="card-btn primary">Editar</button>
            <button class="card-btn alternate" onclick="excluirMesa('mesa-${qtde_mesas}')">Excluir</button>
        </div>`;

    // Insere a nova mesa antes do botão "Adicionar"
    mesa.insertBefore(novaMesa, addTable);

    qtde_mesas += 1;
}

function excluirMesa(idMesa) {
    let mesa = document.getElementById(idMesa);
    if (mesa) {
        mesa.remove();
    }
}

function cadastrarFuncionario() {
    document.getElementById("modalOverlayFuncionario").style.display = "flex";
}

function cadastrarProduto() {
    document.getElementById("modalOverlayCardapio").style.display = "flex";
}

document.addEventListener("DOMContentLoaded", function () {
    // Obtém os campos de entrada e a tabela de funcionários
    let inputNome = document.getElementById("nome");
    let inputCpf = document.getElementById("cpf");
    let tabela = document.getElementById("employeeTable").getElementsByTagName("tbody")[0];

    // Adiciona eventos para filtrar enquanto o usuário digita
    inputNome.addEventListener("input", function () {
        filtrarTabela();
    });

    inputCpf.addEventListener("input", function () {
        filtrarTabela();
    });

    function filtrarTabela() {
        let filtroNome = inputNome.value.trim().toLowerCase();
        let filtroCpf = inputCpf.value.trim().toLowerCase();

        let linhas = tabela.getElementsByTagName("tr");

        for (let linha of linhas) {
            let nomeFuncionario = linha.cells[0].textContent.trim().toLowerCase();
            let cpfFuncionario = linha.cells[1].textContent.trim().toLowerCase();

            // Verifica se o Nome ou CPF contém o filtro digitado
            let correspondeNome = filtroNome === "" || nomeFuncionario.includes(filtroNome);
            let correspondeCpf = filtroCpf === "" || cpfFuncionario.includes(filtroCpf);

            // Mostra ou oculta a linha conforme a busca
            linha.style.display = (correspondeNome && correspondeCpf) ? "" : "none";
        }
    }
});

document.addEventListener("DOMContentLoaded", function () {
    // Obtém os campos de entrada e a tabela de produtos
    let inputId = document.getElementById("id");
    let inputNome = document.getElementById("produto");
    let tabela = document.getElementById("productTable").getElementsByTagName("tbody")[0];

    // Adiciona eventos para filtrar enquanto o usuário digita
    inputId.addEventListener("input", function () {
        filtrarTabela();
    });

    inputNome.addEventListener("input", function () {
        filtrarTabela();
    });

    function filtrarTabela() {
        let filtroId = inputId.value.trim().toLowerCase();
        let filtroNome = inputNome.value.trim().toLowerCase();

        let linhas = tabela.getElementsByTagName("tr");

        for (let linha of linhas) {
            let idProduto = linha.cells[0].textContent.trim().toLowerCase();
            let nomeProduto = linha.cells[1].textContent.trim().toLowerCase();

            // Verifica se o ID ou Nome do produto contém o filtro digitado
            let correspondeId = filtroId === "" || idProduto.includes(filtroId);
            let correspondeNome = filtroNome === "" || nomeProduto.includes(filtroNome);

            // Mostra ou oculta a linha conforme a busca
            linha.style.display = (correspondeId && correspondeNome) ? "" : "none";
        }
    }
});