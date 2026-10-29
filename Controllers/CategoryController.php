<?php

require '../Config/DBconnection.php';
require '../Model/Category.php';
class CategoryController{
    private $db;

    public function __construct(){
        $this->db = new DB();
    }
function getAll(){
    $sql = 'select categories from nexora';
    $stmt = $this->db->run_query($sql);
    $categories = $stmt->fetchAll();
    return $categories;
}

function getCategoryProducts(){
    
}


}



?>