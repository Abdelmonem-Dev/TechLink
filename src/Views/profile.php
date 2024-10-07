<?php

session_start();

require_once '../Controllers/UserController.php';
require_once '../Controllers/FreelancerController.php';

// Check if the session variable is set before accessing it
$isAuthenticated = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;

if (!isset($_SESSION['user_id'])) {
    die("Error: User ID not Found");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="John Doe's professional profile showcasing skills, portfolio, and testimonials.">
    <title>John Doe - Profile Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css"> <!-- External CSS for better organization -->
</head>
<style>
:root {
    --primary-color: #FF5722;
    --secondary-color: #555;
    --text-color: #333;
    --background-color: #f4f4f4;
    --hover-background-color: #e0e0e0;
    --border-color: #ccc;
    --dark-background-color: #1f1f1f;
    --dark-text-color: #e0e0e0;
    --transition-speed: 0.3s;
    --border-radius: 6px;
    --padding: 20px;
}

[data-theme="dark"] {
    --background-color: var(--dark-background-color);
    --text-color: var(--dark-text-color);
    --border-color: #444;
    --hover-background-color: #333;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: var(--background-color);
    color: var(--text-color);
    transition: background-color var(--transition-speed), color var(--transition-speed);
    line-height: 1.6;
}


.logo {
    font-size: 28px;
    font-weight: bold;
}

.container {
    margin: 0 auto;
    max-width: 1200px;
    padding: var(--padding);
    display: flex;
    flex-direction: column;
}

.profile-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 40px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--border-color);
}

.profile-picture {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
    border: 4px solid var(--border-color);
    transition: border-color var(--transition-speed);
}

.profile-picture:hover {
    border-color: var(--primary-color);
}

.profile-text {
    flex: 1;
}

.profile-text .username {
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 10px;
}

.profile-text .country {
    font-size: 20px;
    color: var(--secondary-color);
    margin-bottom: 15px;
}

.rating {
    color: var(--text-color);
    font-size: 22px;
    margin-top: 5px;
}

.info-section {
    margin-bottom: 40px;
}

.info-section h2 {
    font-size: 26px;
    margin-bottom: 15px;
    border-bottom: 4px solid var(--primary-color);
    padding-bottom: 8px;
    display: inline-block;
}


