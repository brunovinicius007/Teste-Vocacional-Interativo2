<?php
// --- CONFIGURAÇÃO DO BANCO DE DADOS ---
$db_file = 'vocational_test.db';
$db = new SQLite3($db_file);

// Ativa o suporte a chaves estrangeiras (essencial para a integridade dos dados)
$db->exec('PRAGMA foreign_keys = ON;');

// --- DEFINIÇÃO DA ESTRUTURA DAS TABELAS ---
$commands = [
    // Mantém a tabela de administradores
    'CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL
    )',

    // 1. Tabela de Usuários
    'CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY,
        nome TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
    )',

    // 2. Tabela de Categorias Vocacionais
    'CREATE TABLE IF NOT EXISTS categorias (
        id INTEGER PRIMARY KEY,
        nome_chave TEXT NOT NULL UNIQUE,
        nome_exibicao TEXT NOT NULL
    )',

    // 3. Tabela de Perguntas
    'CREATE TABLE IF NOT EXISTS perguntas (
        id INTEGER PRIMARY KEY,
        texto_pergunta TEXT NOT NULL
    )',

    // 4. Tabela de Pesos (Conecta Perguntas e Categorias)
    'CREATE TABLE IF NOT EXISTS pesos_perguntas (
        pergunta_id INTEGER NOT NULL,
        categoria_id INTEGER NOT NULL,
        peso INTEGER NOT NULL,
        PRIMARY KEY (pergunta_id, categoria_id),
        FOREIGN KEY (pergunta_id) REFERENCES perguntas(id) ON DELETE CASCADE,
        FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
    )',

    // 5. Tabela de Sessões de Teste (Registra cada tentativa)
    'CREATE TABLE IF NOT EXISTS sessoes_teste (
        id INTEGER PRIMARY KEY,
        usuario_id INTEGER NOT NULL,
        data_realizacao DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    )',

    // 6. Tabela de Respostas dadas em uma Sessão
    'CREATE TABLE IF NOT EXISTS respostas_sessao (
        sessao_id INTEGER NOT NULL,
        pergunta_id INTEGER NOT NULL,
        valor_resposta INTEGER NOT NULL,
        PRIMARY KEY (sessao_id, pergunta_id),
        FOREIGN KEY (sessao_id) REFERENCES sessoes_teste(id) ON DELETE CASCADE,
        FOREIGN KEY (pergunta_id) REFERENCES perguntas(id) ON DELETE CASCADE
    )',

    // 7. Tabela de Pontuações Finais de uma Sessão
    'CREATE TABLE IF NOT EXISTS pontuacoes_sessao (
        sessao_id INTEGER NOT NULL,
        categoria_id INTEGER NOT NULL,
        pontuacao_final INTEGER NOT NULL,
        PRIMARY KEY (sessao_id, categoria_id),
        FOREIGN KEY (sessao_id) REFERENCES sessoes_teste(id) ON DELETE CASCADE,
        FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
    )'
];

// Executa todos os comandos de criação de tabela
foreach ($commands as $command) {
    $db->exec($command);
}

