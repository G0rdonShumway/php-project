<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        echo json_encode(['error' => 'Пожалуйста, заполните имя пользователя и пароль.']);
        $conn->close();
        exit();
    }

    $sql_select = "SELECT id, username, password_hash, email_verified FROM users WHERE username = '$username'";
    $result_select = $conn->query($sql_select);

    if ($result_select->num_rows == 1) {
        $user = $result_select->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            if (!$user['email_verified']) {
                echo json_encode(['error' => 'Ваш адрес электронной почты не подтвержден. Пожалуйста, проверьте свою почту.']);
                $conn->close();
                exit();
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            $sql_update_login = "UPDATE users SET last_login = NOW() WHERE id = " . $user['id'];
            $conn->query($sql_update_login);

            echo json_encode(['success' => 'Авторизация прошла успешно!', 'username' => $user['username']]);
        } else {
            echo json_encode(['error' => 'Неверное имя пользователя или пароль.']);
        }
    } else {
        echo json_encode(['error' => 'Пользователь не найден.']);
    }

    $conn->close();
} else {
    echo json_encode(['error' => 'Неверный метод запроса.']);
    $conn->close();
}
?>