.skills-list {
    list-style: none;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.skills-list li {
    background-color: var(--hover-background-color);
    padding: 10px 20px;
    border-radius: var(--border-radius);
    font-size: 16px;
    transition: background-color var(--transition-speed);
}

.skills-list li:hover {
    background-color: var(--primary-color);
    color: #fff;
}

.portfolio-list,
.testimonials-list,
.education-list,
.experience-list {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.portfolio-item,
.testimonial-item,
.education-item,
.experience-item {
    background: var(--background-color);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 20px;
    transition: background-color var(--transition-speed);
    display: flex;
    align-items: center;
    gap: 15px;
}

.portfolio-item:hover,
.testimonial-item:hover,
.education-item:hover,
.experience-item:hover {
    background: var(--hover-background-color);
}

.portfolio-item img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: var(--border-radius);
    border: 2px solid var(--border-color);
    transition: border-color var(--transition-speed);
}

.portfolio-item img:hover {
    border-color: var(--primary-color);
}

.portfolio-info {
    flex: 1;
}

.portfolio-title {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 10px;
}

.portfolio-description {
    font-size: 16px;
    color: var(--secondary-color);
}

.testimonial-item p {
    font-style: italic;
    color: var(--secondary-color);
    margin: 0;
}

.client-name {
    font-weight: bold;
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.client-name img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 2px solid var(--border-color);
    transition: border-color var(--transition-speed);
}

.client-name img:hover {
    border-color: var(--primary-color);
}

.contact-info {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.contact-info div {
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.contact-info i {
    color: var(--primary-color);
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .profile-text {
        text-align: center;
    }

    .container {
        padding: 10px;
    }

}

.profile-picture-container {
    position: relative;
    display: inline-block;
}

.profile-picture {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
    border: 4px solid var(--border-color);
    transition: border-color var(--transition-speed);
}

.profile-picture:hover {
    border-color: var(--primary-color);
}

.update-button {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background-color: var(--primary-color);
    border: none;
    border-radius: 50%;
    color: white;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    font-size: 20px;
    transition: background-color var(--transition-speed);
}

.update-button:hover {
    background-color: #FF7849;
}

.update-button i {
    margin: 0;
}
</style>

<body>

    <?php
    include "Layout/header.php";

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $UserData = UserController::FetchByUserID1($_GET['id']);
        $UserDetails = FreelancerController::fetchByUserID($_GET['id']);
    } else {
        $UserData = UserController::FetchByUserID1($_SESSION['user_id']);
        $UserDetails = FreelancerController::fetchByUserID($_SESSION['user_id']);
    }

    $FullName = $UserData->getFirstName() . " " . $UserData->getLastName();
    ?>

    <div class="container">
        <main>
            <section id="profile" class="profile-section">
                <div class="profile-header">
                    <!-- Profile Picture -->
                    <div class="profile-picture-container">
                        <label for="profileImageUpload">
                            <img id="profilePicture" src="<?php echo htmlspecialchars($UserData->getImageUrl()); ?>"
                                alt="<?php echo htmlspecialchars($UserData->getImageDescription()); ?>"
                                class="profile-picture">
                        </label>

                        <!-- Hidden file input -->
                        <form id="profileImageForm" action="update_profile.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="oldImageUrl" value="<?php echo htmlspecialchars($UserData->getImageUrl()); ?>">
    <?php if ($_SESSION['user_id'] === $UserData->getUserID()) { ?>
    <input type="file" id="profileImageUpload" name="profileImageUpload" style="display: none;" accept="image/*">
    <!-- Update Button -->
    <button type="button" class="update-button" onclick="document.getElementById('profileImageUpload').click();">
        <i class="fas fa-camera"></i>
    </button>
    <?php } ?>
</form>

                    </div>
                    <div class="profile-text">
                        <div class="username">
                            <?php echo $UserData->getAccountType() == 'freelancer' ? $UserDetails->getUserName() : $FullName; ?>
                            <i style="color:blue; font-size:small">
                                <?php echo $UserData->getAccountType() == 'freelancer' ? "\t" . $UserDetails->getExperience() : ""; ?>
                            </i>
                        </div>
                        <div class="country"><?php echo htmlspecialchars($UserData->getCountry()); ?></div>
                        <?php if ($UserData->getAccountType() == 'freelancer') { ?>
                        <div class="rating" aria-label="5 out of 5 stars">
                            ⭐<small><?php echo htmlspecialchars($UserDetails->getRating()); ?></small>
                        </div>
                        <?php } ?>
                    </div>
                </div>

                <section class="info-section">
                    <h2>About Me</h2>
                    <button onclick="updateAboutMe()">Update</button>
                    <p><?php echo htmlspecialchars($UserData->getBio()); ?></p>
                </section>

                <section class="info-section">
                    <h2>Skills</h2>
                    <button onclick="updateSkills()">Update</button>
                    <ul class="skills-list">
                        <!-- You may want to dynamically generate skills list -->
                        <li>JavaScript</li>
                        <li>PHP</li>
                        <li>HTML</li>
                        <li>CSS</li>
                        <li>MySQL</li>
                    </ul>
                </section>

                <section id="portfolio" class="info-section">
                    <h2>Portfolio
                        <button class="btn btn-sm btn-primary" onclick="updatePortfolio()">Update</button>
                    </h2>
                    <ul class="portfolio-list">
                        <!-- Use dynamic content for portfolio items -->
                        <li class="portfolio-item">
                            <img src="dhcp-chart.png" alt="Project 1">
                            <div class="portfolio-info">
                                <div class="portfolio-title">Project Title 1</div>
                                <div class="portfolio-description">Brief description of Project 1.</div>
                            </div>
                        </li>
                        <li class="portfolio-item">
                            <img src="dhcp-chart.png" alt="Project 2">
                            <div class="portfolio-info">
                                <div class="portfolio-title">Project Title 2</div>
                                <div class="portfolio-description">Brief description of Project 2.</div>
                            </div>
                        </li>
                    </ul>
                </section>

                <section id="contact" class="info-section">
                    <h2>Contact Info
                        <button class="btn btn-sm btn-primary" onclick="updateContact()">Update</button>
                    </h2>
                    <div class="contact-info">
                        <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($UserData->getEmail()); ?>
                        </div>
                        <div><i class="fas fa-phone"></i> <?php echo htmlspecialchars($UserData->getPhoneNumber()); ?>
                        </div>
                    </div>
                </section>

                <section class="info-section">
                    <h2>Education
                        <button class="btn btn-sm btn-primary" onclick="updateEducation()">Update</button>
                    </h2>
                    <ul class="education-list">
                        <!-- Use dynamic content for education items -->
                        <li class="education-item">
                            <h3>Degree Title - University</h3>
                            <p>Graduation Year</p>
                        </li>
                    </ul>
                </section>

                <section class="info-section">
                    <h2>Experience
                        <button class="btn btn-sm btn-primary" onclick="updateExperience()">Update</button>
                    </h2>
                    <ul class="experience-list">
                        <!-- Use dynamic content for experience items -->
                        <li class="experience-item">
                            <h3>Job Title - Company</h3>
                            <p>Brief description of job responsibilities and achievements.</p>
                        </li>
                    </ul>
                </section>
            </section>
        </main>
    </div>
    <script>
document.getElementById('profileImageUpload').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePicture').src = e.target.result;
        };
        reader.readAsDataURL(file);

        // Automatically submit the form once a file is selected
        document.getElementById('profileImageForm').submit();
    }
});

</script>

</body>

</html>