<?php

require_once 'DB_configuration.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Вземаме и филтрираме данните от POST заявката
    $eventType = mysqli_real_escape_string($conn, trim($_POST['eventType']));
    $idCelebration = mysqli_real_escape_string($conn, trim($_POST['idCelebration']));
    $deadline = mysqli_real_escape_string($conn, trim($_POST['deadline']));
    $moneyCollect = mysqli_real_escape_string($conn, trim($_POST['moneyCollect']));
    $bankId = mysqli_real_escape_string($conn, trim($_POST['bankId']));
    $Comments = mysqli_real_escape_string($conn, trim($_POST['Comments']));

    // Проверка за празни полета
    if (empty($eventType) || empty($idCelebration) || empty($deadline) || empty($moneyCollect) || empty($bankId) || empty($Comments)) {
        $error_message = "Моля, попълнете всички полета!";
        header("Location: eventForm.php?error=" . urlencode($error_message));
        exit;
    }

    //id remove ?
    // Подготовка на SQL заявката за вмъкване в таблицата events
    $sql_insert = "INSERT INTO eventspeople (eventType, idCelebration, deadline, moneyCollect, bankId, Comments) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);

    if ($stmt_insert) {
        // Свързваме параметрите към заявката:
        // 1) $type         -> string (s)
        // 2) $idCelebration-> int    (i)
        // 3) $deadline     -> string (s) (подаваме като 'YYYY-MM-DD')
        // 4) $moneyCollect -> int    (i)
        // 5) $bankId       -> string (s)
        // 6) $Comments     -> string (s)
        /*mysqli_stmt_bind_param($stmt_insert, "sisiss", 
            $eventType, 
            $idCelebration, 
            $deadline, 
            $moneyCollect, 
            $bankId, 
            $Comments
        );*/
        mysqli_stmt_bind_param(
        $stmt_insert,
        "sisiss",
        $eventType,
        $idCelebration,
        $deadline,
        $moneyCollect,
        $bankId,
        $Comments
    );

        if (mysqli_stmt_execute($stmt_insert)) {
            // Успешно добавяне на събитието
            //header("Location: eventForm.php?success=" . urlencode("Събитието беше успешно добавено!"));
            //exit;
            echo "<script>
            alert('Успешно създадохте събитие!');
            // Ако искаш след това да останеш на същата страница, просто няма нужда от допълнително действие.
            // Ако искаш да презаредиш или да пренасочиш, примерно:
            window.location.href = 'events.php';
          </script>";
        } else {
            // Грешка при изпълнение на заявката
            $error_message = "Грешка при добавянето на събитието. Моля, опитайте отново.";
            header("Location: eventForm.php?error=" . urlencode($error_message));
            exit;
        }
    } else {
        $error_message = "Грешка при подготовката на заявката към базата данни.";
        header("Location: eventForm.php?error=" . urlencode($error_message));
        exit;
    }

    // Освобождаваме ресурсите
    mysqli_stmt_close($stmt_insert);
    mysqli_close($conn);
}
?>