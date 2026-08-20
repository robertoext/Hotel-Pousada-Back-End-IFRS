-- 1. RESERVAS POR PERÍODO

WITH parametros AS (
    SELECT
        CURRENT_DATE - 365 AS data_inicio,
        CURRENT_DATE + 365 AS data_fim
)
SELECT
    h.nome AS hospede,
    q.numero AS quarto,
    tq.nome AS tipo_quarto,
    r.data_entrada,
    r.data_saida,
    r.status
FROM reservas r
JOIN hospedes h
    ON h.id_hospede = r.id_hospede
JOIN quartos q
    ON q.id_quarto = r.id_quarto
JOIN tipos_quarto tq
    ON tq.id_tipo_quarto = q.id_tipo_quarto
CROSS JOIN parametros p
WHERE r.data_entrada <= p.data_fim
  AND r.data_saida >= p.data_inicio
ORDER BY r.data_entrada, h.nome;

-- 2. QUARTOS OCUPADOS EM DETERMINADA DATA

WITH parametro AS (
    SELECT CURRENT_DATE AS data_consulta
)
SELECT
    q.numero AS quarto,
    tq.nome AS tipo_quarto,
    h.nome AS hospede,
    r.data_entrada,
    r.data_saida
FROM reservas r
JOIN hospedes h
    ON h.id_hospede = r.id_hospede
JOIN quartos q
    ON q.id_quarto = r.id_quarto
JOIN tipos_quarto tq
    ON tq.id_tipo_quarto = q.id_tipo_quarto
CROSS JOIN parametro p
WHERE r.status <> 'Cancelada'
  AND p.data_consulta >= r.data_entrada
  AND p.data_consulta < r.data_saida
ORDER BY q.numero;

-- 3. FATURAMENTO POR TIPO DE QUARTO

SELECT
    tq.nome AS tipo_quarto,
    COUNT(r.id_reserva) AS quantidade_reservas,
    SUM(r.data_saida - r.data_entrada) AS quantidade_diarias,
    SUM(
        (r.data_saida - r.data_entrada) * q.valor_diaria
    ) AS faturamento
FROM reservas r
JOIN quartos q
    ON q.id_quarto = r.id_quarto
JOIN tipos_quarto tq
    ON tq.id_tipo_quarto = q.id_tipo_quarto
WHERE r.status <> 'Cancelada'
GROUP BY tq.id_tipo_quarto, tq.nome
ORDER BY faturamento DESC;



-- 4. SERVIÇOS MAIS UTILIZADOS

SELECT
    s.nome AS servico,
    COUNT(rs.id_reserva_servico) AS quantidade_utilizacoes,
    SUM(rs.quantidade) AS quantidade_total,
    SUM(rs.quantidade * s.preco) AS valor_gerado
FROM reserva_servicos rs
JOIN servicos s
    ON s.id_servico = rs.id_servico
GROUP BY s.id_servico, s.nome
HAVING SUM(rs.quantidade) > 0
ORDER BY quantidade_total DESC, valor_gerado DESC;

-- 5. HÓSPEDES QUE MAIS GASTARAM

WITH hospedagem AS (
    SELECT
        r.id_hospede,
        COUNT(r.id_reserva) AS quantidade_reservas,
        SUM(
            (r.data_saida - r.data_entrada) * q.valor_diaria
        ) AS total_hospedagem
    FROM reservas r
    JOIN quartos q
        ON q.id_quarto = r.id_quarto
    WHERE r.status <> 'Cancelada'
    GROUP BY r.id_hospede
),
gastos_servicos AS (
    SELECT
        r.id_hospede,
        SUM(rs.quantidade * s.preco) AS total_servicos
    FROM reservas r
    JOIN reserva_servicos rs
        ON rs.id_reserva = r.id_reserva
    JOIN servicos s
        ON s.id_servico = rs.id_servico
    WHERE r.status <> 'Cancelada'
    GROUP BY r.id_hospede
)
SELECT
    h.nome AS hospede,
    COALESCE(hp.quantidade_reservas, 0) AS quantidade_reservas,
    COALESCE(hp.total_hospedagem, 0) AS hospedagem,
    COALESCE(gs.total_servicos, 0) AS servicos,
    COALESCE(hp.total_hospedagem, 0)
        + COALESCE(gs.total_servicos, 0) AS total
FROM hospedes h
LEFT JOIN hospedagem hp
    ON hp.id_hospede = h.id_hospede
LEFT JOIN gastos_servicos gs
    ON gs.id_hospede = h.id_hospede
ORDER BY total DESC
LIMIT 10;



-- 6. QUARTOS SEM RESERVAS

SELECT
    q.numero AS quarto,
    tq.nome AS tipo_quarto,
    q.capacidade,
    q.valor_diaria,
    q.status
FROM quartos q
JOIN tipos_quarto tq
    ON tq.id_tipo_quarto = q.id_tipo_quarto
LEFT JOIN reservas r
    ON r.id_quarto = q.id_quarto
WHERE r.id_reserva IS NULL
ORDER BY q.numero;

-- 7. RESERVAS POR STATUS

SELECT
    status,
    COUNT(*) AS quantidade_reservas
FROM reservas
GROUP BY status
ORDER BY quantidade_reservas DESC, status;
