

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Form in HTML and CSS | CodingNepal</title>
  <link rel="stylesheet" href="../../../public/css/styles.css" />
  <style>
    
body {
    width: 100%;
    min-height: 100vh;
    padding: 0 10px;
    display: flex;
    background: #fff;
    justify-content: center;
    align-items: center;
}
  </style>
</head>
<body>
  <div class="login_form">
  <?php if (isset($_GET['error'])): ?>
            <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
    <form action="authLogin.php" method="post">
      <h3>Log in with</h3>

      <!-- <div class="login_option">
        <div class="option">
          <a href="#">
            <img src="../../../public/images/logos/google.png" alt="Google" />
            <span>Google</span>
          </a>
        </div>

        <div class="option">
          <a href="#">
            <img src="../../../public/images/logos/apple.png" alt="Apple" />
            <span>Apple</span>
          </a>
        </div>
      </div> -->

      <!-- Login option separator -->
      <p class="separator">
        <span>or</span>
      </p>

      <!-- Email input box -->
      <div class="input_box">
        <label for="email">Email</label>
        <input type="email" name="email" placeholder="Enter email address" required />
      </div>

      <!-- Paswwrod input box -->
      <div class="input_box">
        <div class="password_title">
          <label for="password">Password</label>
          <a href="#">Forgot Password?</a>
        </div>

        <input type="password" name="password" placeholder="Enter your password" required />
      </div>

      <!-- Login button -->
      <button type="submit">Log In</button>

      <p class="sign_up">Don't have an account? <a href="#" onclick="window.location.href='CreateAccount.php'">Sign up</a></p>
    </form>
  </div>
</body>
</html>