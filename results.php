<?php
session_start();

if (!isset($_SESSION['resultado'])) {
    header('Location: index.php');
    exit();
}

$resultado = $_SESSION['resultado'];
$nome_usuario = htmlspecialchars($resultado['nome']);
$pontuacoes = $resultado['pontuacoes'];

$categorias = [
    'exatas' => 'Ciências Exatas', 'humanas' => 'Ciências Humanas', 'biologicas' => 'Ciências Biológicas',
    'artes' => 'Artes e Criatividade', 'negocios' => 'Negócios', 'tecnologia' => 'Tecnologia',
    'saude' => 'Saúde', 'educacao' => 'Educação'
];

// Prepara os dados para o gráfico
$chart_data = [];
foreach ($pontuacoes as $cat_chave => $score) {
    $chart_data[] = ['area' => $categorias[$cat_chave], 'pontuacao' => $score];
}

// Ordena as pontuações para pegar as top 3 áreas
arsort($pontuacoes);
$top_areas_keys = array_slice(array_keys($pontuacoes), 0, 3);

// Dados de sugestões de carreira (mantidos aqui por simplicidade, poderiam vir de um JSON externo)
$careerSuggestions = [
    'exatas' => [
        ['name' => 'Engenharia', 'description' => 'Diversas áreas como Civil, Mecânica, Elétrica'],
        ['name' => 'Ciência da Computação', 'description' => 'Desenvolvimento de software e sistemas'],
        ['name' => 'Estatística', 'description' => 'Análise de dados e modelagem']
    ],
    'humanas' => [
        ['name' => 'Direito', 'description' => 'Advocacia, magistratura e consultoria'],
        ['name' => 'Psicologia', 'description' => 'Atendimento clínico e organizacional'],
        ['name' => 'Jornalismo', 'description' => 'Comunicação e produção de conteúdo']
    ],
    'biologicas' => [
        ['name' => 'Medicina', 'description' => 'Diagnóstico e tratamento de doenças'],
        ['name' => 'Biologia', 'description' => 'Pesquisa científica e meio ambiente'],
        ['name' => 'Veterinária', 'description' => 'Saúde e cuidado de animais']
    ],
    'artes' => [
        ['name' => 'Design Gráfico', 'description' => 'Comunicação visual e criação de marcas'],
        ['name' => 'Arquitetura', 'description' => 'Projeto e construção de espaços'],
        ['name' => 'Cinema', 'description' => 'Produção audiovisual e direção']
    ],
    'negocios' => [
        ['name' => 'Administração', 'description' => 'Gestão de empresas e equipes'],
        ['name' => 'Marketing', 'description' => 'Estratégias de mercado e vendas'],
        ['name' => 'Contabilidade', 'description' => 'Gestão financeira e fiscal']
    ],
    'tecnologia' => [
        ['name' => 'Engenharia de Software', 'description' => 'Criação de sistemas e aplicativos'],
        ['name' => 'Segurança da Informação', 'description' => 'Proteção de dados e sistemas'],
        ['name' => 'Ciência de Dados', 'description' => 'Análise e interpretação de grandes volumes de dados']
    ],
    'saude' => [
        ['name' => 'Enfermagem', 'description' => 'Cuidado direto e assistência a pacientes'],
        ['name' => 'Fisioterapia', 'description' => 'Reabilitação e terapia física'],
        ['name' => 'Nutrição', 'description' => 'Planejamento alimentar e saúde']
    ],
    'educacao' => [
        ['name' => 'Pedagogia', 'description' => 'Educação infantil e fundamental'],
        ['name' => 'Licenciaturas', 'description' => 'Ensino em áreas específicas (Letras, Matemática, etc.)'],
        ['name' => 'Gestão Escolar', 'description' => 'Administração de instituições de ensino']
    ]
];

