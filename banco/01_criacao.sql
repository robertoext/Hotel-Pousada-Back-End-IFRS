-- Remove as tabelas caso o script seja executado novamente.
DROP TABLE IF EXISTS pagamentos CASCADE;
DROP TABLE IF EXISTS reserva_servicos CASCADE;
DROP TABLE IF EXISTS reservas CASCADE;
DROP TABLE IF EXISTS servicos CASCADE;
DROP TABLE IF EXISTS quartos CASCADE;
DROP TABLE IF EXISTS tipos_quarto CASCADE;
DROP TABLE IF EXISTS hospedes CASCADE;


-- TABELA: hospedes

CREATE TABLE hospedes (
    id_hospede INTEGER GENERATED ALWAYS AS IDENTITY,
    nome VARCHAR(150) NOT NULL,
    cpf VARCHAR(14) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL,

    CONSTRAINT pk_hospedes PRIMARY KEY (id_hospede),
    CONSTRAINT uq_hospedes_cpf UNIQUE (cpf),
    CONSTRAINT uq_hospedes_email UNIQUE (email),
    CONSTRAINT ck_hospedes_nome CHECK (LENGTH(TRIM(nome)) >= 3),
    CONSTRAINT ck_hospedes_cpf CHECK (LENGTH(TRIM(cpf)) BETWEEN 11 AND 14),
    CONSTRAINT ck_hospedes_email CHECK (POSITION('@' IN email) > 1)
);


-- TABELA: tipos_quarto

CREATE TABLE tipos_quarto (
    id_tipo_quarto INTEGER GENERATED ALWAYS AS IDENTITY,
    nome VARCHAR(60) NOT NULL,
    descricao TEXT,

    CONSTRAINT pk_tipos_quarto PRIMARY KEY (id_tipo_quarto),
    CONSTRAINT uq_tipos_quarto_nome UNIQUE (nome),
    CONSTRAINT ck_tipos_quarto_nome CHECK (LENGTH(TRIM(nome)) >= 2)
);


-- TABELA: quartos

