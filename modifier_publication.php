<?php
session_start();
require_once 'connexion.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion_vue.php');
    exit();
}

$id_article = $_GET['id'] ?? null;
if (!$id_article) {
    header('Location: gestion_publications.php');
    exit();
}

// Récupérer l'article à modifier
$stmt = $pdo->prepare("SELECT * FROM publications WHERE id = ?");
$stmt->execute([$id_article]);
$article = $stmt->fetch();

if (!$article) {
    die("Article introuvable.");
}

// Récupérer les catégories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $id_categorie = $_POST['id_categorie'];
    $statut = $_POST['statut'];
    $cheminImage = $article['image']; // Garder l'ancienne image par défaut

    // Gestion du nouveau téléversement d'image si une image est fournie
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $dossierUpload = 'uploads/';
        if (!is_dir($dossierUpload)) {
            mkdir($dossierUpload, 0777, true);
        }
        $nomFichier = uniqid() . '_' . basename($_FILES['image']['name']);
        $cheminImageTemp = $dossierUpload . $nomFichier;
        $extension = strtolower(pathinfo($cheminImageTemp, PATHINFO_EXTENSION));
        $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($extension, $extensionsAutorisees)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $cheminImageTemp)) {
                // Supprimer l'ancienne image si elle existe
                if (!empty($article['image']) && file_exists($article['image'])) {
                    unlink($article['image']);
                }
                $cheminImage = $cheminImageTemp;
            }
        }
    }

    if (!empty($titre) && !empty($contenu) && !empty($id_categorie)) {
        $update = $pdo->prepare("UPDATE publications SET titre = ?, contenu = ?, image = ?, statut = ?, id_categorie = ? WHERE id = ?");
        if ($update->execute([$titre, $contenu, $cheminImage, $statut, $id_categorie, $id_article])) {
            header('Location: gestion_publications.php');
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Erreur lors de la mise à jour dans la base de données.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Veuillez remplir tous les champs obligatoires.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'Article - Tufurahi Amijay News</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { background-color: #f4f6f9; }</style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card p-4 shadow border-0">
                    <h3 class="mb-4 text-primary"><i class="fas fa-edit me-2"></i>Modifier l'article</h3>
                    <?= $message ?>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Titre de l'article</label>
                            <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($article['titre']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Catégorie</label>
                            <select name="id_categorie" class="form-select" required>
                                <option value="">-- Choisir une catégorie --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($article['id_categorie'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Contenu</label>
                            <textarea name="contenu" rows="6" class="form-control" required><?= htmlspecialchars($article['contenu']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Image actuelle</label>
                            <div>
                                <?php if (!empty($article['image'])): ?>
                                    <img src="<?= htmlspecialchars($article['image']) ?>" alt="Aperçu" width="120" class="rounded mb-2">
                                <?php else: ?>
                                    <p class="text-muted">Aucune image</p>
                                <?php endif; ?>
                            </div>
                            <label class="form-label fw-bold mt-2">Changer l'image d'illustration (optionnel)</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Statut</label>
                            <select name="statut" class="form-select" required>
                                <option value="publie" <?= ($article['statut'] == 'publie') ? 'selected' : '' ?>>Publié</option>
                                <option value="brouillon" <?= ($article['statut'] == 'brouillon') ? 'selected' : '' ?>>Brouillon</option>
                                <option value="archive" <?= ($article['statut'] == 'archive') ? 'selected' : '' ?>>Archivé</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="gestion_publications.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Retour</a>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>