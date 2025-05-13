$(document).ready(function() {
    // Функция для загрузки контента страницы из внешнего файла
    function loadPage(pageId) {
        const templateFile = `templates/${pageId}.html`;
        $.ajax({
            url: templateFile,
            dataType: 'html',
            success: function(template) {
                $('#main-container').html(template);

                // Активируем соответствующий пункт навигации
                $('.bottom-navigation a').removeClass('active');
                $(`.bottom-navigation a[data-page="${pageId}"]`).addClass('active');

                // Инициализация обработчиков для форм после загрузки страницы
                if (pageId === 'register') {
                    $('#register-form').off('submit').on('submit', handleRegistration); // Отключаем старые и подключаем новые обработчики
                } else if (pageId === 'login') {
                    $('#login-form').off('submit').on('submit', handleLogin);
                } else if (pageId === 'forgot-password') {
                    $('#forgot-password-form').off('submit').on('submit', handleForgotPassword);
                } else if (pageId === 'profile') {
                    loadProfileContent();
                }
            },
            error: function() {
                $('#main-container').html('<p>Ошибка загрузки страницы.</p>');
            }
        });
    }

    // Функция для обработки регистрации (без изменений)
    function handleRegistration(event) {
        event.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: 'register.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.success);
                    loadPage('login');
                } else if (response.error) {
                    alert(response.error);
                }
            },
            error: function() {
                alert('Ошибка при выполнении запроса на регистрацию.');
            }
        });
    }

    // Функция для обработки авторизации (без изменений)
    function handleLogin(event) {
        event.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: 'login.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.success);
                    loadPage('profile');
                    loadProfileContent(response.username);
                } else if (response.error) {
                    alert(response.error);
                }
            },
            error: function() {
                alert('Ошибка при выполнении запроса на авторизацию.');
            }
        });
    }

    // Функция для обработки запроса сброса пароля (без изменений)
    function handleForgotPassword(event) {
        event.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: 'forgot_password.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.success);
                    loadPage('login');
                } else if (response.error) {
                    alert(response.error);
                }
            },
            error: function() {
                alert('Ошибка при выполнении запроса на сброс пароля.');
            }
        });
    }

    // Функция для загрузки контента профиля (без изменений)
    function loadProfileContent(username) {
        let content = '';
        if (username) {
            content += `<p class="mb-2 text-gray-700">Вы вошли как: <span class="font-semibold">${username}</span></p>`;
            content += '<button id="logout-button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2">Выйти</button>';
            content += '<button id="change-password-button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Сменить пароль</button>';
        } else {
            content += '<p class="mb-2 text-gray-700">Вы не авторизованы.</p>';
            content += `<p><a href="#register" data-page="register" class="text-blue-500 hover:underline">Зарегистрироваться</a> или <a href="#login" data-page="login" class="text-blue-500 hover:underline">Войти</a></p>`;
        }
        $('#profile-content').html(content);

        $('#logout-button').off('click').on('click', function() {
            alert('Функциональность выхода будет реализована позже.');
            loadPage('profile');
        });

        $('#change-password-button').off('click').on('click', function() {
            alert('Функциональность смены пароля (через профиль) будет реализована позже.');
        });
    }

    // Обработчик кликов по элементам навигации (без изменений)
    $('.bottom-navigation a').on('click', function(e) {
        e.preventDefault();
        const pageId = $(this).data('page');
        loadPage(pageId);
        window.location.hash = $(this).attr('href');
    });

    // Обработчик события hashchange
    $(window).on('hashchange', function() {
        const hash = window.location.hash.substring(1);
        loadPage(hash);
    });

    // Загрузка начальной страницы (без изменений)
    if (window.location.hash) {
        const initialPage = window.location.hash.substring(1);
        loadPage(initialPage);
    } else {
        loadPage('main');
        window.location.hash = '#main';
    }
});