// --- FUNÇÃO PARA POPULAR DADOS INICIAIS (SÓ EXECUTA SE NECESSÁRIO) ---
function popularDadosIniciais($db) {
    // Verifica se as categorias já foram inseridas
    $result = $db->querySingle('SELECT COUNT(id) FROM categorias');
    if ($result > 0) {
        return; // Sai da função se já houver dados
    }

    // Inicia uma transação para performance e segurança
    $db->exec('BEGIN');

    // Dados que antes estavam no código
    $categorias = [
        ['exatas', 'Ciências Exatas'],
        ['humanas', 'Ciências Humanas'],
        ['biologicas', 'Ciências Biológicas'],
        ['artes', 'Artes e Criatividade'],
        ['negocios', 'Negócios e Administração'],
        ['tecnologia', 'Tecnologia'],
        ['saude', 'Saúde'],
        ['educacao', 'Educação']
    ];

    $stmt_cat = $db->prepare('INSERT INTO categorias (nome_chave, nome_exibicao) VALUES (?, ?)');
    foreach ($categorias as $cat) {
        $stmt_cat->bindValue(1, $cat[0], SQLITE3_TEXT);
        $stmt_cat->bindValue(2, $cat[1], SQLITE3_TEXT);
        $stmt_cat->execute();
    }

    // Mapeia nome_chave para id para facilitar a inserção dos pesos
    $map_categorias = [];
    $result = $db->query('SELECT id, nome_chave FROM categorias');
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $map_categorias[$row['nome_chave']] = $row['id'];
    }

    $perguntas_com_pesos = [
        ['texto' => 'Você gosta de trabalhar com números e resolver problemas matemáticos?', 'pesos' => ['exatas' => 3, 'tecnologia' => 2, 'negocios' => 1]],
        ['texto' => 'Você se sente confortável falando em público e expressando suas ideias?', 'pesos' => ['humanas' => 3, 'educacao' => 2, 'negocios' => 2, 'artes' => 1]],
        ['texto' => 'Você tem interesse em entender como os organismos vivos funcionam?', 'pesos' => ['biologicas' => 3, 'saude' => 2, 'exatas' => 1]],
        ['texto' => 'Você gosta de criar, desenhar ou trabalhar com design?', 'pesos' => ['artes' => 3, 'tecnologia' => 1, 'humanas' => 1]],
        ['texto' => 'Você se vê liderando equipes e tomando decisões importantes?', 'pesos' => ['negocios' => 3, 'humanas' => 2, 'educacao' => 1]],
        ['texto' => 'Você tem facilidade para aprender novas tecnologias e programas?', 'pesos' => ['tecnologia' => 3, 'exatas' => 2, 'negocios' => 1]],
        ['texto' => 'Você gosta de ajudar pessoas e cuidar da saúde delas?', 'pesos' => ['saude' => 3, 'biologicas' => 2, 'educacao' => 1]],
        ['texto' => 'Você tem prazer em ensinar e explicar conceitos para outras pessoas?', 'pesos' => ['educacao' => 3, 'humanas' => 2, 'saude' => 1]],
        ['texto' => 'Você se interessa por experimentos científicos e pesquisas?', 'pesos' => ['exatas' => 2, 'biologicas' => 2, 'tecnologia' => 2]],
        ['texto' => 'Você gosta de ler, escrever e trabalhar com textos?', 'pesos' => ['humanas' => 3, 'educacao' => 2, 'artes' => 1]],
        ['texto' => 'Você se sente motivado em resolver problemas ambientais?', 'pesos' => ['biologicas' => 3, 'exatas' => 1, 'humanas' => 1]],
        ['texto' => 'Você tem habilidades artísticas como música, pintura ou teatro?', 'pesos' => ['artes' => 3, 'humanas' => 1, 'educacao' => 1]],
        ['texto' => 'Você gosta de planejar estratégias e analisar mercados?', 'pesos' => ['negocios' => 3, 'humanas' => 1, 'tecnologia' => 1]],
        ['texto' => 'Você tem interesse em programação e desenvolvimento de software?', 'pesos' => ['tecnologia' => 3, 'exatas' => 2, 'negocios' => 1]],
        ['texto' => 'Você se preocupa com o bem-estar físico e mental das pessoas?', 'pesos' => ['saude' => 3, 'biologicas' => 1, 'educacao' => 1]],
        ['texto' => 'Você gosta de trabalhar com crianças e jovens?', 'pesos' => ['educacao' => 3, 'saude' => 1, 'humanas' => 1]],
        ['texto' => 'Você tem facilidade para trabalhar com dados e estatísticas?', 'pesos' => ['exatas' => 3, 'tecnologia' => 2, 'negocios' => 2]],
        ['texto' => 'Você se interessa por questões sociais e políticas?', 'pesos' => ['humanas' => 3, 'educacao' => 2, 'negocios' => 1]],
        ['texto' => 'Você gosta de trabalhar ao ar livre e com a natureza?', 'pesos' => ['biologicas' => 3, 'saude' => 1, 'exatas' => 1]],
        ['texto' => 'Você tem interesse em comunicação visual e marketing?', 'pesos' => ['artes' => 2, 'negocios' => 2, 'tecnologia' => 1]]
    ];

    $stmt_perg = $db->prepare('INSERT INTO perguntas (texto_pergunta) VALUES (?)');
    $stmt_peso = $db->prepare('INSERT INTO pesos_perguntas (pergunta_id, categoria_id, peso) VALUES (?, ?, ?)');

    foreach ($perguntas_com_pesos as $p) {
        // Insere a pergunta
        $stmt_perg->bindValue(1, $p['texto'], SQLITE3_TEXT);
        $stmt_perg->execute();
        $pergunta_id = $db->lastInsertRowID();

        // Insere os pesos associados
        foreach ($p['pesos'] as $chave_cat => $peso) {
            $categoria_id = $map_categorias[$chave_cat];
            $stmt_peso->bindValue(1, $pergunta_id, SQLITE3_INTEGER);
            $stmt_peso->bindValue(2, $categoria_id, SQLITE3_INTEGER);
            $stmt_peso->bindValue(3, $peso, SQLITE3_INTEGER);
            $stmt_peso->execute();
        }
    }

    // Insere o admin padrão (se não existir)
    $hashed_password = password_hash('admin', PASSWORD_DEFAULT);
    $stmt_admin = $db->prepare("INSERT OR IGNORE INTO admins (username, password) VALUES ('admin', ?)");
    $stmt_admin->bindValue(1, $hashed_password, SQLITE3_TEXT);
    $stmt_admin->execute();

    // Finaliza a transação
    $db->exec('COMMIT');
}

// Chama a função para popular os dados
popularDadosIniciais($db);

?>