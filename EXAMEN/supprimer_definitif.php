<?php
session_start();
require_once 'connexion.php';
require_once 'securite.php';

exigerAdmin($pdo);

if (isset($_GET['id'])) {
    $id_historique = $_GET['id'];

    // Supprimer définitivement de la table historique
    $del = $pdo->prepare("DELETE FROM historique WHERE id = ?");
    if ($del->execute([$id_historique])) {
        enregistrerLog($pdo, "Suppression définitive de l'utilisateur de la corbeille (ID historique : $id_historique)");
        header('Location: corbeille_utilisateurs.php?success=supprime_definitif');
        exit();
    }
}

header('Location: corbeille_utilisateurs.php');
exit();