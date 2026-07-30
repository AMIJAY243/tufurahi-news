<?php
// Inclure votre fichier de connexion à la base de données
require_once 'connexion.php';

try {
    // Préparer la requête d'insertion (sans la date pour éviter les erreurs)
    $sql = "INSERT INTO articles (titre, contenu, statut) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    // Exécuter l'insertion avec les valeurs de test
    $stmt->execute([
        'Mon premier article archivé', 
        'Ceci est le contenu de test pour vérifier que les archives fonctionnent parfaitement.', 
        'archive'
    ]);

    echo "<h2 style='color: green; font-family: sans-serif; text-align: center; margin-top: 50px;'>
            Article inséré et archivé avec succès ! <br><br>
            <a href='archives.php'>Voir les archives</a>
          </h2>";

} catch (PDOException $e) {
    echo "Erreur lors de l'insertion : " . $e->getMessage();
}
?>