<?php

class Hospede
{
    private ?int $idHospede;
    private string $nome;
    private string $cpf;
    private string $telefone;
    private string $email;

    public function __construct(
        string $nome,
        string $cpf,
        string $telefone,
        string $email,
        ?int $idHospede = null
    ) {
        $this->idHospede = $idHospede;
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->telefone = $telefone;
        $this->email = $email;
    }

    public function cadastrar(PDO $pdo): bool
    {
        $sql = "
            INSERT INTO hospedes (
                nome,
                cpf,
                telefone,
                email
            ) VALUES (
                :nome,
                :cpf,
                :telefone,
                :email
            )
            RETURNING id_hospede
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'nome' => $this->nome,
            'cpf' => $this->cpf,
            'telefone' => $this->telefone,
            'email' => $this->email
        ]);

        $this->idHospede = (int) $stmt->fetchColumn();

        return true;
    }

    public static function listar(PDO $pdo): array
    {
        $sql = "
            SELECT
                id_hospede,
                nome,
                cpf,
                telefone,
                email
            FROM hospedes
            ORDER BY nome
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId(PDO $pdo, int $id): ?Hospede
    {
        $sql = "
            SELECT
                id_hospede,
                nome,
                cpf,
                telefone,
                email
            FROM hospedes
            WHERE id_hospede = :id
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return null;
        }

        return new Hospede(
            $dados['nome'],
            $dados['cpf'],
            $dados['telefone'],
            $dados['email'],
            (int) $dados['id_hospede']
        );
    }

    public function atualizar(PDO $pdo): bool
    {
        if ($this->idHospede === null) {
            throw new Exception('O hóspede não possui um ID.');
        }

        $sql = "
            UPDATE hospedes
            SET
                nome = :nome,
                cpf = :cpf,
                telefone = :telefone,
                email = :email
            WHERE id_hospede = :id
        ";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'nome' => $this->nome,
            'cpf' => $this->cpf,
            'telefone' => $this->telefone,
            'email' => $this->email,
            'id' => $this->idHospede
        ]);
    }

    public function excluir(PDO $pdo): bool
    {
        if ($this->idHospede === null) {
            throw new Exception('O hóspede não possui um ID.');
        }

        $sql = "
            DELETE FROM hospedes
            WHERE id_hospede = :id
        ";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id' => $this->idHospede
        ]);
    }

    public function getIdHospede(): ?int
    {
        return $this->idHospede;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getCpf(): string
    {
        return $this->cpf;
    }

    public function getTelefone(): string
    {
        return $this->telefone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function setCpf(string $cpf): void
    {
        $this->cpf = $cpf;
    }

    public function setTelefone(string $telefone): void
    {
        $this->telefone = $telefone;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}