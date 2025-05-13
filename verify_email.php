<?php
include 'db_connect.php';

if (isset($_GET['token'])) {
    $token = $conn->real_escape_string(filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING));

    $sql_select = "SELECT id, email FROM users WHERE verification_token = '$token' AND verification_token_expiry > NOW() AND email_verified = FALSE";
    $result_select = $conn->query($sql_select);
    $user = $result_select->fetch_assoc();

    if ($user) {
        $sql_update = "UPDATE users SET email_verified = TRUE, verification_token = NULL, verification_token_expiry = NULL WHERE id = " . $user['id'];
        if ($conn->query($sql_update) === TRUE) {
            echo "Ваш адрес электронной почты успешно подтвержден! Теперь вы можете войти.";
            // Можно добавить ссылку на страницу входа
        } else {
            echo "Ошибка при подтверждении адреса электронной почты: " . $conn->error;
        }
    } else {
        echo "Неверная или устаревшая ссылка для подтверждения.";
    }
} else {
    echo "Отсутствует токен подтверждения.";
}

$conn->close();
?>