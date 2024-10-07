<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .page-content {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 720px;
            width: 100%;
        }

        /* Form Styling */
        .form-detail h2 {
            text-align: center;
            color: #333333;
            font-size: 2rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .form-row {
            margin-bottom: 15px;
            width: 100%;
        }

        .form-row label {
            font-size: 1rem;
            color: #555555;
            margin-bottom: 8px;
            display: block;
        }

        .input-text,
        .select-text {
            width: 100%;
            padding: 12px;
            border: 1px solid #cccccc;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .input-text:focus,
        .select-text:focus {
            border-color: #2cb1bc;
            box-shadow: 0 0 5px rgba(44, 177, 188, 0.5);
            outline: none;
        }

        /* Two-column layout */
        .form-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .form-group .form-row {
            flex: 1;
            min-width: 280px;
        }

        /* Full width for submit button */
        .form-row-last {
            margin-top: 15px;
            width: 100%;
        }

        .register {
            background: #2cb1bc;
            color: #ffffff;
            font-size: 1.1rem;
            padding: 14px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-align: center;
            width: 100%;
            transition: background 0.3s ease;
        }

        .register:hover {
            background: #1a8e98;
        }

        .sign_up {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9rem;
            color: #555;
        }

        .sign_up a {
            color: #2cb1bc;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .sign_up a:hover {
            color: #1a8e98;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .form-group {
                flex-direction: column;
            }

            .form-group .form-row {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="page-content">
        <div class="form-v4-content">
            <form class="form-detail" action="authFreelancer.php" method="post">
                <h2>Sign Up to Get Started</h2>
                <div class="form-group">
                    <div class="form-row">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" class="input-text" id="first_name" required>
                    </div>
                    <div class="form-row">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" class="input-text" id="last_name" required>
                    </div>
                </div>
                <div class="form-row">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="input-text" id="email" required>
                </div>
                <div class="form-row">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" class="input-text" id="phone" required placeholder="e.g., 0799473033">
                </div>
                <div class="form-row">
                    <label for="country">Country</label>
                    <select name="country" class="select-text" id="country" required>
                        <option value="" disabled selected>Select your country</option>
                        <?php
                        $json_data = file_get_contents('../../../country.json');
                        $countries = json_decode($json_data, true)['countries'];
                        foreach ($countries as $country) {
                            echo "<option value='{$country['name']}'>{$country['name']} - ({$country['code']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <div class="form-row">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="input-text" id="password" required>
                    </div>
                    <div class="form-row">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" name="confirm_password" class="input-text" id="confirm_password" required>
                    </div>
                </div>
                <div class="form-row-last">
                    <button type="submit" class="register">Register</button>
                </div>
                <p class="sign_up">Already have an account? <a href="#" onclick="window.location.href='login.php'">Log In</a></p>
            </form>
        </div>
    </div>
</body>

</html>
