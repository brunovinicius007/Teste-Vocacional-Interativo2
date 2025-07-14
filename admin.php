<?php
session_start();

// Senha fixa para o admin. Em um projeto real, isso seria mais seguro.
const ADMIN_PASSWORD = 'admin123';

$error = '';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: report.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['password']) && $_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: report.php');
        exit();
    } else {
        $error = 'Senha incorreta!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login do Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
    </style>
</head>
<body class="gradient-bg flex items-center justify-center h-screen">
    <div class="login-card w-full max-w-md rounded-lg shadow-xl p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-8 flex items-center justify-center">
            <i class="fas fa-user-shield mr-3"></i>
            Acesso Restrito
        </h2>
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" action="admin.php" class="space-y-6">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Senha de Administrador</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" name="password" id="password" class="block w-full pl-10 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="********" required>
                </div>
            </div>
            <div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300">
                    Entrar
                </button>
            </div>
        </form>
    </div>
</body>
</html>