<?php

class ReservaServico
{
    private ?int $idReservaServico;
    private int $quantidade;
    private int $idReserva;
    private int $idServico;

    public function __construct(
        int $quantidade,
        int $idReserva,
        int $idServico,
        ?int $idReservaServico = null
    ) {
        $this->idReservaServico = $idReservaServico;
        $this->quantidade = $quantidade;
        $this->idReserva = $idReserva;
        $this->idServico = $idServico;
    }

    public function cadastrar(PDO $pdo): bool
    {
        $sql = "
            INSERT INTO reserva_servicos (
                quantidade,
                id_reserva,
                id_servico
            ) VALUES (
                :quantidade,
                :id_reserva,
                :id_servico
            )
            RETURNING id_reserva_servico
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'quantidade' => $this->quantidade,
            'id_reserva' => $this->idReserva,
            'id_servico' => $this->idServico
        ]);

        $this->idReservaServico = (int) $stmt->fetchColumn();

        return true;
    }

    public static function listar(PDO $pdo): array
    {
        $sql = "
            SELECT
                rs.id_reserva_servico,
                rs.quantidade,
                rs.id_reserva,
                rs.id_servico,
                s.nome AS servico,
                s.preco,
                h.nome AS hospede,
                q.numero AS quarto,
                rs.quantidade * s.preco AS valor_total
            FROM reserva_servicos rs
            JOIN servicos s
                ON s.id_servico = rs.id_servico
            JOIN reservas r
                ON r.id_reserva = rs.id_reserva
            JOIN hospedes h
                ON h.id_hospede = r.id_hospede
            JOIN quartos q
                ON q.id_quarto = r.id_quarto
            ORDER BY rs.id_reserva_servico
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId(
        PDO $pdo,
        int $id
    ): ?ReservaServico {
        $sql = "
            SELECT
                id_reserva_servico,
                quantidade,
                id_reserva,
                id_servico
            FROM reserva_servicos
            WHERE id_reserva_servico = :id
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return null;
        }

        return new ReservaServico(
            (int) $dados['quantidade'],
            (int) $dados['id_reserva'],
            (int) $dados['id_servico'],
            (int) $dados['id_reserva_servico']
        );
    }

    public function atualizar(PDO $pdo): bool
    {
        if ($this->idReservaServico === null) {
            throw new Exception(
                'O serviço da reserva não possui um ID.'
            );
        }

        $sql = "
            UPDATE reserva_servicos
            SET
                quantidade = :quantidade,
                id_reserva = :id_reserva,
                id_servico = :id_servico
            WHERE id_reserva_servico = :id
        ";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'quantidade' => $this->quantidade,
            'id_reserva' => $this->idReserva,
            'id_servico' => $this->idServico,
            'id' => $this->idReservaServico
        ]);
    }

    public function excluir(PDO $pdo): bool
    {
        if ($this->idReservaServico === null) {
            throw new Exception(
                'O serviço da reserva não possui um ID.'
            );
        }

        $sql = "
            DELETE FROM reserva_servicos
            WHERE id_reserva_servico = :id
        ";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id' => $this->idReservaServico
        ]);
    }

    public function getIdReservaServico(): ?int
    {
        return $this->idReservaServico;
    }

    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    public function getIdReserva(): int
    {
        return $this->idReserva;
    }

    public function getIdServico(): int
    {
        return $this->idServico;
    }

    public function setQuantidade(int $quantidade): void
    {
        $this->quantidade = $quantidade;
    }

    public function setIdReserva(int $idReserva): void
    {
        $this->idReserva = $idReserva;
    }

    public function setIdServico(int $idServico): void
    {
        $this->idServico = $idServico;
    }
}