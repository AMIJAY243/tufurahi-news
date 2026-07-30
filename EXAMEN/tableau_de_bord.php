<?php
session_start();
// Vérification de la connexion (sécurité du dashboard)
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - TufurahiAmijayNews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #334155; }
        .navbar-custom { background: #0f172a; }
        .card-stat { border: none; border-radius: 12px; color: white; }
        .module-card { transition: all 0.2s ease; text-decoration: none; display: block; color: inherit; }
        .module-card:hover { transform: translateY(-2px); }
    </style>
</head>
<body>

    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-newspaper text-primary me-2"></i>TufurahiAmijay<span class="text-info">News</span>
            </a>
            <div class="d-flex align-items-center">
                <!-- Affichage dynamique du nom et du vrai rôle de l'utilisateur connecté -->
                <span class="text-light me-3 small">
                    <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($_SESSION['user_nom'] ?? 'Utilisateur') ?> 
                    <span class="badge bg-<?= (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN') ? 'danger' : 'secondary' ?> ms-1">
                        <?= htmlspecialchars($_SESSION['user_role'] ?? 'Lecteur') ?>
                    </span>
                </span>
                <a href="deconnexion.php" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Quitter</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 my-4">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Dashboard</h2>
            <p class="text-muted small">Bienvenue sur votre espace de gestion sécurisé.</p>
        </div>

        <!-- Statistiques -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card card-stat bg-primary p-3 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase small fw-semibold mb-1">Articles</h6>
                            <h3 class="fw-bold mb-0">2</h3>
                        </div>
                        <i class="fas fa-file-alt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat bg-success p-3 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase small fw-semibold mb-1">Catégories</h6>
                            <h3 class="fw-bold mb-0">2</h3>
                        </div>
                        <i class="fas fa-list fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat bg-warning p-3 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase small fw-semibold mb-1">Utilisateurs</h6>
                            <h3 class="fw-bold mb-0">4</h3>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat bg-danger p-3 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase small fw-semibold mb-1">Commentaires</h6>
                            <h3 class="fw-bold mb-0">0</h3>
                        </div>
                        <i class="fas fa-comments fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modules de Navigation -->
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <h4 class="fw-bold mb-3 text-dark fs-5"><i class="fas fa-th text-primary me-2"></i>Modules de Navigation</h4>
            <p class="text-muted small mb-4">Accédez aux fonctionnalités autorisées pour votre profil :</p>

            <div class="list-group list-group-flush gap-2">
                <!-- Voir le site public -->
                <a href="index.php" class="list-group-item list-group-item-action border rounded-3 p-3 module-card">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-globe text-primary fa-lg me-3"></i>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Voir le site public</h6>
                            <small class="text-muted">Consulter les actualités et laisser des commentaires</small>
                        </div>
                    </div>
                </a>

                <!-- Gérer les publications -->
                <a href="gestion_publications.php" class="list-group-item list-group-item-action border rounded-3 p-3 module-card">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-edit text-success fa-lg me-3"></i>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Gérer les publications</h6>
                            <small class="text-muted">Créer, rédiger et gérer vos articles d'actualités</small>
                        </div>
                    </div>
                </a>

                <!-- Gérer les Catégories -->
                <a href="gestion_categories.php" class="list-group-item list-group-item-action border rounded-3 p-3 module-card">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-list-alt text-success fa-lg me-3"></i>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Gérer les catégories</h6>
                            <small class="text-muted">Ajouter et organiser les catégories de vos articles</small>
                        </div>
                    </div>
                </a>

                <!-- Gérer les Utilisateurs -->
                <a href="gestion_utilisateurs.php" class="list-group-item list-group-item-action border rounded-3 p-3 module-card">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users-cog text-warning fa-lg me-3"></i>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Gérer les utilisateurs</h6>
                            <small class="text-muted">Modifier les permissions et les rôles des membres</small>
                        </div>
                    </div>
                </a>

                <!-- Gérer les Archives -->
                <a href="archives.php" class="list-group-item list-group-item-action border rounded-3 p-3 module-card">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-archive text-warning fa-lg me-3"></i>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Gérer les archives</h6>
                            <small class="text-muted">Consulter et restaurer les articles archivés</small>
                        </div>
                    </div>
                </a>

                <!-- Journal d'Historique / Corbeille -->
                <a href="historique.php" class="list-group-item list-group-item-action border rounded-3 p-3 module-card">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-history text-info fa-lg me-3"></i>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Journal d'historique & Corbeille</h6>
                            <small class="text-muted">Visualiser la traçabilité et gérer les utilisateurs supprimés</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>