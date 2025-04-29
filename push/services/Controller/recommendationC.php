<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Recommendation.php';

class RecommendationC
{
    public function isServiceRecommended($userId, $serviceId) {
        $sql = "SELECT * FROM User_Service_Recommendation 
                WHERE id_user = :id_user AND id_service = :id_service";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_user' => $userId,
                'id_service' => $serviceId
            ]);
            return $query->fetch() !== false;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return false;
        }
    }

    public function ajouterRecommendation($recommendation) {
        $db = config::getConnexion();
    
        try {
            // First, count how many services the user has ALREADY recommended
            $countSql = "SELECT COUNT(*) FROM User_Service_Recommendation WHERE id_user = :id_user";
            $countQuery = $db->prepare($countSql);
            $countQuery->execute(['id_user' => $recommendation->getIdUser()]);
            $recommendationCount = $countQuery->fetchColumn();
    
            // Now fetch the original price of the service
            $serviceSql = "SELECT price FROM Services WHERE id_service = :id_service";
            $serviceQuery = $db->prepare($serviceSql);
            $serviceQuery->execute(['id_service' => $recommendation->getIdService()]);
            $serviceRow = $serviceQuery->fetch(PDO::FETCH_ASSOC);
    
            if (!$serviceRow) {
                throw new Exception("Service not found");
            }
    
            $originalPrice = $serviceRow['price'];
    
            // Calculate discount
            $discountedPrice = null;
            if ($recommendationCount + 1 >= 5) { // +1 because we're about to add a new recommendation
                $discountedPrice = $originalPrice * 0.9; // 10% discount
                $minimumPrice = $originalPrice * 0.7;    // Minimum 70% of original price
    
                if ($discountedPrice < $minimumPrice) {
                    $discountedPrice = $minimumPrice;
                }
            }
    
            // Insert the recommendation with the correct discounted_price
            $insertSql = "INSERT INTO User_Service_Recommendation (id_user, id_service, discounted_price)
                          VALUES (:id_user, :id_service, :discounted_price)";
            $insertQuery = $db->prepare($insertSql);
            $insertQuery->execute([
                'id_user' => $recommendation->getIdUser(),
                'id_service' => $recommendation->getIdService(),
                'discounted_price' => $discountedPrice
            ]);
    
            return true;
    
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return false;
        }
    }
    
    }             
?>