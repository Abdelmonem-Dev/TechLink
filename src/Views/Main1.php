<?php
session_start();
$isAuthenticated = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;



?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>TechLink</title>

    <style>
        /* Dark Mode Styles */
        .dark-mode {
            background-color: #121212;
            color: #ffffff;
        }

        .dark-mode .navbar {
            background-color: #333333;
        }

        .dark-mode .navbar .nav-link,
        .dark-mode .navbar .navbar-brand {
            color: #ffffff;
        }

        /* General Styles */
        body {
            background-color: #f8f9fa;
        }

        .container {
            width: 100%;
            min-height: 100vh;
            padding: 20px;
        }

        footer {
            background-color: #1c2331;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: auto;
        }

        .footer-section {
            margin-top: 2rem;
        }

        .footer-hr {
            width: 60px;
            background-color: #7c4dff;
            height: 2px;
            margin: 0 auto;
        }

        .copyright {
            background-color: rgba(0, 0, 0, 0.2);
            font-size: 16px;
            padding: 1rem;
            text-align: center;
        }

        .card-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1.5rem;
            padding: 1.5rem;
        }

        .card {
            width: 100%;
            max-width: 18rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card img {
            width: 100%;
            height: auto;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .hType {
            text-align: center;
            margin: 1rem 0;
            font-size: 1.5rem;
            color: #333;
        }

        .card-body {
            padding: 1rem;
        }

        .card-text {
            text-align: center;
            font-size: 1rem;
            color: #555;
        }

        .video-container {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        .video-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .container1 {
            margin: 30px 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 150px;
            height: 100px;
            background-color: #f1f3f4;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-align: center;
        }

        .icon-wrapper a {
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .icon-wrapper img {
            width: 24px;
            height: 24px;
            margin-bottom: 8px;
        }

        .icon-wrapper .icon-text {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .icon-wrapper:hover {
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
        }

        .icon-wrapper:hover .icon-text {
            color: #007bff;
        }

        /* Responsive styles */
        @media (max-width: 576px) {
            .hType {
                font-size: 1.25rem;
            }

            .card-container {
                padding: 1rem;
            }
        }

        @media (max-width: 768px) {
            .card {
                max-width: 100%;
            }

            .container1 {
                flex-direction: column;
            }
        }

        @media (max-width: 992px) {
            .icon-wrapper {
                width: 100px;
                height: 80px;
            }

            .icon-wrapper .icon-text {
                font-size: 12px;
            }
        }

        .suggestions-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            max-height: 200px;
            overflow-y: auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none; /* Hide by default */
        }

        .suggestions-dropdown div {
            padding: 10px;
            cursor: pointer;
        }

        .suggestions-dropdown div:hover {
            background-color: #f1f1f1;
        }

        /* New Section: Featured Content */
        .featured-content {
            background-color: #f8f9fa;
            padding: 3rem 0;
        }

        .featured-content .card {
            margin-bottom: 2rem;
        }

        /* New Section: FAQ */
        .faq-section {
            padding: 3rem 0;
        }

        .faq-section h2 {
            text-align: center;
            margin-bottom: 2rem;
        }

        .faq-item {
            margin-bottom: 1.5rem;
        }

        .faq-item h5 {
            font-weight: bold;
        }

        .faq-item p {
            margin-top: 0.5rem;
            color: #666;
        }

        /* Dark mode styles for new sections */
        .dark-mode .featured-content {
            background-color: #333;
        }

        .dark-mode .faq-section {
            background-color: #333;
            color: #fff;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include "Layout/header.php" ?>

    <main role="main" class="main-content">
        <div class="container">
            <!-- Search Section -->
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="text-center py-5 my-5">
                        <h2 class="page-title mb-4">How can we help?</h2>
                        <p class="lead text-muted mb-4">Find posts and answers from our community.</p>
                        <form action="community.php" id="searchForm" class="searchform" method="GET">
                            <div class="position-relative">
                                <input id="txt1" class="form-control form-control-lg bg-white rounded-pill pl-5" 
                                    type="search" placeholder="Search" aria-label="Search" name="search"
                                    onkeyup="showHint(this.value)">
                                <div id="suggestions" class="suggestions-dropdown"></div>
                            </div>
                            <p class="help-text mt-2 text-muted">Enter your search term above.</p>
                        </form>
                        <div id="searchResults"></div> <!-- Container for displaying search results -->
                    </div>
                </div>
            </div>

            <!-- Featured Content Section -->
            <div class="featured-content">
                <h3 class="hType">Featured Topics</h3>
                <div class="card-container">
                    <div class="card">
                        <img src="pexels-markusspiske-6502328.jpg" alt="Topic Image 1">
                        <div class="card-body">
                            <h5 class="card-title">Topic 1</h5>
                            <p class="card-text">Brief description of the topic.</p>
                        </div>
                    </div>
                    <div class="card">
                        <img src="pexels-markusspiske-6502328.jpg" alt="Topic Image 2">
                        <div class="card-body">
                            <h5 class="card-title">Topic 2</h5>
                            <p class="card-text">Brief description of the topic.</p>
                        </div>
                    </div>
                    <!-- Add more cards as needed -->
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="faq-section">
                <div class="container">
                    <h2 class="text-center mb-4">Frequently Asked Questions</h2>
                    <div class="row">
                        <div class="col-lg-8 offset-lg-2">
                            <div class="faq-item">
                                <h5>What is TechLink?</h5>
                                <p>TechLink is a platform for discussing and sharing knowledge about technology and related fields.</p>
                            </div>
                            <div class="faq-item">
                                <h5>How can I contribute?</h5>
                                <p>You can contribute by sharing your knowledge, participating in discussions, and providing feedback.</p>
                            </div>
                            <!-- Add more FAQ items as needed -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Video Section -->
            <div class="video-container">
                <video autoplay muted loop>
                    <source src="video.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>

            <!-- Icon Links Section -->
            <div class="container1">
                <div class="icon-wrapper">
                    <a href="link1.html">
                        <img src="icon1.png" alt="Icon 1">
                        <span class="icon-text">Feature 1</span>
                    </a>
                </div>
                <div class="icon-wrapper">
                    <a href="link2.html">
                        <img src="icon2.png" alt="Icon 2">
                        <span class="icon-text">Feature 2</span>
                    </a>
                </div>
                <!-- Add more icons as needed -->
            </div>
        </div>
    </main>


    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz4fnFO9g26fMjw2J6z5J5U6CvJ6b0D8mF0iLm6A4Jr9sJ3sZO2EzZjoXa"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-pzjw8f+ua7Kw1TI7R69tmgUfsnLe8DVMbY/xE/fV6YyMwQ+sl6f8Z/2H/eSuXbboG"
        crossorigin="anonymous"></script>

    <script>
        function showHint(str) {
            const suggestions = document.getElementById('suggestions');
            if (str.length === 0) {
                suggestions.style.display = 'none';
                return;
            }

            // Perform AJAX request to fetch search hints
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `search-hints.php?q=${str}`, true);
            xhr.onload = function () {
                if (this.status === 200) {
                    const hints = JSON.parse(this.responseText);
                    suggestions.innerHTML = '';
                    hints.forEach(hint => {
                        const div = document.createElement('div');
                        div.textContent = hint;
                        div.onclick = function () {
                            document.getElementById('txt1').value = hint;
                            suggestions.style.display = 'none';
                        };
                        suggestions.appendChild(div);
                    });
                    suggestions.style.display = 'block';
                }
            };
            xhr.send();
        }
    </script>
        <?php include 'Layout/footer.php';?>

</body>

</html>
