<?php
include_once "../../../Controller/categorieC.php";

$categorieC = new CategorieC();

if (isset($_GET['id'])) {
    $idCategorie = $_GET['id'];
    $categorieC->supprimerCategorie($idCategorie);
    header("Location: catservices.php");
    exit();
}
?>
