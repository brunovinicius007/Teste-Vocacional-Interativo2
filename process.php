<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// --- Dados do Teste (Pesos e Categorias) ---
// Em uma aplicação sem DB, mantemos a lógica de cálculo aqui.
$pesos_perguntas = [
    1 => ['exatas' => 3, 'tecnologia' => 2, 'negocios' => 1],
    2 => ['humanas' => 3, 'educacao' => 2, 'negocios' => 2, 'artes' => 1],
    3 => ['biologicas' => 3, 'saude' => 2, 'exatas' => 1],
    4 => ['artes' => 3, 'tecnologia' => 1, 'humanas' => 1],
    5 => ['negocios' => 3, 'humanas' => 2, 'educacao' => 1],
    6 => ['tecnologia' => 3, 'exatas' => 2, 'negocios' => 1],
    7 => ['saude' => 3, 'biologicas' => 2, 'educacao' => 1],
    8 => ['educacao' => 3, 'humanas' => 2, 'saude' => 1],
    9 => ['exatas' => 2, 'biologicas' => 2, 'tecnologia' => 2],
    10 => ['humanas' => 3, 'educacao' => 2, 'artes' => 1],
    11 => ['biologicas' => 3, 'exatas' => 1, 'humanas' => 1],
    12 => ['artes' => 3, 'humanas' => 1, 'educacao' => 1],
    13 => ['negocios' => 3, 'humanas' => 1, 'tecnologia' => 1],
    14 => ['tecnologia' => 3, 'exatas' => 2, 'negocios' => 1],
    15 => ['saude' => 3, 'biologicas' => 1, 'educacao' => 1],
    16 => ['educacao' => 3, 'saude' => 1, 'humanas' => 1],
    17 => ['exatas' => 3, 'tecnologia' => 2, 'negocios' => 2],
    18 => ['humanas' => 3, 'educacao' => 2, 'negocios' => 1],
    19 => ['biologicas' => 3, 'saude' => 1, 'exatas' => 1],
    20 => ['artes' => 2, 'negocios' => 2, 'tecnologia' => 1]
];

$categorias = [
    'exatas' => 'Ciências Exatas', 'humanas' => 'Ciências Humanas', 'biologicas' => 'Ciências Biológicas',
    'artes' => 'Artes e Criatividade', 'negocios' => 'Negócios', 'tecnologia' => 'Tecnologia',
    'saude' => 'Saúde', 'educacao' => 'Educação'
];

// --- Processamento ---
$nome = trim($_POST['nome'] ?? 'Anônimo');
$email = trim($_POST['email'] ?? '');
$idade = trim($_POST['idade'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$respostas = $_POST['respostas'] ?? [];

// Validação básica para campos obrigatórios
if (empty($nome) || empty($idade) || empty($respostas)) {
    die('Erro: Nome, Idade e todas as perguntas são obrigatórios.');
}

// Calcula as pontuações
$pontuacoes = array_fill_keys(array_keys($categorias), 0);
foreach ($respostas as $pergunta_id => $valor) {
    if (isset($pesos_perguntas[$pergunta_id])) {
        foreach ($pesos_perguntas[$pergunta_id] as $cat => $peso) {
            $pontuacoes[$cat] += $peso * (int)$valor;
        }
    }
}

// Ordena para encontrar a principal
arsort($pontuacoes);

// --- Salva os dados em um arquivo JSON ---
$data_dir = __DIR__ . '/data';
if (!is_dir($data_dir)) {
    mkdir($data_dir, 0777, true);
}

$resultado_final = [
    'id' => uniqid(),
    'nome' => $nome,
    'email' => $email,
    'idade' => $idade,
    'telefone' => $telefone,
    'data_realizacao' => date('Y-m-d H:i:s'),
    'pontuacoes' => $pontuacoes,
    'respostas' => $respostas
];

$filename = $data_dir . '/' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.json';
file_put_contents($filename, json_encode($resultado_final, JSON_PRETTY_PRINT));

// --- Prepara para a página de resultados ---
$_SESSION['resultado'] = $resultado_final;

header('Location: results.php');
exit();
?>