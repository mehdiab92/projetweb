<?php

class Service
{
    private $id_service;
    private $service_name;
    private $service_description;
    private $price;
    private $eco_friendly;
    private $id_categorie;

    // Constructeur
    public function __construct($id_service = null, $service_name, $service_description, $price, $eco_friendly, $id_categorie)
    {
        $this->id_service = $id_service;
        $this->service_name = $service_name;
        $this->service_description = $service_description;
        $this->price = $price;
        $this->eco_friendly = $eco_friendly;
        $this->id_categorie = $id_categorie;
    }

    // Getters
    public function getIdService()
    {
        return $this->id_service;
    }

    public function getServiceName()
    {
        return $this->service_name;
    }

    public function getServiceDescription()
    {
        return $this->service_description;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getEcoFriendly()
    {
        return $this->eco_friendly;
    }

    public function getIdCategorie()
    {
        return $this->id_categorie;
    }

    // Setters
    public function setServiceName($service_name)
    {
        $this->service_name = $service_name;
    }

    public function setServiceDescription($service_description)
    {
        $this->service_description = $service_description;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function setEcoFriendly($eco_friendly)
    {
        $this->eco_friendly = $eco_friendly;
    }

    public function setIdCategorie($id_categorie)
    {
        $this->id_categorie = $id_categorie;
    }
}
