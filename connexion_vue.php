<?php
session_start();
require_once 'connexion.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    if (!empty($email) && !empty($mot_de_passe)) {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            // Enregistrement des sessions
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_role'] = $user['role'];

            // Redirection vers le tableau de bord
            header('Location: tableau_de_bord.php');
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Email ou mot de passe incorrect.</div>";
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
    <title>Connexion - Tufurahi News</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Connexion</h3>
                        <?= $message ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Adresse Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mot de passe</label>
                                <input type="password" name="mot_de_passe" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Se connecter</button>
                        </form>
                        <div class="mt-3 text-center">
                            <a href="inscription.php">Pas encore de compte ? S'inscrire</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>