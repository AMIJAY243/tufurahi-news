<?php
session_start();
require_once 'connexion.php';
require_once 'securite.php';

// Sécurité experte : Exiger que l'utilisateur soit un administrateur
exigerAdmin($pdo);

$message = "";

// Traitement du changement de rôle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_utilisateur'], $_POST['nouveau_role'])) {
    $id_u = $_POST['id_utilisateur'];
    $nouveau_role = $_POST['nouveau_role'];

    // Empêcher l'administrateur de modifier son propre rôle par erreur
    if ($id_u == $_SESSION['user_id'] && $nouveau_role !== 'admin') {
        $message = "<div class='alert alert-danger'>Vous ne pouvez pas modifier votre propre rôle administrateur.</div>";
    } else {
        $updateRole = $pdo->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?");
        if ($updateRole->execute([$nouveau_role, $id_u])) {
            enregistrerLog($pdo, "Modification du rôle de l'utilisateur ID $id_u en '$nouveau_role'");
            $message = "<div class='alert alert-success'>Rôle mis à jour avec succès.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Erreur lors de la mise à jour du rôle.</div>";
        }
    }
}

// Suppression d'un utilisateur avec envoi automatique dans la Corbeille (table historique)
if (isset($_GET['supprimer'])) {
    $id_sup = $_GET['supprimer'];
    if ($id_sup == $_SESSION['user_id']) {
        $message = "<div class='alert alert-danger'>Vous ne pouvez pas supprimer votre propre compte administrateur.</div>";
    } else {
        // 1. Récupérer les informations de l'utilisateur avant suppression
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id_sup]);
        $user_to_delete = $stmt->fetch();

        if ($user_to_delete) {
            // 2. Insérer ses informations dans la table 'historique' (Corbeille)
            $logInsert = $pdo->prepare("INSERT INTO historique (user_id_original, nom, email, role) VALUES (?, ?, ?, ?)");
            $logInsert->execute([$user_to_delete['id'], $user_to_delete['nom'], $user_to_delete['email'], $user_to_delete['role'] ?? 'lecteur']);

            // 3. Supprimer l'utilisateur de la table active
            $del = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
            if ($del->execute([$id_sup])) {
                enregistrerLog($pdo, "Suppression et envoi en corbeille de l'utilisateur ID : $id_sup");
                header('Location: gestion_utilisateurs.php?success=supprime');
                exit();
            }
        }
    }
}

// Récupération de tous les utilisateurs
$utilisateurs = $pdo->query("SELECT id, nom, email, role FROM utilisateurs ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - Tufurahi News</title>
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
                <a href="tableau_de_bord.php" class="btn btn-outline-light btn-sm me-2"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
                <a href="deconnexion.php" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Quitter</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 my-4">
        <div class="mb-4">
            <h2><i class="fas fa-users-cog text-primary me-2"></i>Gestion des Utilisateurs et Rôles</h2>
            <p class="text-muted small mb-0">Modifiez les permissions et les accès des membres inscrits sur la plateforme.</p>
        </div>

        <?= $message ?>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'supprime'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Utilisateur supprimé et déplacé dans la corbeille avec succès.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card card-table p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7">
                        <tr>
                            <th class="py-3">ID</th>
                            <th class="py-3">Nom complet</th>
                            <th class="py-3">Adresse Email</th>
                            <th class="py-3">Rôle actuel</th>
                            <th class="py-3">Changer le rôle</th>
                            <th class="py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($utilisateurs) > 0): ?>
                            <?php foreach ($utilisateurs as $user): ?>
                                <tr>
                                    <td><strong>#<?= $user['id'] ?></strong></td>
                                    <td><?= htmlspecialchars($user['nom']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <?php if ($user['role'] == 'admin'): ?>
                                            <span class="badge bg-danger px-3 py-2">Administrateur</span>
                                        <?php elseif ($user['role'] == 'journaliste'): ?>
                                            <span class="badge bg-warning text-dark px-3 py-2">Journaliste</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary px-3 py-2">Lecteur</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="" class="d-flex align-items-center">
                                            <input type="hidden" name="id_utilisateur" value="<?= $user['id'] ?>">
                                            <select name="nouveau_role" class="form-select form-select-sm me-2 shadow-none" style="width: 140px;">
                                                <option value="lecteur" <?= ($user['role'] == 'lecteur') ? 'selected' : '' ?>>Lecteur</option>
                                                <option value="journaliste" <?= ($user['role'] == 'journaliste') ? 'selected' : '' ?>>Journaliste</option>
                                                <option value="admin" <?= ($user['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-dark" title="Appliquer le rôle"><i class="fas fa-check"></i></button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <a href="gestion_utilisateurs.php?supprimer=<?= $user['id'] ?>" class="btn btn-sm btn-light text-danger" title="Supprimer le compte" onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur et l\'envoyer dans la corbeille ?');"><i class="fas fa-trash-alt"></i></a>
                                        <?php else: ?>
                                            <span class="text-muted small">Compte actif</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Aucun utilisateur enregistré.</td>
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