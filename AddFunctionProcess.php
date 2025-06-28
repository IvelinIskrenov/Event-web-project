
<?php
session_start(); // Започваме сесията
require_once 'DB_configuration.php';

//$conn = new mysqli($host, $user, $pass, $db);

// Проверка за логнат потребител
if (!isset($_SESSION['user_id'])) {
    die("Грешка: Трябва да сте влезли в системата!");
}

$logged_user_id = $_SESSION['user_id']; // Взимаме ID на логнатия потребител

// Проверяваме дали имаме избрани любими хора
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['favourites'])) {
    $favourites = $_POST['favourites']; // Списък с ID-та на любимите хора

    // Подготвяме заявката за вмъкване
    $stmt = $conn->prepare("INSERT INTO favouritepeople (id, favouriteID) VALUES (?, ?)");

    if ($stmt) {
        foreach ($favourites as $fav_id) {
            $stmt->bind_param("ii", $logged_user_id, $fav_id);
            $stmt->execute();
        }
        //echo "Любимите хора бяха добавени успешно!";
        echo "<script>
            alert('Любимите хора бяха добавени успешно!');
            // Ако искаш след това да останеш на същата страница, просто няма нужда от допълнително действие.
            // Ако искаш да презаредиш или да пренасочиш, примерно:
            window.location.href = 'AddFunction.php';
          </script>";
    } else {
        echo "Грешка при подготовка на заявката: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Няма избрани любими хора!";
}

$conn->close();

?>

