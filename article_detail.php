<?php
session_start();
require_once 'connexion.php';

// Vérifier si un ID est présent dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: archives.php');
    exit();
}

$id_publication = $_GET['id'];

// Récupérer la publication dans la table 'publications'
$stmt = $pdo->prepare("SELECT * FROM publications WHERE id = ?");
$stmt->execute([$id_publication]);
$article = $stmt->fetch();

if (!$article) {
    die("<div class='container my-5 text-center'><h3>Article introuvable.</h3><a href='archives.php' class='btn btn-primary mt-3'>Retour aux archives</a></div>");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['titre']) ?> - Tufurahi News</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #334155; }
    </style>
</head>
<body class="bg-light">
    <div class="container my-5" style="max-width: 800px;">
        <a href="archives.php" class="btn btn-outline-secondary btn-sm mb-4"><i class="fas fa-arrow-left me-1"></i> Retour aux archives</a>
        
        <div class="card shadow-sm p-4 bg-white rounded-4 border-0">
            <div class="alert alert-warning py-2 small mb-3">
                <i class="fas fa-archive me-1"></i> Cet élément provient des <strong>archives</strong>.
            </div>

            <h1 class="fw-bold mb-3 text-dark"><?= htmlspecialchars($article['titre']) ?></h1>
            <hr class="text-muted opacity-25">
            <div class="article-content mt-3" style="line-height: 1.7;">
                <?= nl2br(htmlspecialchars($article['contenu'] ?? 'Contenu non disponible.')) ?>
            </div>
        </div>
    </div>
</body>
</html>