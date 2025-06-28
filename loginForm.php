<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="registerForm_style.css">
</head>

<body>
<main class="LoginForm">
    <h1>Login</h1>
    <form action="processLogin.php" method="POST"> 
        <label for="name">Username:</label>
        <input type="username" id="user" name="username" placeholder="username" required>
        
        <label for="password">Password:</label>
        <input type="password" id="pass" name="password" placeholder="password" required>
        
        <button type="submit">Login</button>
    </form>
    <p></p>
    <button class="register-button" onclick="location.href='registerForm.php'">Create new account</button>
</main>
</body>

</html>