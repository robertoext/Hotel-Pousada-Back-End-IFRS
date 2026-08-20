<?php

require_once __DIR__ . '/config/Database.php';

try {
    $database = new Database();
    $pdo = $database->conectar();

    echo 'Conexão com o PostgreSQL realizada com sucesso!';
} catch (Exception $erro) {
    echo $erro->getMessage();
}