<?php
require_once 'DB_configuration.php';
//Стартиране/възстановява PHP сесията на потребителя
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

$ep_id = isset($_GET['ep_id']) ? (int)$_GET['ep_id'] : 0;// извличаме id-то на събитието
if (!$ep_id) {
    die("Невалидно ID на събитието.");
}

//Обновяване на инфорамцията на коментара
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newComments = isset($_POST['comments']) ? trim($_POST['comments']) : '';
    
    $upd = $conn->prepare("UPDATE eventspeople SET Comments = ? WHERE id = ?");
    $upd->bind_param("si", $newComments, $ep_id);
    if ($upd->execute()) {
        // Успешно обновяване, може да пренасочиш обратно към списъка:
        header("Location: events.php?msg=updated");
        exit;
    } else {
        $error = "Грешка при запис: " . $conn->error;
    }
}

// SQL заявка за извличане на всички потребители, първо любимите после останалите в азбучен ред 
$sql = "SELECT Comments
FROM eventspeople
WHERE id = ?";

$stmt = $conn->prepare($sql); //Подготовка (премахваме опастност от sql inj.!!!)
$stmt->bind_param("i", $ep_id);  // Връзка на параметър a (ID)
$stmt->execute();
$result = $stmt->get_result();

// Извеждаме резултата
$event = "";
while ($row = $result->fetch_assoc()) 
{
    $event = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Преглед на събития</title>
    <link rel="stylesheet" href="MainStyle.css">
</head>
<body>
    <button class="LogOutB" onclick="location.href='registerForm.php'">Log out</button>
    
<section class="Comments"> 
    <h2>Коментари:</h2>
        <ul> 
            <?= htmlspecialchars($event['Comments']); ?>
        </ul>
        
    <form method="post">
      <input type="hidden" name="ep_id" value="<?= htmlspecialchars($ep_id) ?>">
      <label for="comments">Коментари:</label><br>
      <textarea name="comments" rows="10" id="comments"><?= htmlspecialchars($event['Comments']) ?></textarea><br>
      <button type="submit">Запази</button>
      <button type="button" onclick="location.href='events.php'">Отказ</button>
    </form>
</section>
</body>
</html>