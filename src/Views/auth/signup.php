<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>SignUp</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="../../../public/css/styles.css" />
    <meta name="robots" content="noindex, follow">
    <style>
 body {
        width: 100%;
        min-height: 110vh;
        padding: 0 10px;
        display: flex;
        background: #fff;
        justify-content: center;
        align-items: center;
    }

    .page-content {
        width: 100%;
        height: 100px;
        max-width: 700px;
        padding: 10px;
        display: flex;
        background: #fff;
        justify-content: center;
        align-items: center;
        border-radius: 10px;
    }

    </style>
</head>

<body>
    <div class="page-content">
        <div class="form-v4-content">
            <!-- <div class="form-left">
                <h2>INFOMATION</h2>
                <p class="text-1">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                    incididunt ut labore et dolore magna aliqua. Et molestie ac feugiat sed. Diam volutpat commodo.</p>
                <p class="text-2"><span>Eu ultrices:</span> Vitae auctor eu augue ut. Malesuada nunc vel risus commodo
                    viverra. Praesent elementum facilisis leo vel.</p>
                <div class="form-left-last">
                    <input type="submit" name="account" class="account" onclick="window.location.href='login.php'"
                        value="Have An Account">
                </div>
            </div> -->
            <form class="form-detail" action="authUtils.php" method="post">
                <h2>REGISTER FORM</h2>
                <div class="form-group">
                    <div class="form-row form-row-1">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" class="input-text">
                    </div>
                    <div class="form-row form-row-1">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" class="input-text">
                    </div>
                </div>
                <div class="form-row">
                    <label for="email">Your Email</label>
                    <input type="text" name="email" class="input-text" required pattern="[^@]+@[^@]+.[a-zA-Z]{2,6}">
                </div>
                <div class="form-row">
                    <label for="your_Phone">Phone Number</label>
                    <input type="text" name="your_Phone" class="input-text" required pattern="079\d{7}"
                        placeholder="Phone Number (e.g., 0799473033)">
                </div>
                <div class="form-group">
                    <div class="form-row form-row-1 ">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="input-text" required>
                    </div>
                    <div class="form-row form-row-1">
                        <label for="comfirm-password">Comfirm Password</label>
                        <input type="password" name="comfirm_password" class="input-text" required>
                    </div>
                </div>
                <div class="form-checkbox">
                    <label class="container">
                        <p>I agree to the <a href="#" class="text">Terms and Conditions</a></p>
                        <input type="checkbox" name="checkbox">
                        <span class="checkmark"></span>
                    </label>
                </div>
                <div class="form-row-last">
                    <input type="submit" name="register" class="register" value="Register">
                </div>
            </form>
        </div>
    </div>
</body>

</html>