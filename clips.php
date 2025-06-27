<?php
$uploadDir = __DIR__ . '/upload/';
$maxFileSize = 50 * 1024 * 1024; // 50MB

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            if ($file['size'] <= $maxFileSize) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg'];
                if (in_array($file['type'], $allowedTypes)) {
                    $fileName = basename($file['name']);
                    $targetPath = $uploadDir . $fileName;
                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $success = "File caricato con successo!";
                    } else {
                        $errors[] = "Errore nel salvataggio del file.";
                    }
                } else {
                    $errors[] = "Formato file non consentito.";
                }
            } else {
                $errors[] = "File troppo grande. Max 50MB.";
            }
        } else {
            $errors[] = "Errore nel caricamento file.";
        }
    } else {
        $errors[] = "Nessun file selezionato.";
    }
}

$files = array_diff(scandir($uploadDir), ['.', '..']);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Carica e Visualizza Clip" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <title>Clips - GheshinMerDact</title>
    <style>
        /* Stili ripresi dalla homepage */
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
            font-size: 2.5rem;
            color: #fbbc05;
            margin-bottom: 1rem;
        }

        header.hero p {
            font-size: 1.2rem;
            color: #ddd;
        }

        main {
            max-width: 1200px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 25px;
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
        }

        .btn {
            background-color: #ea4335;
            color: white;
            padding: 0.9rem 2rem;
            border-radius: 9999px;
            font-weight: 700;
            text-decoration: none;
            font-size: 1rem;
            transition: background-color 0.3s ease, transform 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 10px rgb(234 67 53 / 0.6);
        }

        .btn:hover {
            background-color: #ff6659;
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgb(255 102 89 / 0.9);
        }

        .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .gallery img, .gallery video {
            max-width: 200px;
            border-radius: 15px;
            box-shadow: 0 0 10px #000;
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

        .mobile-block {
            display: none;
            text-align: center;
            font-size: 1.4rem;
            color: #ff5555;
            font-weight: bold;
            max-width: 400px;
            margin: auto;
            padding: 2rem;
        }

        @media (max-width: 768px) {
            body > *:not(.mobile-block) {
                display: none !important;
            }
            .mobile-block {
                display: block !important;
            }
        }
    </style>
</head>
<body>

    <div class="mobile-block">
        <p>Questo sito non è accessibile da dispositivi mobili.</p>
    </div>

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
        <h1>Clips</h1>
        <p>Carica immagini o video e visualizzali qui sotto.</p>
    </header>

    <main>

        <?php if ($success): ?>
            <p style="color: #0f0;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <?php if ($errors): ?>
            <ul style="color: #f44;">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <section class="card">
            <form method="post" enctype="multipart/form-data">
                <label for="file"><strong>Seleziona immagine o video (max 50MB):</strong></label><br><br>
                <input type="file" name="file" id="file" accept="image/*,video/*" required /><br><br>
                <button type="submit" class="btn">Carica</button>
            </form>
        </section>

        <section class="card" style="width: 100%; max-width: 1000px;">
            <h2 style="color: #fbbc05; margin-bottom: 1rem;">Clip Caricate</h2>
            <div class="gallery">
                <?php foreach ($files as $file): 
                    $filePath = 'upload/' . $file;
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg','jpeg','png','gif','webp'])): ?>
                        <img src="<?= htmlspecialchars($filePath) ?>" alt="immagine" />
                    <?php elseif (in_array($ext, ['mp4','webm','ogg'])): ?>
                        <video controls>
                            <source src="<?= htmlspecialchars($filePath) ?>" type="video/<?= $ext ?>" />
                            Il tuo browser non supporta il video.
                        </video>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

    </main>

    <footer>
        <p>&copy; 2025 GheshinMerDact. Tutti i diritti riservati.</p>
    </footer>

</body>
</html>
