<?php
include_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Categorie.php';

class CategorieC
{
    // Ajouter une catégorie
    public function ajouterCategorie($categorie)
    {
        $sql = "INSERT INTO Categorie (nom_categorie, description) 
                VALUES (:nom, :type)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $categorie->getNom(),
                'type' => $categorie->getType()
            ]);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // Afficher toutes les catégories
    public function afficherCategories()
    {
        $sql = "SELECT * FROM Categorie";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Supprimer une catégorie par ID
    public function supprimerCategorie($id)
    {
        $sql = "DELETE FROM Categorie WHERE id_categorie = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Récupérer une catégorie par ID
    public function recupererCategorie($id)
    {
        $sql = "SELECT * FROM Categorie WHERE id_categorie = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return new Categorie($row['id_categorie'], $row['nom_categorie'], $row['description']);
            }
            return null;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // Modifier une catégorie
    public function modifierCategorie($categorie, $id)
    {
        $sql = "UPDATE Categorie SET 
                nom_categorie = :nom, 
                description = :type 
                WHERE id_categorie = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $categorie->getNom(),
                'type' => $categorie->getType(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }
    public function getAllCategories() {
        $sql = "SELECT * FROM Categorie";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $rows = $query->fetchAll(PDO::FETCH_ASSOC);
            $categories = [];
            foreach ($rows as $row) {
                $categories[] = new Categorie($row['id_categorie'], $row['nom_categorie'], $row['description']);
            }
            return $categories;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return [];
        }
    }
    public function getCategories() {
        $sql = "SELECT * FROM Categorie";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }
    
}
?>
