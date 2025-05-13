<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['token']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    $token = $conn->real_escape_string(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING));
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($newPassword) || empty($confirmPassword)) {
        echo "Пожалуйста, введите новый пароль и подтвердите его.";
        $conn->close();
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        echo "Новый пароль и подтверждение не совпадают.";
        $conn->close();
        exit();
    }

    $sql_select_token = "SELECT user_id FROM password_reset_tokens WHERE token = '$token' AND expiry > NOW()";
    $result_select_token = $conn->query($sql_select_token);
    $resetToken = $result_select_token->fetch_assoc();

    if ($resetToken) {
        $userId = $resetToken['user_id'];
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql_update_password = "UPDATE users SET password_hash = '$newPasswordHash' WHERE id = $userId";

        if ($conn->query($sql_update_password) === TRUE) {
            $sql_delete_token = "DELETE FROM password_reset_tokens WHERE token = '$token'";
            $conn->query($sql_delete_token);

            echo "Ваш пароль успешно изменен! Теперь вы можете войти с новым паролем.";
        } else {
            echo "Ошибка при обновлении пароля: " . $conn->error;
        }
    } else {
        echo "Неверный или устаревший токен сброса пароля.";
    }

    $conn->close();
} elseif (isset($_GET['token'])) {
    $token = $conn->real_escape_string(filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING));
    ?>
    <h2>Сброс пароля</h2>
    <form method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <div>
            <label for="new_password">Новый пароль:</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        <div>
            <label for="confirm_password">Подтвердите новый пароль:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit">Сбросить пароль</button>
    </form>
    <?php
} else {
    echo "Неверный запрос на сброс пароля.";
    $conn->close();
}
?>