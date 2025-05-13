<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Croupier Assistant</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script.js"></script>
</head>
<body class="bg-gray-100">
    <div class="bg-gray-800 text-white py-4 text-center shadow-md">
        <h1 class="text-xl font-semibold">Тренировка счета</h1>
    </div>

    <div class="container mx-auto py-8" id="main-container">
        </div>

    <nav class="bg-gray-200 fixed bottom-0 left-0 right-0 flex justify-around py-2 border-t border-gray-300 shadow-md">
        <a href="#main" data-page="main" class="block text-gray-600 hover:text-blue-500 py-2 px-4 text-center nav-item active">Главная</a>
        <a href="#stats" data-page="stats" class="block text-gray-600 hover:text-blue-500 py-2 px-4 text-center nav-item">Статистика</a>
        <a href="#profile" data-page="profile" class="block text-gray-600 hover:text-blue-500 py-2 px-4 text-center nav-item">Профиль</a>
    </nav>

</body>
</html>
<!-- <?php phpinfo(); ?> -->