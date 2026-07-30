<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function verifierConnexion() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: connexion_vue.php?error=non_connecte');
        exit();
    }
}

function obtenirRole() {
    return $_SESSION['user_role'] ?? 'lecteur';
}

// Fonction d'expert pour enregistrer les actions dans les logs
function enregistrerLog($pdo, $action) {
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("INSERT INTO logs_activites (id_utilisateur, action) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $action]);
    }
}

function exigerAdmin($pdo = null) {
    verifierConnexion();
    if (obtenirRole() !== 'admin') {
        if ($pdo) enregistrerLog($pdo, "Tentative d'accès non autorisé à la zone Admin");
        redrigerAccesRefuse("Zone ultra-sécurisée : Réservée exclusivement à l'Administrateur.");
    }
}

function exigerJournalisteOuAdmin() {
    verifierConnexion();
    $role = obtenirRole();
    if ($role !== 'admin' && $role !== 'journaliste') {
        redrigerAccesRefuse("Accès restreint : Seuls les journalistes et administrateurs peuvent gérer les publications.");
    }
}

function redrigerAccesRefuse($message) {
    echo '<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Accès Refusé - Tufurahi News</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="bg-light d-flex align-items-center justify-content-center vh-100">
        <div class="card shadow-lg border-0 p-5 text-center rounded-4" style="max-width: 500px;">
            <div class="mb-3 text-danger"><i class="fas fa-shield-alt fa-3x"></i></div>
            <h3 class="fw-bold text-dark">Accès Interdit</h3>
            <p class="text-muted my-3">'.htmlspecialchars($message).'</p>
            <a href="tableau_de_bord.php" class="btn btn-dark rounded-pill py-2"><i class="fas fa-arrow-left me-2"></i>Retour au tableau de bord</a>
        </div>
    </body>
    </html>';
    exit();
}
?>