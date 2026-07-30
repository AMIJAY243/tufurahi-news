<?php
require_once 'connexion.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $role = $_POST['role']; // admin, journaliste, lecteur

    if (!empty($nom) && !empty($email) && !empty($mot_de_passe) && !empty($role)) {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $message = "<div class='alert alert-danger'>Cet email est déjà utilisé.</div>";
        } else {
            // Hachage sécurisé du mot de passe
            $passwordHash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            $insert = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
            if ($insert->execute([$nom, $email, $passwordHash, $role])) {
                $message = "<div class='alert alert-success'>Inscription réussie ! <a href='connexion_vue.php'>Connectez-vous ici</a>.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Erreur lors de l'inscription.</div>";
            }
        }
    } else {
        $message = "<div class='alert alert-warning'>Veuillez remplir tous les champs.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Tufurahi News</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Créer un compte</h3>
                        <?= $message ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Nom complet</label>
                                <input type="text" name="nom" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Adresse Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mot de passe</label>
                                <input type="password" name="mot_de_passe" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rôle</label>
                                <select name="role" class="form-select" required>
                                    <option value="lecteur">Lecteur</option>
                                    <option value="journaliste">Journaliste</option>
                                    <option value="admin">Administrateur</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                        </form>
                        <div class="mt-3 text-center">
                            <a href="connexion_vue.php">Déjà un compte ? Connectez-vous</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>