<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join as a Client or Freelancer</title>
    <link rel="stylesheet" href="styles.css">

    <style>
    body {
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        background-color: #f5f5f5;
    }

    .container {
        text-align: center;
        background-color: #fff;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        margin-bottom: 30px;
    }

    .options {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .option {
        margin: 0 15px;
        cursor: pointer;
    }

    .option input {
        display: none;
    }

    .option label {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        border: 2px solid #ccc;
        border-radius: 10px;
        width: 200px;
        transition: border-color 0.3s;
    }

    .option input:checked+label {
        border-color: #000;
    }

    .icon {
        font-size: 40px;
        margin-bottom: 10px;
    }

    .text {
        font-size: 16px;
    }

    .create-account {
        background-color: #ccc;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        cursor: not-allowed;
        border-radius: 5px;
        margin-top: 20px;
    }

    .create-account.enabled {
        background-color: #000;
        color: #fff;
        cursor: pointer;
    }

    p {
        margin-top: 20px;
    }

    a {
        color: #000;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Join as a client or freelancer</h1>
        <div class="options">
            <div class="option">
                <input type="radio" id="client" name="role">
                <label for="client">
                    <div class="icon">👤</div>
                    <div class="text">I'm a client, hiring for a project</div>
                </label>
            </div>
            <div class="option">
                <input type="radio" id="freelancer" name="role">
                <label for="freelancer">
                    <div class="icon">👨‍💻</div>
                    <div class="text">I'm a freelancer, looking for work</div>
                </label>
            </div>
        </div>
        <button class="create-account" disabled>Create Account</button>
        <p>Already have an account? <a href="login.php">Log In</a></p>
    </div>

    <script>
    const options = document.querySelectorAll('.option input');
    const createAccountButton = document.querySelector('.create-account');

    options.forEach(option => {
        option.addEventListener('change', () => {
            createAccountButton.classList.add('enabled');
            createAccountButton.disabled = false;
        });
    });

    createAccountButton.addEventListener('click', () => {
        const selectedOption = document.querySelector('.option input:checked');
        if (selectedOption) {
            if (selectedOption.id === 'client') {
                window.location.href = 'signup.php';
            } else if (selectedOption.id === 'freelancer') {
                window.location.href = 'signupFreelancer.php';
            }
        }
    });
    </script>
</body>
</html>
