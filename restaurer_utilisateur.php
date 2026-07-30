<?php
session_start();
require_once 'connexion.php';
require_once 'securite.php';

exigerAdmin($pdo);

if (isset($_GET['id'])) {
    $id_historique = $_GET['id'];

    // 1. Récupérer l'utilisateur dans la table historique
    $stmt = $pdo->prepare("SELECT * FROM historique WHERE id = ?");
    $stmt->execute([$id_historique]);
    $user = $stmt->fetch();

    if ($user) {
        // 2. Remettre l'utilisateur dans la table principale 'utilisateurs' (sans la colonne password absente)
        $insert = $pdo->prepare("INSERT INTO utilisateurs (nom, email, role) VALUES (?, ?, ?)");
        $insert->execute([$user['nom'], $user['email'], $user['role'] ?? 'lecteur']);

        // 3. Supprimer l'entrée de la table historique
        $del = $pdo->prepare("DELETE FROM historique WHERE id = ?");
        $del->execute([$id_historique]);

        enregistrerLog($pdo, "Restauration de l'utilisateur : " . $user['nom']);
        header('Location: historique.php?success=restaure');
        exit();
    }
}

header('Location: historique.php');
exit();