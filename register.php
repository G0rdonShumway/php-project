<?php
include 'db_connect.php';

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function sendVerificationEmail($email, $token) {
    $subject = 'Подтверждение адреса электронной почты';
    $verificationLink = 'YOUR_DOMAIN/verify_email.php?token=' . $token; // Замените YOUR_DOMAIN
    $message = "Пожалуйста, перейдите по следующей ссылке, чтобы подтвердить свой адрес электронной почты: " . $verificationLink;
    $headers = 'From: noreply@yourdomain.com' . "\r\n" .
               'Reply-To: noreply@yourdomain.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();

    mail($email, $subject, $message, $headers);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $password = $_POST['password'];
    $email = $conn->real_escape_string(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $firstName = $conn->real_escape_string(filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING));
    $lastName = $conn->real_escape_string(filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING));

    if (empty($username) || empty($password) || empty($email)) {
        echo json_encode(['error' => 'Пожалуйста, заполните имя пользователя, пароль и email.']);
        $conn->close();
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => 'Некорректный формат email.']);
        $conn->close();
        exit();
    }

    $sql_check = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        $row = $result_check->fetch_assoc();
        $sql_check_username = "SELECT id FROM users WHERE username = '$username'";
        $result_check_username = $conn->query($sql_check_username);
        if ($result_check_username->num_rows > 0) {
            echo json_encode(['error' => 'Пользователь с таким именем уже существует.']);
            $conn->close();
            exit();
        }
        $sql_check_email = "SELECT id FROM users WHERE email = '$email'";
        $result_check_email = $conn->query($sql_check_email);
        if ($result_check_email->num_rows > 0) {
            echo json_encode(['error' => 'Пользователь с таким email уже зарегистрирован.']);
            $conn->close();
            exit();
        }
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $verificationToken = generateToken();
    $verificationExpiry = date('Y-m-d H:i:s', time() + (60 * 60 * 24));

    $sql_insert = "INSERT INTO users (username, password_hash, email, first_name, last_name, verification_token, verification_token_expiry) VALUES ('$username', '$password_hash', '$email', '$firstName', '$lastName', '$verificationToken', '$verificationExpiry')";

    if ($conn->query($sql_insert) === TRUE) {
        sendVerificationEmail($email, $verificationToken);
        echo json_encode(['success' => 'Регистрация прошла успешно! Пожалуйста, проверьте свою почту для подтверждения.']);
    } else {
        echo json_encode(['error' => 'Ошибка при регистрации пользователя: ' . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(['error' => 'Неверный метод запроса.']);
    $conn->close();
}
?>