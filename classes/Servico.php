<?php

class Servico
{
    private ?int $idServico;
    private string $nome;
    private string $descricao;
    private float $preco;
    private bool $ativo;

    public function __construct(
        string $nome,
        string $descricao,
        float $preco,
        bool $ativo = true,
        ?int $idServico = null
    ) {
        $this->idServico = $idServico;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->preco = $preco;
        $this->ativo = $ativo;
    }

    public function cadastrar(PDO $pdo): bool
    {
        $sql = "
            INSERT INTO servicos (
                nome,
                descricao,
                preco,
                ativo
            ) VALUES (
                :nome,
                :descricao,
                :preco,
                :ativo
            )
            RETURNING id_servico
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':nome', $this->nome);
        $stmt->bindValue(':descricao', $this->descricao);
        $stmt->bindValue(':preco', $this->preco);
        $stmt->bindValue(':ativo', $this->ativo, PDO::PARAM_BOOL);

        $stmt->execute();

        $this->idServico = (int) $stmt->fetchColumn();

        return true;
    }

    public static function listar(PDO $pdo): array
    {
        $sql = "
            SELECT
                id_servico,
                nome,
                descricao,
                preco,
                ativo
            FROM servicos
            ORDER BY nome
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId(
        PDO $pdo,
        int $id
    ): ?Servico {
        $sql = "
            SELECT
                id_servico,
                nome,
                descricao,
                preco,
                ativo
            FROM servicos
            WHERE id_servico = :id
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return null;
        }

        return new Servico(
            $dados['nome'],
            $dados['descricao'] ?? '',
            (float) $dados['preco'],
            (bool) $dados['ativo'],
            (int) $dados['id_servico']
        );
    }

    public function atualizar(PDO $pdo): bool
    {
        if ($this->idServico === null) {
            throw new Exception(
                'O serviço não possui um ID.'
            );
        }

        $sql = "
            UPDATE servicos
            SET
                nome = :nome,
                descricao = :descricao,
                preco = :preco,
                ativo = :ativo
            WHERE id_servico = :id
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':nome', $this->nome);
        $stmt->bindValue(':descricao', $this->descricao);
        $stmt->bindValue(':preco', $this->preco);
        $stmt->bindValue(
            ':ativo',
            $this->ativo,
            PDO::PARAM_BOOL
        );
        $stmt->bindValue(
            ':id',
            $this->idServico,
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }

    public function excluir(PDO $pdo): bool
    {
        if ($this->idServico === null) {
            throw new Exception(
                'O serviço não possui um ID.'
            );
        }

        $sql = "
            DELETE FROM servicos
            WHERE id_servico = :id
        ";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id' => $this->idServico
        ]);
    }

    public function getIdServico(): ?int
    {
        return $this->idServico;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function getPreco(): float
    {
        return $this->preco;
    }

    public function getAtivo(): bool
    {
        return $this->ativo;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function setDescricao(
        string $descricao
    ): void {
        $this->descricao = $descricao;
    }

    public function setPreco(float $preco): void
    {
        $this->preco = $preco;
    }

    public function setAtivo(bool $ativo): void
    {
        $this->ativo = $ativo;
    }
}