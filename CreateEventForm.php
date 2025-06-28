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


$sql = "SELECT u.id,
       u.FirstName,
       u.LastName
FROM events u
LEFT JOIN favouritepeople f
       ON f.favouriteID = u.id 
      AND f.id = ?
ORDER BY 
    CASE 
        WHEN f.favouriteID IS NOT NULL THEN 0
        ELSE 1
    END,
    u.FirstName ASC,
    u.LastName ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $my_id);  // Връзка на параметър a (ID)
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
    <title>Добавяне на събитие</title>
    <link rel="stylesheet" href="registerForm_style.css">
</head>
<body>
    <main class="EventForm">
    <h2>Добави събитие</h2>
    <form action="CreateEventFormProcess.php" method="POST">
        <label for="eventType">Тип на събитието:</label>
        <select name="eventType" id="eventType" required>
            <option value="birthday">Рожден ден</option>
            <option value="nameday">Имен ден</option>
            <option value="party">Парти</option>
        </select>
        
        <label for="idCelebration">Празнуващ</label>
        <select name="idCelebration" id="idCelebration" required>
            <?php foreach ($users as $user): ?>
                <option value="<?= htmlspecialchars($user['id']); ?>">
                    <?= htmlspecialchars($user['FirstName']) . " " . htmlspecialchars($user['LastName']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label for="deadline">Краен срок:</label>
        <input type="date" name="deadline" id="deadline" required>
        
        <label for="moneyCollect">Сума за събиране:</label>
        <input type="number" name="moneyCollect" id="moneyCollect" required>
        
        <label for="bankId">ID на банката:</label>
        <textarea name="bankId" id="bankId" rows="4" cols="50" required></textarea>

        <label for="Comments">Коментари:</label>
        <textarea name="Comments" id="Comments" rows="4" cols="50" required></textarea>
        
        <button type="submit">Добави събитие</button>
    </form>
    </main>
</body>
</html>
