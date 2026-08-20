<?php

require_once __DIR__ . '/config/Database.php';

require_once __DIR__ . '/classes/Hospede.php';
require_once __DIR__ . '/classes/Quarto.php';
require_once __DIR__ . '/classes/Servico.php';
require_once __DIR__ . '/classes/Reserva.php';
require_once __DIR__ . '/classes/Pagamento.php';
require_once __DIR__ . '/classes/TipoQuarto.php';
require_once __DIR__ . '/classes/ReservaServico.php';

echo "======================================" . PHP_EOL;
echo " SISTEMA DE GESTÃO DE HOTEL / POUSADA " . PHP_EOL;
echo "======================================" . PHP_EOL;

try {

    // CONEXÃO
    $database = new Database();
    $pdo = $database->conectar();

    echo PHP_EOL;
    echo "[OK] Conexão com PostgreSQL realizada." . PHP_EOL;

    // LISTAGENS
    echo PHP_EOL;
    echo "---------- DADOS DO SISTEMA ----------" . PHP_EOL;

    $hospedes = Hospede::listar($pdo);
    $quartos = Quarto::listar($pdo);
    $servicos = Servico::listar($pdo);
    $reservas = Reserva::listar($pdo);
    $pagamentos = Pagamento::listar($pdo);
    $tiposQuarto = TipoQuarto::listar($pdo);
    $reservasServicos = ReservaServico::listar($pdo);

    echo "Hóspedes cadastrados: " . count($hospedes) . PHP_EOL;
    echo "Quartos cadastrados: " . count($quartos) . PHP_EOL;
    echo "Serviços cadastrados: " . count($servicos) . PHP_EOL;
    echo "Reservas cadastradas: " . count($reservas) . PHP_EOL;
    echo "Pagamentos cadastrados: " . count($pagamentos) . PHP_EOL;
    echo "Tipos de quarto: " . count($tiposQuarto) . PHP_EOL;
    echo "Serviços em reservas: " . count($reservasServicos) . PHP_EOL;

    // TESTE CRUD DE HÓSPEDE
    echo PHP_EOL;
    echo "---------- TESTE DE CRUD ----------" . PHP_EOL;

    $numeroAleatorio = random_int(100, 999);
    $finalCpf = random_int(10, 99);

    $cpfTeste = "999.999.$numeroAleatorio-$finalCpf";
    $emailTeste = "teste.$numeroAleatorio.$finalCpf@email.com";

    // CREATE
    $hospede = new Hospede(
        'Hóspede de Teste',
        $cpfTeste,
        '(54) 99999-9999',
        $emailTeste
    );

    $hospede->cadastrar($pdo);

    $idTeste = $hospede->getIdHospede();

    echo "[CREATE] Hóspede cadastrado. ID: $idTeste" . PHP_EOL;

    // READ
    $hospedeEncontrado = Hospede::buscarPorId($pdo, $idTeste);

    if ($hospedeEncontrado !== null) {
        echo "[READ] Hóspede encontrado: "
            . $hospedeEncontrado->getNome()
            . PHP_EOL;
    }

    // UPDATE
    $hospedeEncontrado->setNome('Hóspede de Teste Atualizado');
    $hospedeEncontrado->atualizar($pdo);

    echo "[UPDATE] Hóspede atualizado com sucesso." . PHP_EOL;

    // DELETE
    $hospedeEncontrado->excluir($pdo);

    echo "[DELETE] Hóspede excluído com sucesso." . PHP_EOL;

    // CONFIRMA EXCLUSÃO
    $verificacao = Hospede::buscarPorId($pdo, $idTeste);

    if ($verificacao === null) {
        echo "[OK] Exclusão confirmada." . PHP_EOL;
    }

    echo PHP_EOL;
    echo "======================================" . PHP_EOL;
    echo " Demonstração concluída com sucesso! " . PHP_EOL;
    echo "======================================" . PHP_EOL;

} catch (Exception $erro) {

    echo PHP_EOL;
    echo "[ERRO] " . $erro->getMessage() . PHP_EOL;
}