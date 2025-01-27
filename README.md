# webshell.php

Myownshell is a web-based terminal emulator with a sleek. It allows users to execute commands directly from their browser, complete with a Discord webhook-based authentication system for secure access.

## Ethical Use Disclaimer ⚠️

Important Notes:
Do Not Misuse: This tool should never be used for malicious purposes, such as unauthorized access to systems, data theft, or any activity that violates laws or ethical guidelines.

Responsible Use: Always ensure you have proper authorization before running commands on any system. Misuse of this tool can lead to legal consequences.

For Good Purposes: Use this tool to enhance your skills, automate tasks, or manage systems responsibly.

By using Myownshell, you agree to use it ethically and responsibly. The developers of this tool are not responsible for any misuse or damage caused by its use.

## Features ✨
Web-Based Terminal: Execute commands directly from your browser.

Hacker Theme: Stylish green-on-black interface with a glowing terminal effect.

Discord Authentication: Secure access using a token sent to your Discord channel via webhook.

Command Execution: Supports basic commands like cd, ls, pwd, and more.

Matrix Rain Effect: Optional background animation for that authentic hacker vibe.

Session Management: Tracks the current directory and authentication status.

## How It Works 🛠️
Authentication:

When you load the page, a random token is generated and sent to your Discord channel via a webhook.

Enter the token in the input field to authenticate and gain access to the terminal.

Command Execution:

Once authenticated, you can enter commands in the terminal.

The terminal supports basic commands and tracks the current directory.

https://github.com/user-attachments/assets/873a80d5-2285-4839-b905-8b72174983f0



Step 1: Token Generation
"Whenever you load the webshell.php, a new authentication token is generated and sent to your Discord channel via a webhook. This token is required to gain access to the terminal."

Step 2: Token Expiry
"Here's an important note: Only the most recently sent token is valid for authentication. If a new token is generated (for example, by refreshing the page or loading the terminal again), the old token becomes invalid. This ensures that the system remains secure and prevents the reuse of expired tokens."

Step 3: Demonstration
"Let me show you how this works. I'll generate a new token and send it to Discord. Then, I'll use that token to authenticate. If I try to use an old token, the system will reject it."

Step 4: Example of Invalid Token
"Now, I'll try to use an old token that was sent earlier. As you can see, the system rejects it because only the most recent token is valid."

## Installation 🚀
Clone the Repository:
```bash
https://github.com/tobiasGuta/Myownshell.git
```
Set Up Discord Webhook:

## Create a webhook in your Discord server and copy the webhook URL.

Replace YOUR_DISCORD_WEBHOOK_URL in the PHP code with your actual webhook URL.

## Run the Application:

Deploy the application to a PHP-enabled server.

Open the application in your browser and start using the terminal!

## Technologies Used 💻
Frontend: HTML, CSS, JavaScript

Backend: PHP

Authentication: Discord Webhooks
