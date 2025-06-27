<?php
// Funzione per rilevare dispositivi mobili (semplice, ma efficace)
function isMobile() {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
    $mobileAgents = ['iphone', 'ipod', 'ipad', 'android', 'blackberry', 'windows phone', 'mobile', 'opera mini', 'webos'];
    foreach ($mobileAgents as $agent) {
        if (strpos($userAgent, $agent) !== false) {
            return true;
        }
    }
    return false;
}

// Se è dispositivo mobile, mostra solo la scritta e termina
if (isMobile()) {
    ?>
    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Accesso non consentito</title>
        <style>
            body {
                font-family: 'Montserrat', sans-serif;
                background-color: #0d0d0d;
                color: #ff5555;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                text-align: center;
                padding: 1rem;
                font-size: 1.6rem;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        Questo sito non è accessibile da dispositivi mobili.
    </body>
    </html>
    <?php
    exit;
}

// Connessione al database
$mysqli = new mysqli("localhost", "root", "17092007", "gheshinmerdact_chat");
if ($mysqli->connect_errno) {
    die("Connessione fallita: " . $mysqli->connect_error);
}

// Inserimento messaggio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($username !== '' && $message !== '') {
        $stmt = $mysqli->prepare("INSERT INTO messages (username, message) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $message);
        $stmt->execute();
        $stmt->close();
        header("Location: chat.php");
        exit;
    }
}

// Recupera messaggi
$result = $mysqli->query("SELECT username, message, created_at FROM messages ORDER BY created_at DESC LIMIT 50");
$messages = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    $result->free();
}
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Chat - Condividi pensieri a caso." />
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <title>Chat - GheshinMerDact</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #0d0d0d;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
        }

        nav {
            background: #1a1a1a;
            padding: 1rem 0;
            text-align: center;
            margin-bottom: 2rem;
            border-radius: 0.75rem;
            width: 100%;
            max-width: 1200px;
        }

        nav a {
            color: white;
            margin: 0 1.25rem;
            font-weight: 700;
            text-decoration: none;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #fbbc05;
        }

        header.hero {
            text-align: center;
            margin-bottom: 3rem;
            max-width: 1200px;
            width: 100%;
            background: rgba(255 255 255 / 0.05);
            padding: 2rem 1rem;
            border-radius: 1rem;
            box-shadow: 0 0 15px rgb(251 188 5 / 0.3);
        }

        header.hero h1 {
            font-size: 3rem;
            margin-bottom: 0.5rem;
            color: #fbbc05;
            text-shadow: 1px 1px 3px #000;
        }

        header.hero p {
            font-size: 1.5rem;
            color: #ddd;
            margin-bottom: 1.5rem;
        }

        main.content {
            max-width: 1200px;
            width: 100%;
            background: rgba(255 255 255 / 0.05);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
            margin-bottom: 3rem;
        }

        form.chat-form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        form.chat-form input[type="text"] {
            flex: 1 1 200px;
            padding: 0.8rem 1rem;
            border-radius: 9999px;
            border: none;
            font-size: 1rem;
        }

        form.chat-form button {
            background-color: #ea4335;
            color: white;
            border: none;
            padding: 0.9rem 2.5rem;
            border-radius: 9999px;
            font-weight: 700;
            cursor: pointer;
            font-size: 1.1rem;
            box-shadow: 0 4px 10px rgb(234 67 53 / 0.6);
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        form.chat-form button:hover {
            background-color: #ff6659;
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgb(255 102 89 / 0.9);
        }

        .messages-box {
            max-height: 400px;
            overflow-y: auto;
            background: rgba(0,0,0,0.4);
            border-radius: 15px;
            padding: 1rem;
            box-shadow: inset 0 0 15px rgb(251 188 5 / 0.4);
        }

        .message {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        .message:last-child {
            border-bottom: none;
        }

        .message strong {
            color: #fbbc05;
            display: block;
            margin-bottom: 0.25rem;
            font-weight: 700;
        }

        .message time {
            font-size: 0.75rem;
            color: #bbb;
            float: right;
        }

        footer {
            background-color: #111;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #aaa;
            border-radius: 0.5rem;
            width: 100%;
            max-width: 1200px;
        }
    </style>
</head>
<body>

<nav>
    <a href="home">Home</a>
    <a href="clips">Clips</a>
    <a href="gioco">Gioco</a>
    <a href="senza-senso">Senza Senso</a>
    <a href="chat">Chat</a>
    <a href="film-marvel-in-uscita">Film Marvel</a>
    <a href="serie-marvel-in-uscita">Serie Marvel</a>
</nav>

<header class="hero">
    <h1>Chat</h1>
    <p>Scrivi qualcosa di totalmente casuale!</p>
</header>

<main class="content">
    <form class="chat-form" method="post" action="chat.php" autocomplete="off">
        <input type="text" name="username" placeholder="Nome" required maxlength="50" />
        <input type="text" name="message" placeholder="Scrivi un messaggio..." required maxlength="500" />
        <button type="submit">Invia</button>
    </form>

    <div class="messages-box" id="messages">
        <?php if (count($messages) === 0): ?>
            <p style="color: #ddd; text-align:center;">Nessun messaggio presente.</p>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <div class="message">
                    <strong><?= htmlspecialchars($msg['username']) ?></strong>
                    <time><?= date("d/m/Y H:i", strtotime($msg['created_at'])) ?></time>
                    <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; 2025 GheshinMerDact. Tutti i diritti riservati.</p>
</footer>

</body>
</html>
