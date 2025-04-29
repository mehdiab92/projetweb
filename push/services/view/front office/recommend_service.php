<?php
include_once "../../config.php";
include_once "../../Controller/RecommendationC.php";
include_once "../../model/Recommendation.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_service'])) {
    $idService = $_POST['id_service'];
    $idUser = 1; // static for now

    $recommendationC = new RecommendationC();
    $recommendation = new Recommendation($idUser, $idService);

    if ($recommendationC->ajouterRecommendation($recommendation)) {
        header('Location: listetransport.php');
        exit();
    } else {
        echo "Failed to recommend service.";
    }
} else {
    echo "Invalid Request.";
}
?>