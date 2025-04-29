<?php

class Recommendation
{
    private $id;
    private $id_user;
    private $id_service;
    private $discounted_price;

    public function __construct($id_user, $id_service, $discounted_price = null, $id = null)
    {
        $this->id = $id;
        $this->id_user = $id_user;
        $this->id_service = $id_service;
        $this->discounted_price = $discounted_price;
    }

    public function getId() { return $this->id; }
    public function getIdUser() { return $this->id_user; }
    public function getIdService() { return $this->id_service; }
    public function getDiscountedPrice() { return $this->discounted_price; }

    public function setId($id) { $this->id = $id; }
    public function setIdUser($id_user) { $this->id_user = $id_user; }
    public function setIdService($id_service) { $this->id_service = $id_service; }
    public function setDiscountedPrice($discounted_price) { $this->discounted_price = $discounted_price; }
}
?>