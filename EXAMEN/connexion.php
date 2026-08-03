<?php
$host = 'mysql-tufurahi-amijaytufurahingaly-e45d.j.aivencloud.com';
$db   = 'defaultdb';
$user = 'avnadmin';
$pass = 'VOTRE_MOT_DE_PASSE_AIVEN'; // Le mot de passe que vous avez récupéré ou révélé tout à l'heure
$port = '20605';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8";
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>
