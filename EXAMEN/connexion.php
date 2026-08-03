<?php
$host = 'mysql-tufurahi-amijaytufurahingaly-e45d.j.aivencloud.com';
$port = '20605';
$dbname = 'defaultdb';
$username = 'avnadmin';
$password = 'AVNS_8uzgGlJPaHIZTsbFrZ-';

try {
    // Connexion PDO avec prise en compte du port et du charset
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Configuration des options pour afficher les erreurs si besoin
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // En cas d'erreur de connexion
    die("Erreur de connexion : " . $e->getMessage());
}
?>