// Limpa a sessão para que o usuário não possa ver o mesmo resultado recarregando a página
session_destroy();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Teste Vocacional</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1rem; /* Mais arredondado */
            box-shadow: 0 10px 30px rgba(0,0,0,0.15); /* Sombra mais suave */
        }
        .result-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .career-card {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 9999px;
            transition: width 0.3s ease-in-out;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex flex-col items-center py-10">
    <!-- Header -->
    <header class="text-center py-8 w-full">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">
                <i class="fas fa-graduation-cap mr-4"></i>Seu Resultado Vocacional
            </h1>
            <p class="text-xl text-white opacity-90 max-w-2xl mx-auto">
                Uma análise detalhada do seu perfil e sugestões de carreira.
            </p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 pb-8 w-full max-w-4xl">
        <!-- Análise do Perfil -->
        <div class="result-card p-8 mb-8 text-white">
            <h2 class="text-2xl font-bold mb-4 flex items-center">
                <i class="fas fa-user-graduate mr-3"></i>Análise do Perfil de <?= $nome_usuario ?>
            </h2>
            <p class="text-lg leading-relaxed">
                Com base nas suas respostas, identificamos suas áreas de maior afinidade. Sua área principal é 
                <strong><?= htmlspecialchars($categorias[key($pontuacoes)]) ?></strong>.
                Suas três áreas de maior destaque são:
                <?php 
                $top_area_names = [];
                foreach ($top_areas_keys as $key) {
                    $top_area_names[] = htmlspecialchars($categorias[$key]);
                }
                echo implode(', ', $top_area_names) . '.';
                ?>
                Explore o gráfico e as sugestões de carreira abaixo para descobrir mais sobre suas possibilidades!
            </p>
        </div>

        <!-- Gráfico de Habilidades -->
        <div class="card p-8 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-bar mr-3 text-purple-600"></i>Suas Áreas de Maior Afinidade
            </h3>
            <div style="height: 400px;">
                <canvas id="skillsChart"></canvas>
            </div>
        </div>

        <!-- Sugestões de Carreira -->
        <div class="career-card p-8 mb-8 text-white">
            <h3 class="text-xl font-bold mb-6 flex items-center">
                <i class="fas fa-briefcase mr-3"></i>Carreiras Recomendadas para Você
            </h3>
            <div class="grid md:grid-cols-1 lg:grid-cols-2 gap-6">
                <?php 
                foreach ($top_areas_keys as $area_key) {
                    $area_name_display = $categorias[$area_key];
                    if (isset($careerSuggestions[$area_name_display])) {
                        echo '<div class="bg-white bg-opacity-20 p-6 rounded-lg shadow-md">';
                        echo '<h4 class="font-bold text-xl mb-3">' . htmlspecialchars($area_name_display) . '</h4>';
                        echo '<ul class="list-disc list-inside space-y-2">';
                        foreach ($careerSuggestions[$area_name_display] as $carreira) {
                            echo '<li><span class="font-semibold">' . htmlspecialchars($carreira['name']) . ':</span> ' . htmlspecialchars($carreira['description']) . '</li>';
                        }
                        echo '</ul>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>

        <!-- Resultados Detalhados -->
        <div class="card p-8 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-list-alt mr-3 text-indigo-600"></i>Pontuações Detalhadas por Área
            </h3>
            <div class="space-y-4">
                <?php 
                // Ordena as pontuações para exibição detalhada
                $sorted_detailed_scores = $pontuacoes;
                arsort($sorted_detailed_scores);

                foreach ($sorted_detailed_scores as $cat_chave => $score) {
                    $percentage = round(($score / max($pontuacoes)) * 100); // Normaliza para porcentagem
                    echo '<div class="bg-gray-50 p-4 rounded-lg shadow-sm">';
                    echo '<div class="flex justify-between items-center mb-2">';
                    echo '<span class="font-semibold text-gray-800">' . htmlspecialchars($categorias[$cat_chave]) . '</span>';
                    echo '<span class="text-sm text-gray-600">' . $percentage . '%</span>';
                    echo '</div>';
                    echo '<div class="w-full bg-gray-200 rounded-full h-2">';
                    echo '<div class="progress-bar-fill" style="width: ' . $percentage . '%"></div>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="text-center py-6 text-white w-full">
        <p class="text-sm opacity-75">
            <a href="index.php" class="hover:underline text-white">
                <i class="fas fa-redo mr-2"></i>Fazer o teste novamente
            </a>
        </p>
    </footer>

    <script>
        const ctx = document.getElementById('skillsChart').getContext('2d');
        const chartData = <?= json_encode($chart_data) ?>;
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.map(d => d.area),
                datasets: [{
                    label: 'Pontuação de Afinidade',
                    data: chartData.map(d => d.pontuacao),
                    backgroundColor: [
                        'rgba(139, 92, 246, 0.8)', 'rgba(6, 182, 212, 0.8)', 'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)', 'rgba(239, 68, 68, 0.8)', 'rgba(99, 102, 241, 0.8)',
                        'rgba(236, 72, 153, 0.8)', 'rgba(132, 204, 22, 0.8)'
                    ],
                    borderColor: 'rgba(255, 255, 255, 0.3)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#4B5563' }
                    },
                    x: {
                        ticks: { color: '#4B5563' }
                    }
                }
            }
        });
    </script>
</body>
</html>