<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin.php');
    exit();
}

$data_dir = __DIR__ . '/data';
$results_files = glob($data_dir . '/*.json');
$all_results = [];

$categorias_map = [
    'exatas' => 'Ciências Exatas', 'humanas' => 'Ciências Humanas', 'biologicas' => 'Ciências Biológicas',
    'artes' => 'Artes e Criatividade', 'negocios' => 'Negócios', 'tecnologia' => 'Tecnologia',
    'saude' => 'Saúde', 'educacao' => 'Educação'
];

// Inicializa o contador para o gráfico
$report_summary = array_fill_keys(array_values($categorias_map), 0);

if ($results_files) {
    foreach ($results_files as $file) {
        $content = file_get_contents($file);
        $result_data = json_decode($content, true);
        $all_results[] = $result_data;

        // Processa os dados para o gráfico
        $pontuacoes = $result_data['pontuacoes'];
        arsort($pontuacoes);
        $top_area_key = key($pontuacoes);
        if (isset($categorias_map[$top_area_key])) {
            $top_area_name = $categorias_map[$top_area_key];
            $report_summary[$top_area_name]++;
        }
    }
    // Ordena a lista de testes do mais recente para o mais antigo
    usort($all_results, function($a, $b) {
        return strtotime($b['data_realizacao']) - strtotime($a['data_realizacao']);
    });
}

// Prepara os dados para o Chart.js
$chart_labels = json_encode(array_keys($report_summary));
$chart_values = json_encode(array_values($report_summary));

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Testes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800"><i class="fas fa-user-shield mr-3"></i>Relatório de Testes</h1>
            <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition duration-300">Sair</a>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-8">
        <!-- Seção do Gráfico -->
        <section class="mb-8">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-chart-pie mr-3"></i>Resumo de Resultados</h2>
                <div style="height: 400px;">
                    <canvas id="summaryChart"></canvas>
                </div>
            </div>
        </section>

        <!-- Seção da Tabela de Detalhes -->
        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-list-ul mr-3"></i>Detalhes dos Testes Realizados</h2>
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Data</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Nome</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Email</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Idade</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Telefone</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Resultado Principal</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php if (empty($all_results)): ?>
                                <tr><td colspan="6" class="text-center py-4">Nenhum teste foi realizado ainda.</td></tr>
                            <?php else: ?>
                                <?php foreach ($all_results as $result): ?>
                                    <?php 
                                        $pontuacoes = $result['pontuacoes'];
                                        arsort($pontuacoes);
                                        $top_area_key = key($pontuacoes);
                                        $top_area_name = $categorias_map[$top_area_key] ?? 'N/A';
                                    ?>
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="py-3 px-4"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($result['data_realizacao']))) ?></td>
                                        <td class="py-3 px-4"><?= htmlspecialchars($result['nome']) ?></td>
                                        <td class="py-3 px-4"><?= htmlspecialchars($result['email'] ?? 'N/A') ?></td>
                                        <td class="py-3 px-4"><?= htmlspecialchars($result['idade'] ?? 'N/A') ?></td>
                                        <td class="py-3 px-4"><?= htmlspecialchars($result['telefone'] ?? 'N/A') ?></td>
                                        <td class="py-3 px-4 font-semibold"><?= htmlspecialchars($top_area_name) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script>
        const ctx = document.getElementById('summaryChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= $chart_labels ?>,
                datasets: [{
                    label: 'Nº de Usuários',
                    data: <?= $chart_values ?>,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)', 'rgba(5, 150, 105, 0.8)', 'rgba(217, 119, 6, 0.8)',
                        'rgba(220, 38, 38, 0.8)', 'rgba(139, 92, 246, 0.8)', 'rgba(234, 88, 12, 0.8)',
                        'rgba(236, 72, 153, 0.8)', 'rgba(16, 185, 129, 0.8)'
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
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    </script>
</body>
</html>