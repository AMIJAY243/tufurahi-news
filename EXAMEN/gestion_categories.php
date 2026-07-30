<?php
session_start();
require_once 'connexion.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion_vue.php');
    exit();
}

$message = "";

// Ajout d'une catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_categorie'])) {
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);

    if (!empty($nom)) {
        $stmt = $pdo->prepare("INSERT INTO categories (nom, description) VALUES (?, ?)");
        if ($stmt->execute([$nom, $description])) {
            $message = "<div class='alert alert-success'>Catégorie ajoutée avec succès.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Erreur lors de l'ajout.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Le nom de la catégorie est obligatoire.</div>";
    }
}

// Suppression d'une catégorie
if (isset($_GET['supprimer'])) {
    $id_cat = $_GET['supprimer'];
    $del = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $del->execute([$id_cat]);
    header('Location: gestion_categories.php');
    exit();
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Catégories - Tufurahi Amijay News</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .navbar { background: linear-gradient(135deg, #1e293b, #0f172a); }
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="tableau_de_bord.php"><i class="fas fa-newspaper text-info me-2"></i>Tufurahi Amijay News</a>
            <div class="d-flex align-items-center">
                <span class="text-light me-3"><i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
                <a href="tableau_de_bord.php" class="btn btn-outline-light btn-sm me-2"><i class="fas fa-tachometer-alt me-1"></i> Tableau de bord</a>
                <a href="deconnexion.php" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="mb-4"><i class="fas fa-list text-success me-2"></i>Gestion des Catégories</h2>
        <?= $message ?>

        <div class="row">
            <!-- Formulaire d'ajout -->
            <div class="col-md-4 mb-4">
                <div class="card p-4">
                    <h5 class="mb-3">Ajouter une catégorie</h5>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" name="ajouter_categorie" class="btn btn-success w-100"><i class="fas fa-plus me-1"></i> Enregistrer</button>
                    </form>
                </div>
            </div>

            <!-- Liste des catégories -->
            <div class="col-md-8">
                <div class="card p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                ID
                                <th>Nom</th>
                                <th>Description</th>
                                <th class="text-end">Actions</th>
                            </thead>
                            <tbody>
                                <?php if (count($categories) > 0): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <tr>
                                            <td><?= $cat['id'] ?></td>
                                            <td class="fw-bold"><?= htmlspecialchars($cat['nom']) ?></td>
                                            <td><?= htmlspecialchars($cat['description']) ?></td>
                                            <td class="text-end">
                                                <a href="gestion_categories.php?supprimer=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette catégorie ?');"><i class="fas fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Aucune catégorie enregistrée.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>