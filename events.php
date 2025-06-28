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

// SQL заявка за извличане на всички потребители, първо любимите после останалите в азбучен ред 
$sql = "SELECT u.id,
       u.FirstName,
       u.LastName
FROM events u
INNER JOIN favouritepeople f
    ON f.favouriteID = u.id
   AND f.id = ?
ORDER BY
    u.FirstName ASC,
    u.LastName ASC";

$stmt = $conn->prepare($sql); //Подготовка (премахваме опастност от sql inj.!!!)
$stmt->bind_param("i", $my_id);  // Връзка на параметър a (ID)
$stmt->execute();
$result = $stmt->get_result();

// Извеждаме резултата
$users = [];
while ($row = $result->fetch_assoc()) 
{
    $users[] = $row;
}

$stmt->close();

//start 2
$sql2 = "SELECT 
        ep.*, 
        e.FirstName,
        e.LastName,
        e.Username
    FROM eventspeople AS ep
    INNER JOIN events AS e
        ON ep.idCelebration = e.id
    WHERE ep.idCelebration != ?
    ORDER BY 
        -- първо любимите хора
        CASE 
            WHEN ep.idCelebration IN (
                SELECT favouriteID 
                FROM favouritepeople 
                WHERE id = ?
            ) THEN 0
            ELSE 1
        END,
        ep.deadline ASC
";

$stmt1 = $conn->prepare($sql2);
$stmt1->bind_param("ii", $my_id, $my_id);  // Връзка на параметър a (ID) (подаваме параметрите на заявката)
$stmt1->execute();
$result = $stmt1->get_result();

// Извеждаме резултата
$eventsList = [];
while ($row = $result->fetch_assoc()) //вземаме всяка следваща редица от резултата
{
    $eventsList[] = $row;
}
$stmt1->close();
//end 2

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
    
<section class="favorites"> 
    <h2>Любими хора:</h2>
    <?php if (count($users) > 0): ?>
        <ul>
            <?php foreach ($users as $user): ?>
                <li><?= htmlspecialchars($user['FirstName']) . " " . htmlspecialchars($user['LastName']) ?></li> <!-- Извеждаме ID-то на потребителя -->
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Нямате любими хора.</p>
    <?php endif; ?>

    <button class="AddB" onclick="location.href='AddFunction.php'">Добави</button>
    <button class="RemoveB" onclick="location.href='RemoveFunction.php'">Премахни</button>
</section>

<section class="eventsPeople">
    <h2>Събития:</h2>
    <?php if (count($eventsList) > 0): ?>
        <ul>
            <?php foreach ($eventsList as $event): ?>  
                <li class="event-frame">
                    <strong><?= htmlspecialchars($event['eventType'] ?? '') . ' на ' . htmlspecialchars($event['FirstName']) . " " . htmlspecialchars($event['LastName']) ?></strong><br>
                    Краен срок за събиране на пари: <?= htmlspecialchars($event['deadline'] ?? '') ?><br>
                    Събрани пари: <?= htmlspecialchars($event['moneyCollect'] ?? '') ?><br>
                    <button class="Comments" onclick="location.href='CommentForm.php?ep_id=<?= urlencode($event['id']) ?>'">Коментари</button>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Няма събития.</p>
    <?php endif; ?>
        <button class="CreateEvent" onclick="location.href='CreateEventForm.php'">Създай събитие</button>
</section>
</body>
</html>
