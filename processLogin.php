<?php

require_once 'DB_configuration.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{

    $inputUsername = mysqli_real_escape_string($conn, trim($_POST['username']));
    $inputPassword = mysqli_real_escape_string($conn, trim($_POST['password']));

    if (empty($inputUsername) || empty($inputPassword)) 
    {
        $error_message = "Fill the username and the passoword";
        header("Location: login.php?2error=" . urlencode($error_message));
        exit;
    }

    $sql = "SELECT id, username, password FROM events WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) 
    {
        mysqli_stmt_bind_param($stmt, "s", $inputUsername);  
        mysqli_stmt_execute($stmt); 
        
        $result = mysqli_stmt_get_result($stmt);
        
        echo "3";
        if (mysqli_num_rows($result) == 1) 
        {
            echo "1";
            $user = mysqli_fetch_assoc($result);
            echo $user['password']; // Print the hashed password from the database
            echo "-------------";
            echo $inputPassword;
            if ($inputPassword == $user['password']) //password_verify($inputPassword, $user['password'])
            {
                echo "2";
                session_start();
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                header("Location: events.php");
                exit;
            } else 
            {
                $error_message = "Wrong username or password!";
            }
        } else 
        {
            $error_message = "Wrong username or password!";
        }
        mysqli_stmt_free_result($stmt);
    } else 
    {
        $error_message = "Error!";
    }

    // free the resourses
    mysqli_stmt_close($stmt);
    
    if (isset($error_message)) {
        header("Location: loginForm.php?1error=" . urlencode($error_message));
        exit;
    }
}

mysqli_close($conn);
?>