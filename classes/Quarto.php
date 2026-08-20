<?php

class Quarto
{
    private ?int $idQuarto;
    private string $numero;
    private int $capacidade;
    private float $valorDiaria;
    private string $status;
    private int $idTipoQuarto;

    public function __construct(
        string $numero,
        int $capacidade,
        float $valorDiaria,
        string $status,
        int $idTipoQuarto,
        ?int $idQuarto = null
    ) {
        $this->idQuarto = $idQuarto;
        $this->numero = $numero;
        $this->capacidade = $capacidade;
        $this->valorDiaria = $valorDiaria;
        $this->status = $status;
        $this->idTipoQuarto = $idTipoQuarto;
    }

    public function cadastrar(PDO $pdo): bool
    {
        $sql = "
            INSERT INTO quartos (
                numero,
                capacidade,
                valor_diaria,
                status,
                id_tipo_quarto
            ) VALUES (
                :numero,
                :capacidade,
                :valor_diaria,
                :status,
                :id_tipo_quarto
            )
            RETURNING id_quarto
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'numero' => $this->numero,
            'capacidade' => $this->capacidade,
            'valor_diaria' => $this->valorDiaria,
            'status' => $this->status,
            'id_tipo_quarto' => $this->idTipoQuarto
        ]);

        $this->idQuarto = (int) $stmt->fetchColumn();

        return true;
    }

    public static function listar(PDO $pdo): array
    {
        $sql = "
            SELECT
                q.id_quarto,
                q.numero,
                q.capacidade,
                q.valor_diaria,
                q.status,
                q.id_tipo_quarto,
                tq.nome AS tipo_quarto
            FROM quartos q
            JOIN tipos_quarto tq
                ON tq.id_tipo_quarto = q.id_tipo_quarto
            ORDER BY q.numero
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId(PDO $pdo, int $id): ?Quarto
    {
        $sql = "
            SELECT
                id_quarto,
                numero,
                capacidade,
                valor_diaria,
                status,
                id_tipo_quarto
            FROM quartos
            WHERE id_quarto = :id
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return null;
        }

        return new Quarto(
            $dados['numero'],
            (int) $dados['capacidade'],
            (float) $dados['valor_diaria'],
            $dados['status'],
            (int) $dados['id_tipo_quarto'],
            (int) $dados['id_quarto']
        );
    }

    public function atualizar(PDO $pdo): bool
    {
        if ($this->idQuarto === null) {
            throw new Exception('O quarto não possui um ID.');
        }

        $sql = "
            UPDATE quartos
            SET
                numero = :numero,
                capacidade = :capacidade,
                valor_diaria = :valor_diaria,
                status = :status,
                id_tipo_quarto = :id_tipo_quarto
            WHERE id_quarto = :id
        ";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'numero' => $this->numero,
            'capacidade' => $this->capacidade,
            'valor_diaria' => $this->valorDiaria,
            'status' => $this->status,
            'id_tipo_quarto' => $this->idTipoQuarto,
            'id' => $this->idQuarto
        ]);
    }

    public function excluir(PDO $pdo): bool
    {
        if ($this->idQuarto === null) {
            throw new Exception('O quarto não possui um ID.');
        }

        $sql = "
            DELETE FROM quartos
            WHERE id_quarto = :id
        ";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id' => $this->idQuarto
        ]);
    }

    public function getIdQuarto(): ?int
    {
        return $this->idQuarto;
    }

    public function getNumero(): string
    {
        return $this->numero;
    }

    public function getCapacidade(): int
    {
        return $this->capacidade;
    }

    public function getValorDiaria(): float
    {
        return $this->valorDiaria;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getIdTipoQuarto(): int
    {
        return $this->idTipoQuarto;
    }

    public function setNumero(string $numero): void
    {
        $this->numero = $numero;
    }

    public function setCapacidade(int $capacidade): void
    {
        $this->capacidade = $capacidade;
    }

    public function setValorDiaria(float $valorDiaria): void
    {
        $this->valorDiaria = $valorDiaria;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setIdTipoQuarto(int $idTipoQuarto): void
    {
        $this->idTipoQuarto = $idTipoQuarto;
    }
}