<?php
namespace models\DB;

use Exception;
use mysqli;

abstract class DB{
    /**
     * Database statement
     *
     * @var Object
     */
    private static $mysqli;
    /**
     * Host of database
     * 
     * @var String
     */
    private static $host;
    /**
     * Username of database
     * 
     * @var String
     */
    private static $username;
    /**
     * Password of database
     * 
     * @var String
     */
    private static $password;
    /**
     * Name of database
     * 
     * @var String
     */
    private static $dbname;
    /**
     * Port of database 
     * 
     * @var String
     */
    private static $port = null;
    /**
     * 
     *
     * @var String
     */
    private static $socket = "";
    
    /**
     * 
     * @return boolean
     */
    private function connect(){
        if(!isset(self::$mysqli)){
            self::$mysqli = new mysqli(
                    self::$host, 
                    self::$username, 
                    self::$password, 
                    self::$dbname, 
                    self::$port, 
                    self::$socket);
        }else return false;
        
        
        
        return true;
    }
    
    /**
     * Trova le colonne delle tabelle passate 
     * come array nel parametro
     * 
     * @param array $tables
     * Array delle tabelle delle quali si vuole
     * ricavare le colonne
     * 
     * @return type
     * Colonne delle tabelle passate come parametro.
     * La struttura del result è: <br />
     * Array(<br />
     *      [nome_tabella1] => Array(<br />
     *          [colonna1] => valore1<br />
     *          [colonna2] => valore2<br />
     *          .....<br />
     *          [colonnaN] => valoreN<br />
     *      ),<br />
     *      [nome_tabella2] => Array(<br />
     *          [colonna1] => valore1<br />
     *          [colonna2] => valore2<br />
     *          .....<br />
     *          [colonnaN] => valoreN<br />
     *      ),<br />
     *      .......<br />
     *      [nome_tabellaN] => Array(<br />
     *          [colonna1] => valore1<br />
     *          [colonna2] => valore2<br />
     *          .....<br />
     *          [colonnaN] => valoreN<br />
     *      ),<br />
     * )<br />
     */
    public function columns(Array $tables){
        //Inizializzo le variabili
        $queries = [];
        $rows    = [];
        
        //Creo le query da eseguire
        foreach ($tables as $key => $value){
            $queries[] = "SELECT * FROM ".$value." LIMIT 1";
        }
        //Eseguo le query
        foreach ($queries as $key => $value){
            $rows[$tables[$key]] = self::query($value);
        }
        //Trovo le colonne
        foreach ($rows as $key => $value){
            foreach ($value as $key1 => $value1){
                foreach ($value1 as $column => $value2){;
                    $columns[$key][] = $column;
                }
            }
        }
        
        
        return $columns;//Colonne
    }
    
    /**
     * 
     * @param array $config
     * @return type
     */
    public function update(Array $config){
        $i = 0;
        foreach ($config['tables'] as $key => $value){
            $query =  "UPDATE `$value` ";
            $query .= "SET ";
            foreach ($config['set'] as $kset => $vset){
                $query .= "`".$vset."`";
                $query .= "='";
                $query .= $config['value'][$kset];
                $query .= "'";
                if($i<(count($config['set'])-1)){
                    $query .= ", ";
                    
                    $i ++;
                }
            }
            $query .= " WHERE ";
            $query .= $config['pk']['id']."=";
            $query .= $config['pk']['value'];
            $query .=";";
            
            //Update row
            return self::freeQuery($query);
        }
    }
    
    
    public function delete(Array $config){
        $i = 0;
        foreach ($config['tables'] as $key => $value){
            $query = "DELETE FROM `$value` WHERE ".$config['pk']['id']."=".$config['pk']['value'];
            
            //Update row
            return self::freeQuery($query);
        }
    }
    
    /**
     * 
     * 
     * @param type $query
     * @return type
     * @throws Exception
     */
    private function freeQuery($query){
        $result = self::$mysqli->query($query);
        if($result) return $result;
        else throw new Exception("Impossible to execute query");
    }
    
    /**
     * 
     * 
     * @param type $resultset
     */
    private function queryTable($resultset){
        echo "<table border='1'>\n";
        echo "\t<tr>\n";
        //HEADER
        foreach ($resultset as $key => $value){
            foreach ($value as $columnKey => $column){
                echo "\t\t<th>\n";
                echo ucfirst($columnKey);
                echo "\t\t</th>\n";
            }
            
            break;
        }
        echo "\t</tr>\n";
        foreach ($resultset as $key => $value){
            echo "\t<tr>\n";
            foreach ($value as $rowKey => $row){
                echo "\t\t<td>\n";
                echo $row;
                echo "\t\t</td>\n";
            }
            echo "\t</tr>\n";
        }
        echo "</table>\n";
    }
    
    /**
     * 
     * 
     * @param type $resultset
     */
    private function queryList($resultset){
        
        foreach ($resultset as $key => $value){
            echo "<table border='1'>\n";
            foreach ($value as $column => $row){
                echo "\t<tr>\n";
                echo "\t\t<th>\n";
                echo ucfirst($column);
                echo "\t\t</th>\n";
                
                echo "\t\t<td>\n";
                echo $row;
                echo "\t\t</td>\n";
                echo "\t</tr>\n";
            }
            echo "</table>\n";
        }
    }

    /**
     * Query to execute
     * 
     * @param String $query
     * @return Array
     */
    public function queryContainer($query, $container = "table"){
        $containers = [
            'table',
            'list'
        ];
        $flag = false;
        
        foreach ($containers as $key => $value){
            if($container == $value){
                $flag = true;
                
                break;
            }
        }
        
        $resultset = self::query($query);
        if($flag){
            $function = "query".ucfirst($container);
        }else {
            $function = "queryTable";
        }
        
        self::$function($resultset);
    }

    /**
     * Query to execute
     * 
     * @param String $query
     * @return Array
     */
    public function query($query){
        $result = self::freeQuery($query);
        
        $resultset = [];
        while ($row = mysqli_fetch_assoc($result)){
            $resultset[] = $row; 
        }
        
        return $resultset;
    }


    
    /**
     * Database's configuration.
     * 
     * @param array $config
     *  - host => hostname e.g.: localhost
     *  - username => Username of database
     *  - password => Database's password
     *  - database => Name of database
     *  - port => Port to use
     *  - socket
     */
    public function init(Array $config){
        if(!isset($config['host'])) throw new Exception('host doesn\'t declared!');
        else self::$host = $config['host'];
        
        if(!isset($config['username'])) throw new Exception('username doesn\'t declared!');
        else self::$username = $config['username'];
        
        if(!isset($config['password'])) throw new Exception('password doesn\'t declared!');
        else self::$password = $config['password'];
        
        if(!isset($config['database'])) throw new Exception('database doesn\'t declared!');
        else self::$dbname = $config['database'];
        
        if(isset($config['port'])) self::$port = $config['port'];
        
        if(isset($config['socket'])) self::$socket = $config['socket'];
        
        //Connect the database
        self::connect();
    }
}