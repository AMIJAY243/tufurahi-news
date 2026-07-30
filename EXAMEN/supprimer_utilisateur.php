<?php
session_start();
require_once 'connexion.php';
require_once 'securite.php';

// Exiger les droits administrateur
exigerAdmin($pdo);

// Récupérer tous les utilisateurs se trouvant dans la corbeille (table historique)
$corbeille = $pdo->query("SELECT * FROM historique ORDER BY date_suppression DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corbeille des Utilisateurs - Tufurahi News</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #334155; }
        .navbar { background: #0f172a; }
        .card-table { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: white; }
    </style>
</head>
<body>

    <!-- Navbar d'administration -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="tableau_de_bord.php">
                <i class="fas fa-newspaper text-primary me-2"></i>Tufurahi<span class="text-info">News</span> Admin
            </a>
            <div class="d-flex align-items-center">
                <span class="text-light me-3 small"><i class="fas fa-user-shield me-1"></i><?= htmlspecialchars($_SESSION['user_nom']) ?> (Admin)</span>
                <a href="gestion_utilisateurs.php" class="btn btn-outline-light btn-sm me-2"><i class="fas fa-users me-1"></i> Gestion Utilisateurs</a>
                <a href="tableau_de_bord.php" class="btn btn-outline-light btn-sm me-2"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
                <a href="deconnexion.php" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Quitter</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 my-4">
        <div class="mb-4">
            <h2><i class="fas fa-trash text-danger me-2"></i>Corbeille des Utilisateurs</h2>
            <p class="text-muted small mb-0">Retrouvez les utilisateurs supprimés. Vous pouvez les restaurer ou les supprimer définitivement à tout moment.</p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                    if ($_GET['success'] == 'restaure') echo "Utilisateur restauré avec succès.";
                    if ($_GET['success'] == 'supprime_definitif') echo "Utilisateur supprimé définitivement.";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card card-table p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7">
                        <tr>
                            <th class="py-3">#ID (Origine)</th>
                            <th class="py-3">Nom complet</th>
                            <th class="py-3">Adresse Email</th>
                            <th class="py-3">Rôle initial</th>
                            <th class="py-3">Date de suppression</th>
                            <th class="py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($corbeille) > 0): ?>
                            <?php foreach ($corbeille as $user): ?>
                                <tr>
                                    <td><strong>#<?= $user['user_id_original'] ?></strong></td>
                                    <td><?= htmlspecialchars($user['nom']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="badge bg-secondary px-3 py-2"><?= htmlspecialchars($user['role']) ?></span>
                                    </td>
                                    <td><?= $user['date_suppression'] ?></td>
                                    <td class="text-end">
                                        <!-- Bouton Restaurer -->
                                        <a href="restaurer_utilisateur.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-success me-1" title="Restaurer l'utilisateur" onclick="return confirm('Voulez-vous vraiment restaurer cet utilisateur ?');">
                                            <i class="fas fa-undo me-1"></i> Restaurer
                                        </a>
                                        <!-- Bouton Supprimer Définitivement -->
                                        <a href="supprimer_definitif.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" title="Supprimer définitivement" onclick="return confirm('Attention : Cette action est irréversible. Supprimer définitivement ?');">
                                            <i class="fas fa-trash-alt me-1"></i> Supprimer
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">La corbeille est vide. Aucun utilisateur supprimé.</td>
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