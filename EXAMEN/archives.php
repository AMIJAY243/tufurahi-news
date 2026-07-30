<?php
session_start();
require_once 'connexion.php';
require_once 'securite.php';

exigerAdmin($pdo);

// Récupérer les archives en faisant une jointure avec la table des publications pour avoir le titre
$sql = "SELECT a.id as archive_id, p.id as publication_id, p.titre, a.date_archivage 
        FROM archives a 
        JOIN publications p ON a.id_publication = p.id 
        ORDER BY a.id DESC";
$stmt = $pdo->query($sql);
$archives = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archives - Tufurahi News</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #334155; }
        .navbar { background: #0f172a; }
        .card-table { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: white; }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="tableau_de_bord.php">
                <i class="fas fa-newspaper text-primary me-2"></i>Tufurahi<span class="text-info">News</span> Admin
            </a>
            <div class="d-flex align-items-center">
                <span class="text-light me-3 small"><i class="fas fa-user-shield me-1"></i><?= htmlspecialchars($_SESSION['user_nom']) ?> (Admin)</span>
                <a href="tableau_de_bord.php" class="btn btn-outline-light btn-sm me-2"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
                <a href="deconnexion.php" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Quitter</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 my-4">
        <div class="mb-4">
            <h2><i class="fas fa-archive text-warning me-2"></i>Articles Archivés</h2>
            <p class="text-muted small mb-0">Retrouvez ici tous les éléments stockés dans la table des archives.</p>
        </div>

        <div class="card card-table p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7">
                        <tr>
                            <th class="py-3">#ID Archive</th>
                            <th class="py-3">Titre de la Publication</th>
                            <th class="py-3">Date d'archivage</th>
                            <th class="py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($archives) > 0): ?>
                            <?php foreach ($archives as $arch): ?>
                                <tr>
                                    <td><strong>#<?= $arch['archive_id'] ?></strong></td>
                                    <td><?= htmlspecialchars($arch['titre'] ?? 'Sans titre') ?></td>
                                    <td><?= htmlspecialchars($arch['date_archivage'] ?? 'Non spécifiée') ?></td>
                                    <td class="text-end">
                                        <a href="article_detail.php?id=<?= $arch['publication_id'] ?>" class="btn btn-sm btn-info text-white" title="Voir l'article">
                                            <i class="fas fa-eye me-1"></i> Voir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Aucun article dans les archives.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>