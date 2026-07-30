<?php
require_once 'connexion.php';

// Gestion des filtres et de la recherche
$recherche = $_GET['q'] ?? '';
$id_cat = $_GET['categorie'] ?? '';

$sql = "SELECT p.*, c.nom as nom_categorie, u.nom as nom_auteur 
        FROM publications p 
        LEFT JOIN categories c ON p.id_categorie = c.id 
        LEFT JOIN utilisateurs u ON p.id_auteur = u.id 
        WHERE p.statut = 'publie'";

$params = [];

if (!empty($recherche)) {
    $sql .= " AND (p.titre LIKE ? OR p.contenu LIKE ?)";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
}

if (!empty($id_cat)) {
    $sql .= " AND p.id_categorie = ?";
    $params[] = $id_cat;
}

$sql .= " ORDER BY p.date_publication DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Récupérer les catégories pour le menu de filtrage
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tufurahi Amijay News - L'actualité en temps réel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #334155;
        }
        .navbar {
            background: #ffffff;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        .hero-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 80px 0;
            margin-bottom: 40px;
        }
        .card-article {
            border: none;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .card-article:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .card-img-top {
            height: 220px;
            object-fit: cover;
        }
        .badge-category {
            background-color: #eff6ff;
            color: #2563eb;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 6px;
        }
        .search-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 text-primary" href="index.php">
                <i class="fas fa-newspaper me-2"></i>Tufurahi Amijay<span class="text-dark">News</span>
            </a>
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="tableau_de_bord.php" class="btn btn-dark btn-sm rounded-pill px-3 me-2">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                    <a href="deconnexion.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">Déconnexion</a>
                <?php else: ?>
                    <a href="connexion_vue.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 me-2">Connexion</a>
                    <a href="inscription.php" class="btn btn-primary btn-sm rounded-pill px-3">S'inscrire</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section avec Recherche Avancée -->
    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-3">Restez informé en temps réel</h1>
            <p class="lead text-slate-300 mb-5">Découvrez les analyses, reportages et actualités sélectionnés pour vous.</p>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <form method="GET" action="index.php" class="search-box row g-3">
                        <div class="col-md-5">
                            <input type="text" name="q" class="form-control form-control-lg border-0 shadow-none" placeholder="Rechercher un sujet..." value="<?= htmlspecialchars($recherche) ?>">
                        </div>
                        <div class="col-md-4">
                            <select name="categorie" class="form-select form-select-lg border-0 shadow-none">
                                <option value="">Toutes les catégories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($id_cat == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">
                                <i class="fas fa-search me-1"></i> Explorer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenu Principal : Liste des Articles -->
    <div class="container mb-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold m-0"><i class="fas fa-fire text-danger me-2"></i>À la une</h3>
            <span class="text-muted"><?= count($articles) ?> article(s) disponible(s)</span>
        </div>

        <div class="row">
            <?php if (count($articles) > 0): ?>
                <?php foreach ($articles as $art): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card card-article h-100">
                            <?php if (!empty($art['image'])): ?>
                                <img src="<?= htmlspecialchars($art['image']) ?>" class="card-img-top" alt="Illustration">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1585829365295-ab7cd400c167?auto=format&fit=crop&w=600&q=80" class="card-img-top" alt="Par défaut">
                            <?php endif; ?>
                            
                            <div class="card-body d-flex flex-column p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge badge-category"><?= htmlspecialchars($art['nom_categorie'] ?? 'Général') ?></span>
                                    <small class="text-muted"><i class="far fa-clock me-1"></i><?= date('d M Y', strtotime($art['date_publication'])) ?></small>
                                </div>
                                
                                <h4 class="card-title fw-bold fs-5 mb-3 text-dark"><?= htmlspecialchars($art['titre']) ?></h4>
                                <p class="card-text text-muted mb-4 flex-grow-1"><?= mb_substr(htmlspecialchars($art['contenu']), 0, 110) ?>...</p>
                                
                                <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                                    <small class="text-secondary"><i class="fas fa-user-pen me-1"></i><?= htmlspecialchars($art['nom_auteur'] ?? 'Rédaction') ?></small>
                                    <a href="article_detail.php?id=<?= $art['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                        Lire l'article <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="p-5 bg-white rounded-4 shadow-sm">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Aucun article ne correspond à votre recherche.</h4>
                        <p class="text-muted small">Essayez de modifier vos filtres ou mots-clés.</p>
                        <a href="index.php" class="btn btn-outline-primary btn-sm mt-2">Réinitialiser la recherche</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0 text-secondary">&copy; 2026 Tufurahi News. Tous droits réservés.</p>
        </div>
    </footer>

</body>
</html>