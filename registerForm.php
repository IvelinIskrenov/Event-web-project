<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="registerForm_style.css">
</head>

<body>
<main class="RegisterForm">
    <h1>Sign up</h1>
    <form action="processRegister.php" method="POST"> 
        <label for="fname">First name</label>
        <input type="text" id="fname" name="fname" required>
        
        <label for="lname">Last Name</label>
        <input type="text" id="lname" name="lname" required>
        
        <label for="username">Username</label>
        <input type="username" name="username" placeholder="Username" required>

        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Password" required>
        <label for="confirm_password">Confirm password</label>
        <input type="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
        
        <button type="submit">Sign up</button>
    </form>
    <p>Already have an account? <a href="loginForm.php">Login here</a>.</p>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
</main>
</body>

</html>