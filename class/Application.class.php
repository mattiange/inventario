<?php
namespace models\Application;

use models\Pager\Pager;
use models\DB\DB;

abstract class Application{
    /**
     * 
     */
    private function layouts(){
        return require './view/templates/main.php';
    }

    /**
     * 
     */
    private function init(){
        self::layouts();
    }
    
    public function content(){
        if(isset($_GET['r'])) $r = $_GET['r'];
        else $r = null;
        if(isset($_GET['a'])) $a = $_GET['a'];
        else $a = null;
        
        Pager::pages($r, $a);
    }
    
    /**
     * 
     */
    public function run(){
        self::init();
    }
}