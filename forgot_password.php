<?php
include 'db_connect.php';

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function sendPasswordResetEmail($email, $token) {
    $subject = 'Запрос на сброс пароля';
    $resetLink = 'YOUR_DOMAIN/reset_password.php?token=' . $token; // Замените YOUR_DOMAIN
    $message = "Вы получили это письмо, так как был запрошен сброс пароля для вашей учетной записи.\n\nПерейдите по следующей ссылке, чтобы сбросить свой пароль:\n" . $resetLink . "\n\nЕсли вы не запрашивали сброс пароля, проигнорируйте это письмо.";
    $headers = 'From: noreply@yourdomain.com' . "\r\n" .
               'Reply-To: noreply@yourdomain.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();

    mail($email, $subject, $message, $headers);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => 'Некорректный формат email.']);
        $conn->close();
        exit();
    }

    $sql_select = "SELECT id FROM users WHERE email = '$email' AND email_verified = TRUE";
    $result_select = $conn->query($sql_select);
    $user = $result_select->fetch_assoc();

    if ($user) {
        $resetToken = generateToken();
        $expiry = date('Y-m-d H:i:s', time() + (60 * 30));

        $sql_insert_token = "INSERT INTO password_reset_tokens (user_id, token, expiry) VALUES (" . $user['id'] . ", '$resetToken', '$expiry')";

        if ($conn->query($sql_insert_token) === TRUE) {
            sendPasswordResetEmail($email, $resetToken);
            echo json_encode(['success' => 'Запрос на сброс пароля отправлен на ваш email. Пожалуйста, проверьте свою почту.']);
        } else {
            echo json_encode(['error' => 'Ошибка при создании запроса на сброс пароля: ' . $conn->error]);
        }
    } else {
        echo json_encode(['error' => 'Пользователь с таким email не найден или email не подтвержден.']);
    }

    $conn->close();
} else {
    echo json_encode(['error' => 'Неверный метод запроса.']);
    $conn->close();
}
?>