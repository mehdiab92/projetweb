<?php
include_once "../../../Controller/serviceC.php";  // Assure-toi d'inclure le bon fichier contrôleur pour les services

$serviceC = new ServiceC();  // Utilise la classe ServiceC pour gérer les services

if (isset($_GET['id'])) {  // Vérifie si l'ID du service est passé en paramètre GET
    $idService = $_GET['id'];  // Récupère l'ID du service à supprimer
    $serviceC->supprimerService($idService);  // Appelle la méthode pour supprimer le service
    header("Location: catservices.php");  // Redirige vers la page de gestion des services
    exit();  // Arrête l'exécution du script
}
?>

