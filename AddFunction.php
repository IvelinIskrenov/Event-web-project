<?php
require_once 'DB_configuration.php';
session_start();

// Проверка дали потребителят е логнат и дали съществува 'user_id' в сесията
if (!isset($_SESSION['user_id'])) {
    die("Потребителят не е логнат. Моля, влезте в системата.");
}

$my_id = $_SESSION['user_id'];

// Проверка за връзка с базата данни
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT u.firstName, u.lastName, u.id
FROM events u
WHERE u.id NOT IN (
    SELECT f.favouriteID
    FROM favouritepeople f
    WHERE f.id = ?
) AND u.id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $my_id, $my_id);  // Връзка на параметър a (ID)
$stmt->execute();
$result = $stmt->get_result();

// Извеждаме резултата
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="registerForm_style.css">
</head>

<body>
    <form action="AddFunctionProcess.php" method="post">
    <main class="AddFunction">
    <h2>Добави към любими: </h2>
    <?php if (count($users) > 0): ?>
    <?php foreach ($users as $user): ?>
            <input type="checkbox" name="favourites[]" value="<?= $user['id']; ?>" id="user_<?= $user['id']; ?>">
            <label>
                <?= htmlspecialchars($user['firstName']) . " " . htmlspecialchars($user['lastName']); ?>
            </label>
    <?php endforeach; ?>
<?php else: ?>
    <p>Всички хора са в списъка.</p>
<?php endif; ?>
    <button type="submit" class="Add">Добави</button>
    <button type="button" class="Back" onclick="location.href='events.php'">Назад</button>
</main>
</form>

</body>

</html>