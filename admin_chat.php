<?php
session_start();

$admin_password = "tuapasswordadmin"; // Cambia questa password!

// Login
if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if ($_POST['password'] === $admin_password) {
            $_SESSION['admin_logged_in'] = true;
            header("Location: admin_chat.php");
            exit;
        } else {
            $error = "Password errata.";
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8" />
        <title>Login Admin Chat</title>
    </head>
    <body>
        <h2>Login Admin Chat</h2>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="post" action="">
            <input type="password" name="password" placeholder="Password admin" required />
            <button type="submit">Accedi</button>
        </form>
    </body>
    </html>
    <?php
    exit;
}

// Connessione DB
$mysqli = new mysqli("localhost", "root", "17092007", "gheshinmerdact_chat");
if ($mysqli->connect_errno) {
    die("Connessione fallita: " . $mysqli->connect_error);
}

// Gestione cancellazione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int) $_POST['delete_id'];
    $stmt = $mysqli->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_chat.php");
    exit;
}

// Recupera messaggi
$result = $mysqli->query("SELECT id, username, message, created_at FROM messages ORDER BY created_at DESC");
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
    <title>Admin Chat - Gestione Messaggi</title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #0d0d0d;
            color: #fff;
            padding: 2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #444;
            text-align: left;
        }
        th {
            background: #222;
        }
        tr:hover {
            background: #111;
        }
        form {
            margin: 0;
        }
        button {
            background: #ea4335;
            border: none;
            padding: 0.3rem 0.7rem;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            font-weight: 700;
        }
        button:hover {
            background: #ff6659;
        }
        h1 {
            color: #fbbc05;
        }
        .logout {
            margin-top: 1rem;
        }
        .logout a {
            color: #fbbc05;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>

<h1>Admin Chat - Gestione Messaggi</h1>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Utente</th>
            <th>Messaggio</th>
            <th>Data</th>
            <th>Elimina</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($messages) === 0): ?>
            <tr><td colspan="5" style="text-align:center; color:#bbb;">Nessun messaggio</td></tr>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
            <tr>
                <td><?= $msg['id'] ?></td>
                <td><?= htmlspecialchars($msg['username']) ?></td>
                <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                <td><?= date("d/m/Y H:i", strtotime($msg['created_at'])) ?></td>
                <td>
                    <form method="post" onsubmit="return confirm('Sei sicuro di voler cancellare questo messaggio?');">
                        <input type="hidden" name="delete_id" value="<?= $msg['id'] ?>" />
                        <button type="submit">Elimina</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div class="logout">
    <a href="admin_logout.php">Logout</a>
</div>

</body>
</html>
