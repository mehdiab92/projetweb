<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Service.php';

class ServiceC
{
    // Ajouter un service
    public function ajouterService($service) {
        $sql = "INSERT INTO Services (service_name, service_description, price, eco_friendly, id_categorie) 
                VALUES (:service_name, :service_description, :price, :eco_friendly, :id_categorie)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'service_name' => $service->getServiceName(),
                'service_description' => $service->getServiceDescription(),
                'price' => $service->getPrice(),
                'eco_friendly' => $service->getEcoFriendly(),
                'id_categorie' => $service->getIdCategorie()
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Afficher tous les services
    public function afficherServices()
    {
        $sql = "SELECT s.*, c.nom_categorie 
                FROM Services s
                LEFT JOIN Categorie c ON s.id_categorie = c.id_categorie";
        $db = config::getConnexion();
        try {
            $liste = $db->query($sql);
            return $liste->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    // Supprimer un service
    public function supprimerService($id)
    {
        $sql = "DELETE FROM Services WHERE id_service = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    // Récupérer un service par ID
    public function recupererService($id) {
        $sql = "SELECT * FROM Services WHERE id_service = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                return new Service(
                    $row['id_service'],
                    $row['service_name'],
                    $row['service_description'],
                    $row['price'],
                    $row['eco_friendly'],
                    $row['id_categorie']
                );
            }
            return null;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return null;
        }
    }

    // Modifier un service
    public function modifierService($service, $id) {
        $sql = "UPDATE Services SET 
                    service_name = :service_name,
                    service_description = :service_description,
                    price = :price,
                    eco_friendly = :eco_friendly,
                    id_categorie = :id_categorie
                WHERE id_service = :id";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'service_name' => $service->getServiceName(),
                'service_description' => $service->getServiceDescription(),
                'price' => $service->getPrice(),
                'eco_friendly' => $service->getEcoFriendly(),
                'id_categorie' => $service->getIdCategorie(),
                'id' => $id
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return false;
        }
    }

    // Récupérer tous les services avec leur catégorie
    public function getServicesWithCategorie() {
        $sql = "SELECT s.*, c.nom_categorie 
                FROM Services s
                JOIN Categorie c ON s.id_categorie = c.id_categorie";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // Récupérer les services par catégorie
    public function getServicesByCategorie($idCategorie) {
        $sql = "SELECT s.*, c.nom_categorie 
                FROM Services s
                JOIN Categorie c ON s.id_categorie = c.id_categorie
                WHERE s.id_categorie = :idCategorie";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['idCategorie' => $idCategorie]);
            return $query->fetchAll();
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }


    // Récupérer tous les services avec leur catégorie + prix personnalisé pour un utilisateur
public function getServicesWithDiscount($userId) {
    $db = config::getConnexion();
    try {
        $sql = "SELECT 
                    s.*, 
                    c.nom_categorie, 
                    usr.discounted_price
                FROM Services s
                JOIN Categorie c ON s.id_categorie = c.id_categorie
                LEFT JOIN User_Service_Recommendation usr 
                    ON usr.id_service = s.id_service AND usr.id_user = :userId";
        
        $query = $db->prepare($sql);
        $query->execute(['userId' => $userId]);
        return $query->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        echo 'Erreur: ' . $e->getMessage();
    }
}

// Get minimum and maximum prices
public function getMinMaxPrices($userId) {
    $db = config::getConnexion();
    try {
        $sql = "SELECT 
                    MIN(COALESCE(usr.discounted_price, s.price)) AS min_price, 
                    MAX(COALESCE(usr.discounted_price, s.price)) AS max_price
                FROM Services s
                LEFT JOIN User_Service_Recommendation usr 
                    ON usr.id_service = s.id_service AND usr.id_user = :userId";
        
        $query = $db->prepare($sql);
        $query->execute(['userId' => $userId]);
        return $query->fetch(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        echo 'Erreur: ' . $e->getMessage();
    }
}


}
?>