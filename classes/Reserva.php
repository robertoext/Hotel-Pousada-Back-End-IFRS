<?php

class Reserva
{
    private ?int $idReserva;
    private string $dataEntrada;
    private string $dataSaida;
    private int $quantidadeHospedes;
    private string $status;
    private ?string $observacao;
    private int $idHospede;
    private int $idQuarto;

    public function __construct(
        string $dataEntrada,
        string $dataSaida,
        int $quantidadeHospedes,
        string $status,
        ?string $observacao,
        int $idHospede,
        int $idQuarto,
        ?int $idReserva = null
    ) {
        $this->idReserva = $idReserva;
        $this->dataEntrada = $dataEntrada;
        $this->dataSaida = $dataSaida;
        $this->quantidadeHospedes = $quantidadeHospedes;
        $this->status = $status;
        $this->observacao = $observacao;
        $this->idHospede = $idHospede;
        $this->idQuarto = $idQuarto;
    }

    public function cadastrar(PDO $pdo): bool
    {
        $sql = "
            INSERT INTO reservas (
                data_entrada,
                data_saida,
                quantidade_hospedes,
                status,
                observacao,
                id_hospede,
                id_quarto
            ) VALUES (
                :data_entrada,
                :data_saida,
                :quantidade_hospedes,
                :status,
                :observacao,
                :id_hospede,
                :id_quarto
            )
            RETURNING id_reserva
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'data_entrada' => $this->dataEntrada,
            'data_saida' => $this->dataSaida,
            'quantidade_hospedes' => $this->quantidadeHospedes,
            'status' => $this->status,
            'observacao' => $this->observacao,
            'id_hospede' => $this->idHospede,
            'id_quarto' => $this->idQuarto
        ]);

        $this->idReserva = (int) $stmt->fetchColumn();

        return true;
    }

    public static function listar(PDO $pdo): array
    {
        $sql = "
            SELECT
                r.id_reserva,
                r.data_entrada,
                r.data_saida,
                r.quantidade_hospedes,
                r.status,
                r.observacao,
                r.id_hospede,
                r.id_quarto,
                h.nome AS hospede,
                q.numero AS quarto
            FROM reservas r
            JOIN hospedes h
                ON h.id_hospede = r.id_hospede
            JOIN quartos q
                ON q.id_quarto = r.id_quarto
            ORDER BY r.data_entrada
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId(
        PDO $pdo,
        int $id
    ): ?Reserva {
        $sql = "
            SELECT
                id_reserva,
                data_entrada,
                data_saida,
                quantidade_hospedes,
                status,
                observacao,
                id_hospede,
                id_quarto
            FROM reservas
            WHERE id_reserva = :id
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return null;
        }

        return new Reserva(
            $dados['data_entrada'],
            $dados['data_saida'],
            (int) $dados['quantidade_hospedes'],
            $dados['status'],
            $dados['observacao'],
            (int) $dados['id_hospede'],
            (int) $dados['id_quarto'],
            (int) $dados['id_reserva']
        );
    }

    public function atualizar(PDO $pdo): bool
    {
        if ($this->idReserva === null) {
            throw new Exception(
                'A reserva não possui um ID.'
            );
        }

        $sql = "
            UPDATE reservas
            SET
                data_entrada = :data_entrada,
                data_saida = :data_saida,
                quantidade_hospedes = :quantidade_hospedes,
                status = :status,
                observacao = :observacao,
                id_hospede = :id_hospede,
                id_quarto = :id_quarto
            WHERE id_reserva = :id
        ";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'data_entrada' => $this->dataEntrada,
            'data_saida' => $this->dataSaida,
            'quantidade_hospedes' => $this->quantidadeHospedes,
            'status' => $this->status,
            'observacao' => $this->observacao,
            'id_hospede' => $this->idHospede,
            'id_quarto' => $this->idQuarto,
            'id' => $this->idReserva
        ]);
    }

    public function excluir(PDO $pdo): bool
    {
        if ($this->idReserva === null) {
            throw new Exception(
                'A reserva não possui um ID.'
            );
        }

        $sql = "
            DELETE FROM reservas
            WHERE id_reserva = :id
        ";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id' => $this->idReserva
        ]);
    }

    public function getIdReserva(): ?int
    {
        return $this->idReserva;
    }

    public function getDataEntrada(): string
    {
        return $this->dataEntrada;
    }

    public function getDataSaida(): string
    {
        return $this->dataSaida;
    }

    public function getQuantidadeHospedes(): int
    {
        return $this->quantidadeHospedes;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function getIdHospede(): int
    {
        return $this->idHospede;
    }

    public function getIdQuarto(): int
    {
        return $this->idQuarto;
    }

    public function setDataEntrada(
        string $dataEntrada
    ): void {
        $this->dataEntrada = $dataEntrada;
    }

    public function setDataSaida(
        string $dataSaida
    ): void {
        $this->dataSaida = $dataSaida;
    }

    public function setQuantidadeHospedes(
        int $quantidadeHospedes
    ): void {
        $this->quantidadeHospedes = $quantidadeHospedes;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setObservacao(
        ?string $observacao
    ): void {
        $this->observacao = $observacao;
    }

    public function setIdHospede(
        int $idHospede
    ): void {
        $this->idHospede = $idHospede;
    }

    public function setIdQuarto(
        int $idQuarto
    ): void {
        $this->idQuarto = $idQuarto;
    }
}