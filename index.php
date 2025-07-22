<?php
// Os dados das perguntas agora vivem aqui, em vez de no banco de dados.
$perguntas = [
    ['id' => 1, 'texto' => 'Você gosta de trabalhar com números e resolver problemas matemáticos?'],
    ['id' => 2, 'texto' => 'Você se sente confortável falando em público e expressando suas ideias?'],
    ['id' => 3, 'texto' => 'Você tem interesse em entender como os organismos vivos funcionam?'],
    ['id' => 4, 'texto' => 'Você gosta de criar, desenhar ou trabalhar com design?'],
    ['id' => 5, 'texto' => 'Você se vê liderando equipes e tomando decisões importantes?'],
    ['id' => 6, 'texto' => 'Você tem facilidade para aprender novas tecnologias e programas?'],
    ['id' => 7, 'texto' => 'Você gosta de ajudar pessoas e cuidar da saúde delas?'],
    ['id' => 8, 'texto' => 'Você tem prazer em ensinar e explicar conceitos para outras pessoas?'],
    ['id' => 9, 'texto' => 'Você se interessa por experimentos científicos e pesquisas?'],
    ['id' => 10, 'texto' => 'Você gosta de ler, escrever e trabalhar com textos?'],
    ['id' => 11, 'texto' => 'Você se sente motivado em resolver problemas ambientais?'],
    ['id' => 12, 'texto' => 'Você tem habilidades artísticas como música, pintura ou teatro?'],
    ['id' => 13, 'texto' => 'Você gosta de planejar estratégias e analisar mercados?'],
    ['id' => 14, 'texto' => 'Você tem interesse em programação e desenvolvimento de software?'],
    ['id' => 15, 'texto' => 'Você se preocupa com o bem-estar físico e mental das pessoas?'],
    ['id' => 16, 'texto' => 'Você gosta de trabalhar com crianças e jovens?'],
    ['id' => 17, 'texto' => 'Você tem facilidade para trabalhar com dados e estatísticas?'],
    ['id' => 18, 'texto' => 'Você se interessa por questões sociais e políticas?'],
    ['id' => 19, 'texto' => 'Você gosta de trabalhar ao ar livre e com a natureza?'],
    ['id' => 20, 'texto' => 'Você tem interesse em comunicação visual e marketing?']
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Vocacional Interativo - Descubra sua Vocação</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
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
        .question-card {
            transition: all 0.3s ease;
            border: 2px solid transparent; /* Borda para destaque */
        }
        .question-card.selected {
            border-color: #667eea; /* Cor da borda ao selecionar */
            box-shadow: 0 0 15px rgba(102, 126, 234, 0.4); /* Sombra ao selecionar */
        }
        .radio-label {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background-color 0.2s ease, border-color 0.2s ease;
            border: 1px solid #e2e8f0; /* Borda padrão */
        }
        .radio-label:hover {
            background-color: #edf2f7; /* Cor ao passar o mouse */
        }
        .radio-label input[type="radio"] {
            margin-right: 0.75rem;
            /* Esconde o radio padrão e usa um customizado */
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid #a0aec0;
            border-radius: 50%;
            outline: none;
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .radio-label input[type="radio"]:checked {
            border-color: #667eea;
            background-color: #667eea;
        }
        .radio-label input[type="radio"]:checked::before {
            content: '';
            width: 0.5rem;
            height: 0.5rem;
            background-color: white;
            border-radius: 50%;
        }
        .progress-bar-container {
            height: 0.75rem;
            border-radius: 9999px;
            background-color: #e2e8f0;
            overflow: hidden;
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
                <i class="fas fa-compass mr-4"></i>Teste Vocacional Interativo
            </h1>
            <p class="text-xl text-white opacity-90 max-w-2xl mx-auto">
                Descubra sua vocação profissional de forma rápida e intuitiva.
            </p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 pb-8 w-full max-w-4xl">
        <form id="testForm" action="process.php" method="POST">
            <!-- Seção de Informações Pessoais -->
            <section id="personal-info" class="card p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-user-circle mr-3 text-blue-600"></i>Informações Pessoais
                </h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome Completo <span class="text-red-500">*</span></label>
                        <input type="text" name="nome" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Seu nome completo" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="seu.email@exemplo.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Idade <span class="text-red-500">*</span></label>
                        <input type="number" name="idade" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Sua idade" min="1" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                        <input type="tel" name="telefone" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="(XX) XXXXX-XXXX">
                    </div>
                </div>
            </section>

            <!-- Barra de Progresso -->
            <div class="card p-6 mb-8">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-base font-semibold text-gray-700">Progresso do Teste</span>
                    <span id="progressText" class="text-base font-bold text-blue-600">0%</span>
                </div>
                <div class="progress-bar-container">
                    <div id="progressBar" class="progress-bar-fill" style="width: 0%"></div>
                </div>
                <p id="questionCounter" class="text-sm text-gray-500 text-right mt-2">0 de <?= count($perguntas) ?> perguntas respondidas</p>
            </div>

            <!-- Seção do Questionário -->
            <section id="questionnaire" class="card p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-clipboard-list mr-3 text-green-600"></i>Questionário Vocacional
                </h2>
                <div class="space-y-8">
                    <?php foreach ($perguntas as $index => $pergunta): ?>
                        <div class="question-card bg-white p-6 rounded-lg shadow-md" data-question-id="<?= $pergunta['id'] ?>">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">
                                <span class="text-blue-600 mr-2"><?= ($index + 1) ?>.</span> <?= htmlspecialchars($pergunta['texto']) ?>
                            </h4>
                            <div class="space-y-3">
                                <?php foreach ([5 => 'Concordo totalmente', 4 => 'Concordo parcialmente', 3 => 'Neutro', 2 => 'Discordo parcialmente', 1 => 'Discordo totalmente'] as $valor => $label): ?>
                                    <label class="radio-label">
                                        <input type="radio" name="respostas[<?= $pergunta['id'] ?>]" value="<?= $valor ?>" required>
                                        <span class="text-gray-700 text-base"><?= $label ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-10">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-lg text-lg">
                        <i class="fas fa-chart-line mr-2"></i>Analisar Resultados
                    </button>
                </div>
            </section>
        </form>
    </main>

    <!-- Footer -->
    <footer class="text-center py-6 text-white w-full">
        <p class="text-sm opacity-75">
            <i class="fas fa-heart mr-2"></i>Desenvolvido para ajudar você a descobrir sua vocação.
        </p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('testForm');
            const radioInputs = form.querySelectorAll('input[type="radio"]');
            const totalQuestions = <?= count($perguntas) ?>;
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            const questionCounter = document.getElementById('questionCounter');
            const questionCards = form.querySelectorAll('.question-card');

            const answeredQuestions = new Set();

            function updateProgress() {
                answeredQuestions.clear();
                radioInputs.forEach(input => {
                    if (input.checked) {
                        answeredQuestions.add(input.name);
                    }
                });

                const progress = totalQuestions > 0 ? (answeredQuestions.size / totalQuestions) * 100 : 0;
                progressBar.style.width = `${progress}%`;
                progressText.textContent = `${Math.round(progress)}%`;
                questionCounter.textContent = `${answeredQuestions.size} de ${totalQuestions} perguntas respondidas`;
            }

            radioInputs.forEach(input => {
                input.addEventListener('change', (event) => {
                    // Remove a classe 'selected' de todos os labels da mesma pergunta
                    const questionId = event.target.name.match(/\d+/)[0];
                    const currentQuestionCard = document.querySelector(`.question-card[data-question-id="${questionId}"]`);

                    // Adiciona a classe 'selected' ao card da pergunta
                    questionCards.forEach(card => card.classList.remove('selected'));
                    currentQuestionCard.classList.add('selected');

                    updateProgress();
                });
            });

            updateProgress(); // Chamada inicial para definir o estado
        });
    </script>
</body>
</html>
