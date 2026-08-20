<?php

class Database
{
    public function conectar(): PDO
    {
        $configuracao = require __DIR__ . '/data_base.php';

        $dsn = "pgsql:host={$configuracao['host']};"
             . "port={$configuracao['porta']};"
             . "dbname={$configuracao['banco']}";

        try {
            $pdo = new PDO(
                $dsn,
                $configuracao['usuario'],
                $configuracao['senha']
            );

            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $pdo->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

            return $pdo;
        } catch (PDOException $erro) {
            throw new Exception(
                'Erro ao conectar ao banco de dados: ' . $erro->getMessage()
            );
        }
    }
}