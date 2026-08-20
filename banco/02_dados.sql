TRUNCATE TABLE
    pagamentos,
    reserva_servicos,
    reservas,
    servicos,
    quartos,
    tipos_quarto,
    hospedes
RESTART IDENTITY CASCADE;


-- 1. TIPOS DE QUARTO

INSERT INTO tipos_quarto (nome, descricao) VALUES
    ('Individual', 'Quarto para uma pessoa, com uma cama de solteiro.'),
    ('Duplo', 'Quarto para duas pessoas, com cama de casal ou duas camas.'),
    ('Triplo', 'Quarto para ate tres pessoas.'),
    ('Suite', 'Quarto de categoria superior, com mais conforto e comodidades.');


-- 2. HOSPEDES

INSERT INTO hospedes (nome, cpf, telefone, email) VALUES
    ('Ana Souza',       '000.000.001-01', '(54) 99901-0001', 'ana.souza@email.com'),
    ('Bruno Martins',   '000.000.002-02', '(54) 99902-0002', 'bruno.martins@email.com'),
    ('Carla Oliveira',  '000.000.003-03', '(54) 99903-0003', 'carla.oliveira@email.com'),
    ('Daniel Ferreira', '000.000.004-04', '(54) 99904-0004', 'daniel.ferreira@email.com'),
    ('Eduarda Lima',    '000.000.005-05', '(54) 99905-0005', 'eduarda.lima@email.com'),
    ('Fabio Ribeiro',   '000.000.006-06', '(54) 99906-0006', 'fabio.ribeiro@email.com'),
    ('Gabriela Costa',  '000.000.007-07', '(54) 99907-0007', 'gabriela.costa@email.com'),
    ('Henrique Alves',  '000.000.008-08', '(54) 99908-0008', 'henrique.alves@email.com'),
    ('Isabela Rocha',   '000.000.009-09', '(54) 99909-0009', 'isabela.rocha@email.com'),
    ('Joao Pereira',    '000.000.010-10', '(54) 99910-0010', 'joao.pereira@email.com');


-- 3. QUARTOS

INSERT INTO quartos
    (numero, capacidade, valor_diaria, status, id_tipo_quarto)
VALUES
    ('101', 1, 180.00, 'Disponivel', 1),
    ('102', 1, 190.00, 'Disponivel', 1),
    ('201', 2, 280.00, 'Ocupado',    2),
    ('202', 2, 300.00, 'Disponivel', 2),
    ('301', 3, 390.00, 'Disponivel', 3),
    ('302', 3, 420.00, 'Disponivel', 3),
    ('401', 2, 550.00, 'Disponivel', 4),
    ('402', 4, 700.00, 'Manutencao', 4);


-- 4. SERVICOS


INSERT INTO servicos (nome, descricao, preco, ativo) VALUES
    ('Cafe da manha',       'Cafe da manha completo por pessoa.',            35.00, TRUE),
    ('Estacionamento',      'Vaga diaria no estacionamento da pousada.',     25.00, TRUE),
    ('Lavanderia',          'Lavagem e secagem de roupas.',                  40.00, TRUE),
    ('Traslado',            'Transporte entre a pousada e o aeroporto.',    120.00, TRUE),
    ('Passeio turistico',   'Passeio guiado pelos principais pontos locais.',180.00, TRUE),
    ('Servico de quarto',   'Entrega de alimentos e itens no quarto.',       50.00, TRUE);

-- 5. RESERVAS

INSERT INTO reservas
    (data_entrada, data_saida, quantidade_hospedes, status,
     observacao, id_hospede, id_quarto)
