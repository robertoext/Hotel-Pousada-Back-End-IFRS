<?php

class Pagamento
{
    private ?int $idPagamento;
    private float $valor;
    private string $dataPagamento;
    private string $formaPagamento;
    private string $situacao;
    private int $idReserva;

    public function __construct(
        float $valor,
        string $dataPagamento,
        string $formaPagamento,
        string $situacao,
        int $idReserva,
        ?int $idPagamento = null
    ) {
        $this->idPagamento = $idPagamento;
        $this->valor = $valor;
        $this->dataPagamento = $dataPagamento;
        $this->formaPagamento = $formaPagamento;
        $this->situacao = $situacao;
        $this->idReserva = $idReserva;
    }

    public function cadastrar(PDO $pdo): bool
    {
        $sql = "
            INSERT INTO pagamentos (
                valor,
                data_pagamento,
                forma_pagamento,
                situacao,
                id_reserva
            ) VALUES (
                :valor,
                :data_pagamento,
                :forma_pagamento,
                :situacao,
                :id_reserva
            )
            RETURNING id_pagamento
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'valor' => $this->valor,
            'data_pagamento' => $this->dataPagamento,
            'forma_pagamento' => $this->formaPagamento,
            'situacao' => $this->situacao,
            'id_reserva' => $this->idReserva
        ]);

        $this->idPagamento = (int) $stmt->fetchColumn();

        return true;
    }

    public static function listar(PDO $pdo): array
    {
        $sql = "
            SELECT
                p.id_pagamento,
                p.valor,
                p.data_pagamento,
                p.forma_pagamento,
                p.situacao,
                p.id_reserva,
                h.nome AS hospede,
                q.numero AS quarto
            FROM pagamentos p
            JOIN reservas r
                ON r.id_reserva = p.id_reserva
            JOIN hospedes h
                ON h.id_hospede = r.id_hospede
            JOIN quartos q
                ON q.id_quarto = r.id_quarto
            ORDER BY p.data_pagamento DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId(
        PDO $pdo,
        int $id
    ): ?Pagamento {
        $sql = "
            SELECT
                id_pagamento,
                valor,
                data_pagamento,
                forma_pagamento,
                situacao,
                id_reserva
            FROM pagamentos
            WHERE id_pagamento = :id
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return null;
        }

        return new Pagamento(
            (float) $dados['valor'],
            $dados['data_pagamento'],
            $dados['forma_pagamento'],
            $dados['situacao'],
            (int) $dados['id_reserva'],
            (int) $dados['id_pagamento']
        );
    }

    public function atualizar(PDO $pdo): bool
    {
        if ($this->idPagamento === null) {
            throw new Exception(
                'O pagamento não possui um ID.'
            );
        }

        $sql = "
            UPDATE pagamentos
            SET
                valor = :valor,
                data_pagamento = :data_pagamento,
                forma_pagamento = :forma_pagamento,
                situacao = :situacao,
                id_reserva = :id_reserva
            WHERE id_pagamento = :id
        ";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'valor' => $this->valor,
            'data_pagamento' => $this->dataPagamento,
            'forma_pagamento' => $this->formaPagamento,
            'situacao' => $this->situacao,
            'id_reserva' => $this->idReserva,
            'id' => $this->idPagamento
        ]);
    }

    public function excluir(PDO $pdo): bool
    {
        if ($this->idPagamento === null) {
            throw new Exception(
                'O pagamento não possui um ID.'
            );
        }

        $sql = "
            DELETE FROM pagamentos
            WHERE id_pagamento = :id
        ";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id' => $this->idPagamento
        ]);
    }

    public function getIdPagamento(): ?int
    {
        return $this->idPagamento;
    }

    public function getValor(): float
    {
        return $this->valor;
    }

    public function getDataPagamento(): string
    {
        return $this->dataPagamento;
    }

    public function getFormaPagamento(): string
    {
        return $this->formaPagamento;
    }

    public function getSituacao(): string
    {
        return $this->situacao;
    }

    public function getIdReserva(): int
    {
        return $this->idReserva;
    }

    public function setValor(float $valor): void
    {
        $this->valor = $valor;
    }

    public function setDataPagamento(
        string $dataPagamento
    ): void {
        $this->dataPagamento = $dataPagamento;
    }

    public function setFormaPagamento(
        string $formaPagamento
    ): void {
        $this->formaPagamento = $formaPagamento;
    }

    public function setSituacao(string $situacao): void
    {
        $this->situacao = $situacao;
    }

    public function setIdReserva(int $idReserva): void
    {
        $this->idReserva = $idReserva;
    }
}