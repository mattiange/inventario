<?php
namespace models\Pager;

use Exception;

abstract class Pager{
    public static $PAGE_PATH = "";
    
    /**
     * 
     * 
     * @param String $r
     * @param String $a
     * @return null
     */
    public function pages($r, $a=null){
        self::$PAGE_PATH = basename(__DIR__).'/../view/';
        
        if($r==null) $function = "actionIndex";
        else $function = "action".ucfirst($r);
        
        if($a!=null){
            $function = "action".ucfirst($a).ucfirst($r);
        }
        if($a == "fupdate"){
            
            /*if(!isset($_REQUEST['table'])){
                throw new Exception("Form error");
            }
            $table = $_GET['table'];
            
            self::update($_POST[$table], $table);*/
            
            //return;
        }
        
        self::$function();
    }
    
    
    /*private function update($values, $table){
        echo "<pre>";
        print_r($values);
        echo "</pre>";
        
        $i = 0;
        
        $query =  "UPDATE TABLE $table";
        $query .= " SET ";
        foreach ($values as $key => $value){
            $query .= $key."='".$value."'";
            if($i<count($values)-1) $query .= ",";
            
            $i ++;
        }
        $query .= " WHERE ".$_GET['pk']."=".$_GET[$_GET['pk']];
        
        echo $query;
    }*/
    
    /**
     * 
     * 
     * @param type $page
     */
    private function render($page){
        include_once self::$PAGE_PATH.$page.'.php';
    }
    
    /**
     * 
     */
    public function actionIndex(){
         self::render('index');
    }
    
    /*ADD PERSONAL ACTION METHOD*/
    /**
     * 
     */
    public function actionProducts(){
        self::render('products');
    }
    /**
     * 
     */
    public function actionUpdateProducts(){
        self::render('updateProducts');
    }
    /**
     * 
     */
    public function actionDeleteProducts(){
        self::render('deleteProducts');
    }
    /**
     * 
     */
    public function actionViewProducts(){
        self::render('viewProducts');
    }
    
    /**
     * 
     */
    public function actionFupdateTgi_products(){
        self::render('actionFupdateTgi_products');
    }
}