VALUES
    (CURRENT_DATE - 120, CURRENT_DATE - 117, 1, 'Finalizada',
     'Hospedagem individual concluida.', 1, 1),

    (CURRENT_DATE - 110, CURRENT_DATE - 106, 2, 'Finalizada',
     'Viagem a trabalho.', 2, 3),

    (CURRENT_DATE - 95, CURRENT_DATE - 92, 2, 'Finalizada',
     'Hospedagem de aniversario.', 1, 7),

    (CURRENT_DATE - 80, CURRENT_DATE - 75, 3, 'Finalizada',
     'Viagem em familia.', 3, 5),

    (CURRENT_DATE - 60, CURRENT_DATE - 57, 2, 'Cancelada',
     'Reserva cancelada pelo hospede.', 4, 4),

    (CURRENT_DATE - 50, CURRENT_DATE - 48, 1, 'Finalizada',
     'Hospedagem de curta duracao.', 5, 2),

    (CURRENT_DATE - 40, CURRENT_DATE - 36, 2, 'Finalizada',
     'Participacao em evento.', 6, 6),

    (CURRENT_DATE + 10, CURRENT_DATE + 13, 2, 'Cancelada',
     'Cancelada antes da data de entrada.', 7, 7),

    (CURRENT_DATE - 1, CURRENT_DATE + 2, 2, 'Hospedado',
     'Hospede atualmente na pousada.', 8, 3),

    (CURRENT_DATE + 7, CURRENT_DATE + 10, 1, 'Reservada',
     'Reserva futura individual.', 9, 1),

    (CURRENT_DATE + 12, CURRENT_DATE + 16, 2, 'Reservada',
     'Reserva futura para casal.', 10, 4),

    (CURRENT_DATE + 20, CURRENT_DATE + 24, 3, 'Reservada',
     'Reserva futura para familia.', 2, 5),

    (CURRENT_DATE + 30, CURRENT_DATE + 34, 2, 'Reservada',
     'Reserva futura para evento.', 3, 6),

    (CURRENT_DATE + 40, CURRENT_DATE + 43, 2, 'Reservada',
     'Reserva futura em suite.', 4, 7),

    (CURRENT_DATE + 60, CURRENT_DATE + 63, 1, 'Reservada',
     'Reserva futura de curta duracao.', 5, 2);


-- 6. UTILIZACAO DE SERVICOS

INSERT INTO reserva_servicos (quantidade, id_reserva, id_servico) VALUES
    (3,  1, 1),
    (3,  1, 2),

    (8,  2, 1),
    (4,  2, 2),
    (1,  2, 3),

    (6,  3, 1),
    (1,  3, 4),
    (2,  3, 6),

    (15, 4, 1),
    (5,  4, 2),
    (3,  4, 5),

    (2,  6, 1),
    (1,  6, 3),

    (8,  7, 1),
    (4,  7, 2),
    (2,  7, 6),

    (4,  9, 1),
    (2,  9, 2),
    (1,  9, 6),

    (3, 10, 1),
    (4, 11, 1),
    (1, 11, 4),
    (12, 12, 1),
    (4, 12, 2);

-- 7. PAGAMENTOS
    
INSERT INTO pagamentos
    (valor, data_pagamento, forma_pagamento, situacao, id_reserva)
VALUES
    (645.00, CURRENT_DATE - 117, 'PIX',      'Pago',      1),
    (800.00, CURRENT_DATE - 108, 'Credito',  'Pago',      2),
    (620.00, CURRENT_DATE - 106, 'Debito',   'Pago',      2),

    (1000.00, CURRENT_DATE - 94, 'Credito',  'Pago',      3),
    (975.00,  CURRENT_DATE - 92, 'PIX',      'Pago',      3),

    (2500.00, CURRENT_DATE - 75, 'Credito',  'Pago',      4),

    (300.00, CURRENT_DATE - 59, 'Credito',   'Estornado', 5),

    (420.00, CURRENT_DATE - 48, 'Dinheiro',  'Pago',      6),

    (1200.00, CURRENT_DATE - 36, 'Debito',   'Pago',      7),

    (550.00, CURRENT_DATE + 1, 'PIX',        'Pendente',  9),

    (180.00, CURRENT_DATE,     'PIX',        'Pago',     10),
    (600.00, CURRENT_DATE,     'Credito',    'Pendente', 11),
    (780.00, CURRENT_DATE,     'Debito',     'Pendente', 12),
    (840.00, CURRENT_DATE,     'Credito',    'Pendente', 13),
    (550.00, CURRENT_DATE,     'PIX',        'Pendente', 14),
    (190.00, CURRENT_DATE,     'Dinheiro',   'Pendente', 15);

-- 8. CONFERENCIA DA MASSA DE DADOS
    
SELECT 'hospedes' AS tabela, COUNT(*) AS quantidade FROM hospedes
UNION ALL
SELECT 'tipos_quarto', COUNT(*) FROM tipos_quarto
UNION ALL
SELECT 'quartos', COUNT(*) FROM quartos
UNION ALL
SELECT 'servicos', COUNT(*) FROM servicos
UNION ALL
SELECT 'reservas', COUNT(*) FROM reservas
UNION ALL
SELECT 'reserva_servicos', COUNT(*) FROM reserva_servicos
UNION ALL
SELECT 'pagamentos', COUNT(*) FROM pagamentos;
