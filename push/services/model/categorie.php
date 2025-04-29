
<?php
class Categorie{
    private string $type;
    private int $id;
    private string $nom;
    
    public function __construct($id = null,$nom = null, $type = null)
    {
        $this->id = (int)$id;
        $this->nom = $nom ?? '';
        $this->type = $type ?? '';
    }
    public function getNom()
    {
        return $this->nom;
    }
    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }
    public function getId()
    {
         return $this->id;
    }
    public function setId($id)
    {
         $this->id = (int)$id;
         return $this;
    }
    public function getType()
    {
         return $this->type;
    }
    public function setType($type)
    {
         $this->type = $type;
         return $this;
    }
}    
?>