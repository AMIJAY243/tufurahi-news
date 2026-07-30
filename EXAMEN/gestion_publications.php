<?php
session_start();
require_once 'connexion.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion_vue.php');
    exit();
}

// Suppression d'une publication
if (isset($_GET['supprimer'])) {
    $id_sup = $_GET['supprimer'];
    $stmtImg = $pdo->prepare("SELECT image FROM publications WHERE id = ?");
    $stmtImg->execute([$id_sup]);
    $imgData = $stmtImg->fetch();
    if ($imgData && !empty($imgData['image']) && file_exists($imgData['image'])) {
        unlink($imgData['image']);
    }

    $del = $pdo->prepare("DELETE FROM publications WHERE id = ?");
    $del->execute([$id_sup]);
    header('Location: gestion_publications.php?success=supprime');
    exit();
}

// Recherche et filtrage performants
$recherche = $_GET['q'] ?? '';
$filtre_statut = $_GET['statut'] ?? '';

$query = "SELECT p.*, c.nom as nom_categorie, u.nom as nom_auteur 
          FROM publications p 
          LEFT JOIN categories c ON p.id_categorie = c.id 
          LEFT JOIN utilisateurs u ON p.id_auteur = u.id 
          WHERE 1=1";

$params = [];

if (!empty($recherche)) {
    $query .= " AND (p.titre LIKE ? OR p.contenu LIKE ?)";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
}

if (!empty($filtre_statut)) {
    $query .= " AND p.statut = ?";
    $params[] = $filtre_statut;
}

$query .= " ORDER BY p.date_publication DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$publications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Publications - Tufurahi News</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #334155; }
        .navbar { background: #0f172a; }
        .card-table { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: white; }
        .table img { width: 65px; height: 45px; object-fit: cover; border-radius: 8px; }
        .btn-custom { border-radius: 8px; font-weight: 500; }
        .filter-card { background: white; border-radius: 12px; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="tableau_de_bord.php">
                <i class="fas fa-newspaper text-primary me-2"></i>Tufurahi<span class="text-info">News</span> Admin
            </a>
            <div class="d-flex align-items-center">
                <span class="text-light me-3 small"><i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
                <a href="tableau_de_bord.php" class="btn btn-outline-light btn-sm btn-custom me-2"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
                <a href="deconnexion.php" class="btn btn-danger btn-sm btn-custom"><i class="fas fa-sign-out-alt me-1"></i> Quitter</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 my-4">
        <!-- En-tête -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-folder-open text-primary me-2"></i>Gestion des Publications</h2>
                <p class="text-muted small mb-0">Ajoutez, modifiez, filtrez ou supprimez vos articles en toute simplicité.</p>
            </div>
            <a href="ajouter_publication.php" class="btn btn-primary btn-custom shadow-sm px-3 py-2">
                <i class="fas fa-plus-circle me-1"></i> Nouvel Article
            </a>
        </div>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'supprime'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Article supprimé avec succès.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Barre de recherche et filtres performante -->
        <div class="filter-card p-3 mb-4">
            <form method="GET" action="gestion_publications.php" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control bg-light border-0 shadow-none" placeholder="Rechercher par titre ou contenu..." value="<?= htmlspecialchars($recherche) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="statut" class="form-select bg-light border-0 shadow-none">
                        <option value="">Tous les statuts</option>
                        <option value="publie" <?= ($filtre_statut == 'publie') ? 'selected' : '' ?>>Publié</option>
                        <option value="brouillon" <?= ($filtre_statut == 'brouillon') ? 'selected' : '' ?>>Brouillon</option>
                        <option value="archive" <?= ($filtre_statut == 'archive') ? 'selected' : '' ?>>Archivé</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 btn-custom">Filtrer</button>
                </div>
            </form>
        </div>

        <!-- Tableau des articles -->
        <div class="card card-table p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7">
                        <tr>
                            <th class="py-3">Illustration</th>
                            <th class="py-3">Titre de l'article</th>
                            <th class="py-3">Catégorie</th>
                            <th class="py-3">Auteur</th>
                            <th class="py-3">Statut</th>
                            <th class="py-3">Date</th>
                            <th class="py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($publications) > 0): ?>
                            <?php foreach ($publications as $pub): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($pub['image'])): ?>
                                            <img src="<?= htmlspecialchars($pub['image']) ?>" alt="Aperçu">
                                        <?php else: ?>
                                            <span class="badge bg-secondary text-light px-2 py-1">Aucune</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($pub['titre']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1"><?= htmlspecialchars($pub['nom_categorie'] ?? 'Non classé') ?></span>
                                    </td>
                                    <td><small class="text-secondary"><?= htmlspecialchars($pub['nom_auteur'] ?? 'Inconnu') ?></small></td>
                                    <td>
                                        <?php if ($pub['statut'] == 'publie'): ?>
                                            <span class="badge bg-success">Publié</span>
                                        <?php elseif ($pub['statut'] == 'brouillon'): ?>
                                            <span class="badge bg-warning text-dark">Brouillon</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Archivé</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($pub['date_publication'])) ?></small></td>
                                    <td class="text-end">
                                        <a href="modifier_publication.php?id=<?= $pub['id'] ?>" class="btn btn-sm btn-light text-primary btn-custom me-1" title="Modifier"><i class="fas fa-edit"></i></a>
                                        <a href="gestion_publications.php?supprimer=<?= $pub['id'] ?>" class="btn btn-sm btn-light text-danger btn-custom" title="Supprimer" onclick="return confirm('Voulez-vous vraiment supprimer cet article ?');"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                                    Aucune publication trouvée.
                                </td>
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