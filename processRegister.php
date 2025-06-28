<?php
require_once 'DB_configuration.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    //get the values from post request
    $firstName = mysqli_real_escape_string($conn, trim($_POST['fname']));
    $lastName = mysqli_real_escape_string($conn, trim($_POST['lname']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    $confirm_password = mysqli_real_escape_string($conn, trim($_POST['confirm_password']));
    // check for no empty fields
    if (empty($firstName) || empty($lastName) || empty($username) || empty($password)) {
        $error_message = "Please fill all fields";
        header("Location: registerForm.php?error4=" . urlencode($error_message));
        exit;
    }

    // check for username exists
    $sql_check = "SELECT * FROM events WHERE username = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $username);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        // if exists we print following code
        $error_message = "Username exists, choose another one!";
        header("Location: loginForm.php?error3=" . urlencode($error_message));
        exit;
    }

    
    //$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // sql for acc insert
    $sql_insert = "INSERT INTO events (firstName, lastName, username, password) VALUES (?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);

    if ($stmt_insert) {
        // connect params and execute request
        mysqli_stmt_bind_param($stmt_insert, "ssss", $firstName, $lastName, $username, $password);
        $execute_result = mysqli_stmt_execute($stmt_insert);

        if ($execute_result) {
            // registration is successful
            header("Location: loginForm.php?success=" . urlencode("Registration is successful"));
            exit;
        } else {
            // Error
            $error_message = "Error, try again.";
            header("Location: registerForm.php?error1=" . urlencode($error_message));
            exit;
        }
    } else {
        $error_message = "Error";
        header("Location: registerForm.php?error2=" . urlencode($error_message));
        exit;
    }

    // free resourses
    //mysqli_stmt_close($stmt_insert);
    //mysqli_stmt_close($stmt_check);
}

// Затваряне на връзката с базата данни
mysqli_close($conn);
?>