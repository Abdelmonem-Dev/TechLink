<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with Sidebar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background-color: #f0f2f5;
        }

        .chat-container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            margin: auto;
            height: 80vh;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            width: 300px;
            background-color: #fff;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .search-bar {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .search-bar input {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 10px;
            font-size: 16px;
            outline: none;
        }

        .contact-list {
            padding: 0;
            margin: 0;
            list-style-type: none;
        }

        .contact {
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            position: relative;
        }

        .contact:hover {
            background-color: #f1f1f1;
        }

        .contact.active {
            background-color: #007bff;
            color: white;
        }

        .contact-avatar {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            background-color: #007bff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            margin-right: 10px;
            font-weight: bold;
            font-size: 20px;
        }

        .contact-info {
            flex: 1;
        }

        .contact-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .contact-status {
            color: #666;
            font-size: 14px;
        }

        .contact-unread {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background-color: #ff5722;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .messages {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .message {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .message.sent {
            justify-content: flex-end;
        }

        .message.received {
            justify-content: flex-start;
        }

        .message-avatar {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-right: 10px;
            background-color: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: bold;
            font-size: 18px;
        }

        .message-content {
            max-width: 75%;
            padding: 10px;
            border-radius: 10px;
            background-color: #e0e0e0;
            position: relative;
            font-size: 16px;
            line-height: 1.4;
        }

        .message.sent .message-content {
            background-color: #007bff;
            color: white;
        }

        .message.received .message-content {
            background-color: #f1f1f1;
        }

        .message-time {
            font-size: 0.8em;
            color: #999;
            margin-top: 5px;
            text-align: right;
        }

        .chat-input {
            display: flex;
            align-items: center;
            padding: 15px;
            border-top: 1px solid #ddd;
            background-color: #fff;
        }

        .chat-input textarea {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 10px;
            font-size: 16px;
            resize: none;
            outline: none;
            transition: border-color 0.3s;
        }

        .chat-input textarea:focus {
            border-color: #007bff;
        }

        .chat-input button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 16px;
            cursor: pointer;
            margin-left: 10px;
            transition: background-color 0.3s;
        }

        .chat-input button:hover {
            background-color: #0056b3;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .chat-container {
                flex-direction: column;
                height: 90vh;
            }

            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #ddd;
                display: block;
            }

            .chat-area {
                height: calc(100% - 60px); /* Adjust based on chat input height */
            }
        }
    </style>
</head>
<body>
    <?php include "Layout/header.php"; ?>

    <div class="chat-container">
        <div class="sidebar">
            <div class="search-bar">
                <input type="text" placeholder="Search contacts...">
            </div>
            <ul class="contact-list">
                <!-- Example Contacts -->
                <li class="contact active">
                    <div class="contact-avatar">A</div>
                    <div class="contact-info">
                        <div class="contact-name">Alice Johnson</div>
                        <div class="contact-status">Last message here...</div>
                    </div>
                    <div class="contact-unread">3</div>
                </li>
                <li class="contact">
                    <div class="contact-avatar">B</div>
                    <div class="contact-info">
                        <div class="contact-name">Bob Smith</div>
                        <div class="contact-status">Another message here...</div>
                    </div>
                    <div class="contact-unread">1</div>
                </li>
                <!-- More contacts here -->
            </ul>
        </div>
        <div class="chat-area">
            <div class="messages">
                <!-- Example Messages -->
                <div class="message received">
                    <div class="message-avatar">A</div>
                    <div class="message-content">
                        Hello, how are you doing today?
                        <div class="message-time">12:45 PM</div>
                    </div>
                </div>
                <div class="message sent">
                    <div class="message-content">
                        I'm great, thanks! How about you?
                        <div class="message-time">12:47 PM</div>
                    </div>
                </div>
                <!-- More messages here -->
            </div>
            <div class="chat-input">
                <textarea placeholder="Type a message..."></textarea>
                <button>Send</button>
            </div>
        </div>
    </div>
</body>
</html>
