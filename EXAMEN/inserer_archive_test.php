<?php
require_once 'connexion.php';

try {
    // 1. On vérifie d'abord s'il y a au moins une publication dans la table 'publications'
    $stmt = $pdo->query("SELECT id FROM publications LIMIT 1");
    $pub = $stmt->fetch();

    if (!$pub) {
        die("Erreur : La table 'publications' est vide. Veuillez d'abord créer une publication/article sur votre site avant de tester les archives.");
    }

    $id_publication = $pub['id'];

    // 2. On insère une archive liée à cette publication
    $sql = "INSERT INTO archives (id_publication, date_archivage, motif) VALUES (?, NOW(), ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_publication, 'Archivage de test']);

    echo "<h2 style='color: green; font-family: sans-serif; text-align: center; margin-top: 50px;'>
            Archive de test ajoutée avec succès ! <br><br>
            <a href='archives.php'>Aller voir la page des archives</a>
          </h2>";

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>