<?php
// api_dashboard.php — Retourne les statistiques du dashboard en JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once "connexion.php";

try {
    // 1. Nombre total de patients
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM patient");
    $total_patients = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 2. Nombre de médecins
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM medecin");
    $total_medecins = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 3. Rendez-vous du jour
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM rendez_vous 
        WHERE DATE(date_rdv) = CURDATE()");
    $rdv_aujourdhui = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 4. Alertes médicaments en rupture (nombre)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM medicament 
        WHERE quantite_stock <= seuil_alerte");
    $medicaments_alerte = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 5. Données pour le graphique (consultations par mois cette année)
    $stmt = $pdo->query("
        SELECT MONTH(date_consultation) as mois, COUNT(*) as total
        FROM consultation
        WHERE YEAR(date_consultation) = YEAR(CURDATE())
        GROUP BY MONTH(date_consultation)
        ORDER BY mois ASC
    ");
    $graphique_consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Liste des médicaments en rupture (pour l'affichage)
    $stmt = $pdo->query("
        SELECT nom_medicament, quantite_stock, seuil_alerte
        FROM medicament
        WHERE quantite_stock <= seuil_alerte
        ORDER BY quantite_stock ASC
        LIMIT 10
    ");
    $alertes_stock = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retourner toutes les données en JSON
    echo json_encode([
        "total_patients"          => $total_patients,
        "total_medecins"          => $total_medecins,
        "rdv_aujourdhui"          => $rdv_aujourdhui,
        "medicaments_alerte"      => $medicaments_alerte,
        "graphique_consultations" => $graphique_consultations,
        "alertes_stock"           => $alertes_stock,
    ]);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>