CREATE TABLE quartos (
    id_quarto INTEGER GENERATED ALWAYS AS IDENTITY,
    numero VARCHAR(10) NOT NULL,
    capacidade INTEGER NOT NULL,
    valor_diaria NUMERIC(10, 2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Disponivel',
    id_tipo_quarto INTEGER NOT NULL,

    CONSTRAINT pk_quartos PRIMARY KEY (id_quarto),
    CONSTRAINT uq_quartos_numero UNIQUE (numero),
    CONSTRAINT fk_quartos_tipo_quarto
        FOREIGN KEY (id_tipo_quarto)
        REFERENCES tipos_quarto (id_tipo_quarto)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT ck_quartos_capacidade CHECK (capacidade > 0),
    CONSTRAINT ck_quartos_valor_diaria CHECK (valor_diaria > 0),
    CONSTRAINT ck_quartos_status
        CHECK (status IN ('Disponivel', 'Ocupado', 'Manutencao'))
);


-- TABELA: servicos

CREATE TABLE servicos (
    id_servico INTEGER GENERATED ALWAYS AS IDENTITY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco NUMERIC(10, 2) NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,

    CONSTRAINT pk_servicos PRIMARY KEY (id_servico),
    CONSTRAINT uq_servicos_nome UNIQUE (nome),
    CONSTRAINT ck_servicos_nome CHECK (LENGTH(TRIM(nome)) >= 2),
    CONSTRAINT ck_servicos_preco CHECK (preco >= 0)
);


-- TABELA: reservas

CREATE TABLE reservas (
    id_reserva INTEGER GENERATED ALWAYS AS IDENTITY,
    data_entrada DATE NOT NULL,
    data_saida DATE NOT NULL,
    quantidade_hospedes INTEGER NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Reservada',
    observacao TEXT,
    id_hospede INTEGER NOT NULL,
    id_quarto INTEGER NOT NULL,

    CONSTRAINT pk_reservas PRIMARY KEY (id_reserva),
    CONSTRAINT fk_reservas_hospede
        FOREIGN KEY (id_hospede)
        REFERENCES hospedes (id_hospede)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_reservas_quarto
        FOREIGN KEY (id_quarto)
        REFERENCES quartos (id_quarto)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT ck_reservas_datas CHECK (data_saida > data_entrada),
    CONSTRAINT ck_reservas_quantidade_hospedes CHECK (quantidade_hospedes > 0),
    CONSTRAINT ck_reservas_status
        CHECK (status IN ('Reservada', 'Hospedado', 'Finalizada', 'Cancelada'))
);


-- TABELA ASSOCIATIVA: reserva_servicos

CREATE TABLE reserva_servicos (
    id_reserva_servico INTEGER GENERATED ALWAYS AS IDENTITY,
    quantidade INTEGER NOT NULL DEFAULT 1,
    id_reserva INTEGER NOT NULL,
    id_servico INTEGER NOT NULL,

    CONSTRAINT pk_reserva_servicos PRIMARY KEY (id_reserva_servico),
    CONSTRAINT fk_reserva_servicos_reserva
        FOREIGN KEY (id_reserva)
        REFERENCES reservas (id_reserva)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_reserva_servicos_servico
        FOREIGN KEY (id_servico)
        REFERENCES servicos (id_servico)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT uq_reserva_servicos UNIQUE (id_reserva, id_servico),
    CONSTRAINT ck_reserva_servicos_quantidade CHECK (quantidade > 0)
);


-- TABELA: pagamentos

CREATE TABLE pagamentos (
    id_pagamento INTEGER GENERATED ALWAYS AS IDENTITY,
    valor NUMERIC(10, 2) NOT NULL,
    data_pagamento DATE NOT NULL DEFAULT CURRENT_DATE,
    forma_pagamento VARCHAR(30) NOT NULL,
    situacao VARCHAR(20) NOT NULL DEFAULT 'Pendente',
    id_reserva INTEGER NOT NULL,

    CONSTRAINT pk_pagamentos PRIMARY KEY (id_pagamento),
    CONSTRAINT fk_pagamentos_reserva
        FOREIGN KEY (id_reserva)
        REFERENCES reservas (id_reserva)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT ck_pagamentos_valor CHECK (valor > 0),
    CONSTRAINT ck_pagamentos_forma
        CHECK (forma_pagamento IN ('Dinheiro', 'Credito', 'Debito', 'PIX')),
    CONSTRAINT ck_pagamentos_situacao
        CHECK (situacao IN ('Pendente', 'Pago', 'Cancelado', 'Estornado'))
);


-- ÍNDICES

CREATE INDEX idx_reservas_hospede ON reservas (id_hospede);
CREATE INDEX idx_reservas_quarto ON reservas (id_quarto);
CREATE INDEX idx_reservas_periodo ON reservas (data_entrada, data_saida);
CREATE INDEX idx_reservas_status ON reservas (status);
CREATE INDEX idx_reserva_servicos_reserva ON reserva_servicos (id_reserva);
CREATE INDEX idx_reserva_servicos_servico ON reserva_servicos (id_servico);
CREATE INDEX idx_pagamentos_reserva ON pagamentos (id_reserva);


-- REGRA: impedir reservas conflitantes para o mesmo quarto

CREATE OR REPLACE FUNCTION verificar_conflito_reserva()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.status <> 'Cancelada' AND EXISTS (
        SELECT 1
        FROM reservas r
        WHERE r.id_quarto = NEW.id_quarto
          AND r.id_reserva <> COALESCE(NEW.id_reserva, 0)
          AND r.status <> 'Cancelada'
          AND NEW.data_entrada < r.data_saida
          AND NEW.data_saida > r.data_entrada
    ) THEN
        RAISE EXCEPTION
            'O quarto ja possui uma reserva no periodo informado.';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_verificar_conflito_reserva
BEFORE INSERT OR UPDATE ON reservas
FOR EACH ROW
EXECUTE FUNCTION verificar_conflito_reserva();

-- REGRA: quantidade de hóspedes não pode superar a capacidade

CREATE OR REPLACE FUNCTION verificar_capacidade_quarto()
RETURNS TRIGGER AS $$
DECLARE
    capacidade_quarto INTEGER;
BEGIN
    SELECT capacidade
      INTO capacidade_quarto
      FROM quartos
     WHERE id_quarto = NEW.id_quarto;

    IF capacidade_quarto IS NULL THEN
        RAISE EXCEPTION 'Quarto nao encontrado.';
    END IF;

    IF NEW.quantidade_hospedes > capacidade_quarto THEN
        RAISE EXCEPTION
            'A quantidade de hospedes excede a capacidade do quarto.';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_verificar_capacidade_quarto
BEFORE INSERT OR UPDATE ON reservas
FOR EACH ROW
EXECUTE FUNCTION verificar_capacidade_quarto();
