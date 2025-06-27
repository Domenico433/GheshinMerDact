<?php
// === Configurazione DB ===
$host = 'localhost';
$db   = 'nome_database';
$user = 'nome_utente';
$pass = 'password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo json_encode([]);
    } else {
        echo "Errore connessione DB";
    }
    exit;
}

// === API per invio messaggi ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($username && $message) {
        $stmt = $pdo->prepare("INSERT INTO chat_messages (username, message) VALUES (?, ?)");
        $stmt->execute([$username, $message]);
    }
    exit;
}

// === API per ricevere messaggi ===
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax'])) {
    $stmt = $pdo->query("SELECT username, message FROM chat_messages ORDER BY created_at DESC LIMIT 100");
    echo json_encode(array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC)));
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Chat - Condividi pensieri a caso.">
  <link rel="stylesheet" href="css/stile_home.css" />
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <script src="js/menu.js" defer></script>
  <title>Chat</title>
  <style>
    .chat-card {
      background-color: #1e1e1e;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 0 10px #fbbc05;
      max-width: 600px;
      width: 100%;
      margin: 30px auto;
      color: white;
    }
    .messages {
      background-color: rgba(255, 255, 255, 0.05);
      border: 1px solid #444;
      padding: 15px;
      max-height: 350px;
      overflow-y: auto;
      border-radius: 10px;
      margin-bottom: 20px;
    }
    .messages p {
      margin: 5px 0;
    }
    .input-area {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      justify-content: center;
    }
    .input-area input {
      padding: 8px;
      border-radius: 8px;
      border: none;
      width: 45%;
    }
    .btn {
      padding: 10px 20px;
      background-color: #ea4335;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }
    .btn:hover {
      background-color: #ff6659;
    }
  </style>
</head>
<body>

<!-- Menu -->
<div class="menu-container">
  <button class="menu-button" onclick="toggleMenu()">☰ Menu</button>
  <div id="menu" class="menu">
    <ul>
      <li><a href="home">Home</a></li>
      <li><a href="clips">Clips</a></li>
      <li><a href="gioco">Gioco</a></li>
      <li><a href="senza-senso">Senza Senso</a></li>
      <li><a href="chat">Chat</a></li>
      <li><a href="film-marvel-in-uscita">Film Marvel</a></li>
      <li><a href="serie-marvel-in-uscita">Serie Marvel</a></li>
    </ul>
  </div>
</div>

<!-- Header -->
<header class="hero">
  <div class="hero-content">
    <h1>Chat GheshinMerDact 💬</h1>
    <p>Scrivi qualcosa di completamente senza senso e condividilo con il mondo!</p>
  </div>
</header>

<!-- Contenuto -->
<main class="features" style="flex-direction: column; align-items: center;">

  <section class="chat-card">
    <div class="messages" id="messages">Caricamento...</div>

    <div class="input-area">
      <input type="text" id="username" placeholder="Nome" />
      <input type="text" id="message" placeholder="Messaggio" />
      <button class="btn" onclick="sendMessage()">Invia</button>
    </div>
  </section>

</main>

<!-- Footer -->
<footer>
  <p>&copy; 2025 GheshinMerDact. Tutti i diritti riservati.</p>
</footer>

<script>
  async function loadMessages() {
    const res = await fetch('chat.php?ajax=1');
    const data = await res.json();
    const messagesBox = document.getElementById('messages');
    messagesBox.innerHTML = '';
    data.forEach(msg => {
      const p = document.createElement('p');
      p.innerHTML = `<strong>${msg.username}</strong>: ${msg.message}`;
      messagesBox.appendChild(p);
    });
    messagesBox.scrollTop = messagesBox.scrollHeight;
  }

  async function sendMessage() {
    const username = document.getElementById('username').value.trim();
    const message = document.getElementById('message').value.trim();
    if (!username || !message) return;

    await fetch('chat.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `username=${encodeURIComponent(username)}&message=${encodeURIComponent(message)}`
    });

    document.getElementById('message').value = '';
    loadMessages();
  }

  setInterval(loadMessages, 3000);
  loadMessages();
</script>

</body>
</html>
