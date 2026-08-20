<?php

class TipoQuarto
{
    private ?int $idTipoQuarto;
    private string $nome;
    private string $descricao;

    public function __construct(
        string $nome,
        string $descricao,
        ?int $idTipoQuarto = null
    ) {
        $this->idTipoQuarto = $idTipoQuarto;
        $this->nome = $nome;
        $this->descricao = $descricao;
    }

    public function cadastrar(PDO $pdo): bool
    {
        $sql = "
            INSERT INTO tipos_quarto (
                nome,
                descricao
            ) VALUES (
                :nome,
                :descricao
            )
            RETURNING id_tipo_quarto
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'nome' => $this->nome,
            'descricao' => $this->descricao
        ]);

        $this->idTipoQuarto = (int) $stmt->fetchColumn();

        return true;
    }

    public static function listar(PDO $pdo): array
    {
        $sql = "
            SELECT
                id_tipo_quarto,
                nome,
                descricao
            FROM tipos_quarto
            ORDER BY nome
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId(
        PDO $pdo,
        int $id
    ): ?TipoQuarto {
        $sql = "
            SELECT
                id_tipo_quarto,
                nome,
                descricao
            FROM tipos_quarto
            WHERE id_tipo_quarto = :id
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return null;
        }

        return new TipoQuarto(
            $dados['nome'],
            $dados['descricao'],
            (int) $dados['id_tipo_quarto']
        );
    }

    public function atualizar(PDO $pdo): bool
    {
        if ($this->idTipoQuarto === null) {
            throw new Exception(
                'O tipo de quarto não possui um ID.'
            );
        }

        $sql = "
            UPDATE tipos_quarto
            SET
                nome = :nome,
                descricao = :descricao
            WHERE id_tipo_quarto = :id
        ";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'id' => $this->idTipoQuarto
        ]);
    }

    public function excluir(PDO $pdo): bool
    {
        if ($this->idTipoQuarto === null) {
            throw new Exception(
                'O tipo de quarto não possui um ID.'
            );
        }

        $sql = "
            DELETE FROM tipos_quarto
            WHERE id_tipo_quarto = :id
        ";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id' => $this->idTipoQuarto
        ]);
    }

    public function getIdTipoQuarto(): ?int
    {
        return $this->idTipoQuarto;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }
}