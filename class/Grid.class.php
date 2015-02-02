<?php
namespace models\Grid;

use models\DB\DB;
use Exception;

abstract class Grid{
    private function pk($tables){
        $query = "SHOW INDEX FROM $tables WHERE Key_name = 'PRIMARY'";
        $resultset = DB::query($query);
        
        return [
            'COLUMN NAME'   => $resultset[0]['Column_name'],
            'TABLE NAME'    => $resultset[0]['Table'],
        ];
    }
    
    /**
     * 
     * 
     * @param String $tables
     * @return Array
     */
    private function fk($tables){
            $query = "SELECT COLUMN_NAME,
                            TABLE_NAME,
                            REFERENCED_TABLE_NAME,
                            REFERENCED_COLUMN_NAME
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                        WHERE TABLE_NAME = '$tables' 
                        AND REFERENCED_TABLE_NAME is not null";
            $resultset = DB::query($query);
            
            return [
                'COLUMN_NAME'           => $resultset[0]['COLUMN_NAME'],
                'TABLE_NAME'            => $resultset[0]['TABLE_NAME'],
                'REFERENCED_COLUMN_NAME' => $resultset[0]['REFERENCED_COLUMN_NAME'],
                'REFERENCED_TABLE_NAME' => $resultset[0]['REFERENCED_TABLE_NAME'],
            ];
    }
    
    /**
     * 
     * 
     * @param type $config
     * @throws Exception
     */
    private function configuration($config){
        self::init();
        
        if(!isset($config['tables'])) throw new Exception('Argument \'tables\' doesn\'t exist');
        else if($config['tables'] == '') throw new Exception('Argument \'tables\' is empty');
        else if(count($config['tables'])==0) throw new Exception('Argument \'tables\' is empty');
        
        if(!isset($config['columns'])) throw new Exception('Argument \'columns\' doesn\'t exist');
        else if($config['columns'] == '') throw new Exception('Argument \'columns\' is empty');
        else if(count($config['columns']) == 0) throw new Exception('Argument \'columns\' is empty');
    }

    public function init(){
        DB::init([
                    'host' => 'localhost',
                    'username' => 'root',
                    'password' => '',
                    'database' => 'my_teatralgioia'
                ]);
    }
    
    public function table($config){
        self::configuration($config);
        
        $tables  = implode(",", $config['tables']);
        
        $pk = self::pk($tables);
        $fk = self::fk($tables);
        $pk_id = strtoupper($pk['COLUMN NAME']);
        
        $columns = $pk['TABLE NAME'].".".$pk['COLUMN NAME']." AS '".  strtoupper($pk['COLUMN NAME'])."'";
        $columns .= ",".implode(",", $config['columns']);
        $actions = ($config['actions']==true)?true:false;
        
        $column_name            = $fk['COLUMN_NAME'];
        $table_name             = $fk['TABLE_NAME'];
        $referenced_table_name  = $fk['REFERENCED_TABLE_NAME'];
        $referenced_column_name = $fk['REFERENCED_COLUMN_NAME'];
        
        $columns .= ", ".$column_name;
        $tables  .= ", ".$referenced_table_name;
        
        $query = "SELECT ".$columns
                        ." FROM ".$tables
                        ." WHERE ".$table_name.".".$column_name."=".$referenced_table_name.".".$referenced_column_name;
        
        
        $rows = DB::query($query);
        
        $flag = false;
        echo "<table class='table table-striped table-bordered'>\n";
        echo "\t<tr>\n";
        //HEADER
        foreach ($rows as $key => $value){
            foreach ($value as $columnKey => $column){
                foreach ($config['columns'] as $key1 => $value1){
                    if($columnKey == $pk_id && !$flag){
                        echo "\t\t<td>\n";
                        echo ucfirst($columnKey);
                        echo "\t\t</td>\n";
                        
                        $flag = true;
                    }
                    if($columnKey == $value1){
                        echo "\t\t<th>\n";
                        echo ucfirst($columnKey);
                        echo "\t\t</th>\n";
                    }
                }
            }
            
            break;
        }
        $flag = false;
        if($actions) echo "\t\t<th>&nbsp;</th>\n";
        echo "\t</tr>\n";
        foreach ($rows as $key => $value){
            echo "\t<tr>\n";
            foreach ($value as $rowKey => $row){
                foreach ($config['columns'] as $key1 => $value1){
                    if($rowKey == $pk_id && !$flag){
                        echo "\t\t<td>\n";
                        echo $row;
                        echo "\t\t</td>\n";
                        
                        $flag = true;
                    }
                    
                    if($rowKey == $value1){
                        echo "\t\t<td>\n";
                        echo $row;
                        echo "\t\t</td>\n";
                    }
                }
                
                $flag = false;
            }
            if($actions){
                $update = "?a=update&r=".$_GET['r']."&".$referenced_column_name."=".$rows[$key][$pk_id]."&".$column_name."=".$rows[$key][$column_name];
                $delete = "?a=delete&r=".$_GET['r']."&".$referenced_column_name."=".$rows[$key][$pk_id]."&".$column_name."=".$rows[$key][$column_name];
                $list   = "?a=view&r=".$_GET['r']."&".$referenced_column_name."=".$rows[$key][$pk_id]."&".$column_name."=".$rows[$key][$column_name];
                
                echo "\t\t<th>\n";
                echo "\t\t\t<a href='".$list."' title='Update'><span class='glyphicon-eye-open glyphicon'></span></a>";
                echo "\t\t\t<a href='".$delete."' title='Update'><span class='glyphicon-trash glyphicon'></span></a>";
                echo "\t\t\t<a href='".$update."' title='Update'><span class='glyphicon-pencil glyphicon'></span></a>";
                echo "\t\t</th>\n";
            }
            echo "\t</tr>\n";
        }
        echo "</table>\n";
    }
    
    /**
     * 
     * 
     * @param Array $config
     */
    public function view($config){
        self::configuration($config);
        
        $tables  = implode(",", $config['tables']);
        
        $pk = self::pk($tables);
        $fk = self::fk($tables);
        $pk_id = strtoupper($pk['COLUMN NAME']);
        
        $columns = $pk['TABLE NAME'].".".$pk['COLUMN NAME']." AS '".  strtoupper($pk['COLUMN NAME'])."'";
        $columns .= ",".implode(",", $config['columns']);
        $actions = ($config['actions']==true)?true:false;
        
        $column_name            = $fk['COLUMN_NAME'];
        $table_name             = $fk['TABLE_NAME'];
        $referenced_table_name  = $fk['REFERENCED_TABLE_NAME'];
        $referenced_column_name = $fk['REFERENCED_COLUMN_NAME'];
        
        $columns .= ", ".$column_name;
        $tables  .= ", ".$referenced_table_name;
        
        $query = "SELECT ".$columns
                        ." FROM ".$tables
                        ." WHERE ".$table_name.".".$column_name."=".$referenced_table_name.".".$referenced_column_name.
                                    " AND ".$table_name.".".$column_name." = ".$_GET[$column_name].
                                    " AND ".$table_name.".".$referenced_column_name." = ".$_GET[$referenced_column_name];
        
        
        $rows = DB::query($query);
        
        $update = "?a=update&r=".$_GET['r']."&".$referenced_column_name."=".$rows[0][$pk_id]."&".$column_name."=".$rows[0][$column_name];
        $delete = "?a=delete&r=".$_GET['r']."&".$referenced_column_name."=".$rows[0][$pk_id]."&".$column_name."=".$rows[0][$column_name];
                
        echo "<a href='".$update."' class='btn btn-primary'>Aggiorna</a>";
        echo "<a href='".$delete."' class='btn btn-danger'>Elimina</a>";
        
        $flag = false;
        
        foreach ($rows as $key => $value){
            echo "<table class='table table-striped table-bordered detail-view'>\n";
            foreach ($value as $column => $row){
                
                foreach ($config['columns'] as $key => $value){
                    if($pk_id==$column && !$flag){
                        echo "\t<tr>\n";
                        
                        echo "\t\t<th>\n";
                        echo ucfirst($column);
                        echo "\t\t</th>\n";
                        
                        echo "\t\t<td>\n";
                        echo $row;
                        echo "\t\t</td>\n";
                        
                        echo "\t</tr>\n";
                        
                        $flag = true;
                    }else if($value == $column){
                        echo "\t<tr>\n";
                        
                        echo "\t\t<th>\n";
                        echo ucfirst($column);
                        echo "\t\t</th>\n";
                        
                        echo "\t\t<td>\n";
                        echo $row;
                        echo "\t\t</td>\n";
                        
                        echo "\t</tr>\n";
                    }
                }
            }
            echo "</table>\n";
        }
    }
    
    /**
     * 
     * 
     * @param type $config
     */
    public function update($config){
        //Controllo la configurazione 
        self::configuration($config);
        
        $common_columns = [];
        
        //Tabelle
        $tables  = implode(",", $config['tables']);
        $columns = implode(",", $config['columns']);
        
        //Recupero le informazioni su eventuali tabelle collegate
        $pk = self::pk($tables);//Individuazione della chiave primaria
        $fk = self::fk($tables);//Individuazione delle informazioni sulla chiave esterna
        
        
        if(count($fk)!=0){
            //Seleziono le colonne delle tabelle collegate
            $my_columns         = DB::columns($config['tables']);
            $referenced_columns = DB::columns([
                    $fk['REFERENCED_TABLE_NAME']
                ]);

            //Verifico se le colonne nei parametri soon tutte della tabella principale
            foreach ($config['tables'] as $keytable => $valuetable){
                foreach ($my_columns[$valuetable] as $keyMy => $valueMY){
                    foreach ($config['columns'] as $key => $column){
                        if($column == $valueMY){
                            $common_columns[] = $valueMY;
                        }
                    }
                }
            }
            //Trovo le colonne delle tabelle collegate
            foreach ($referenced_columns[$fk['REFERENCED_TABLE_NAME']] as $keyRF => $valueRF){
                foreach ($config['columns'] as $key => $column){
                    if($column == $valueRF){
                        $rf_columns[] = $valueRF;
                    }
                }
            }
            //Ottengo i valori e gli id per le colonne delle tabelle collegate
            $query = "SELECT ";
            $query .= $fk['REFERENCED_COLUMN_NAME'];
            foreach ($rf_columns as $key => $value){
                $query .= ", $value";
                $rf_values[] = "";
            }
            $query .= " FROM ".$fk['REFERENCED_TABLE_NAME'];
            //Eseguo la query
            $rf_values = DB::query($query);
            foreach ($rf_values as $key => $values){
                foreach ($rf_columns as $key1 => $value1){
                    $options[$values[$fk['REFERENCED_COLUMN_NAME']]] = $values[$value1];
                }
            }
        }
        
        //Seleziono il record da modificare
        $where = " WHERE ";
        if(count($fk) != 0) {
            $tables .= ", ".$fk['REFERENCED_TABLE_NAME'];
            $where  .= $pk['TABLE NAME'].".";
        }
        $where .= $pk['COLUMN NAME']."=".$_GET[$pk['COLUMN NAME']];
        
        $query  = "SELECT ";
        $query .= $pk['TABLE NAME'].".".$pk['COLUMN NAME'].", ";
        $query .= $columns." FROM $tables ".$where;
        $values = DB::query($query);
        
        //FLAG
        $added       = [];
        $pk_flag     = false;
        $flag_insert = false;
        
        //Link per l'upload dei dati
        $link  = "?";
        $link .= "a=fupdate&r=";
        $link .= $pk['TABLE NAME'];
        $link .= "&".$pk['COLUMN NAME']."=";
        $link .= $_GET[$pk['COLUMN NAME']];
        if(count($fk) != 0){
            $link .= "&";
            $link .= $fk['COLUMN_NAME'];
            $link .= "=";
            $link .= $_GET[$fk['COLUMN_NAME']];
        }/////
        
        //Creo la lista da aggiornare
        echo "<div class='products-update'>\n";
        echo "\t<div class='products-form'>\n";
        echo "\t\t<form action='".$link."' method='post'>\n";
        $vars = "NO";
        foreach ($values as $key => $values){
            foreach ($values as $column => $value){
                foreach ($config['columns'] as $k => $v){
                    if(!$pk_flag){
                        echo "\t\t\t<div class='form-group field-products-code required'>\n";
                        echo "\t\t\t\t<label class='control-label'>".strtoupper($pk['COLUMN NAME'])."</label>\n";
                        echo "\t\t\t\t<input type='text' readonly class='form-control' value='".$value."' />\n";
                        echo "\t\t\t</div>\n";
                        
                        $pk_flag = true;
                    }else if($column == $v && !in_array($v, $added)){
                        echo "\t\t\t<div class='form-group field-products-code required'>\n";
                        echo "\t\t\t\t<label class='control-label'>".ucfirst($column)."</label>\n";
                        //echo "\t\t\t\t<input type='text' class='form-control' value='".$value."' />\n";
                        foreach ($rf_columns as $rf_columns_key => $rf_columns_value){
                            if($rf_columns_value == $column){
                                echo "<select class='form-control' name='".$pk['TABLE NAME']."[".$fk['COLUMN_NAME']."]'>\n";
                                foreach ($options as $option => $value){
                                    echo "<option value='$option'>$value</option>";
                                    
                                    $flag_insert = true;
                                }
                                echo "</select>\n";
                            }
                        }
                        echo "\t\t\t</div>\n";
                        
                        if(!$flag_insert){
                            echo "\t\t\t<div class='form-group field-products-code required'>\n";
                            echo "\t\t\t\t<input type='text' class='form-control' name='".$pk['TABLE NAME']."[".$column."]' value='".$value."' />\n";
                            echo "\t\t\t</div>\n";
                        }
                        
                        
                        $added[] = $v;
                    }
                }
            }
        }
        echo "\t\t\t<div class='form-group'>\n";
        echo "\t\t\t\t<button type='submit' class='btn btn-primary'>Aggiorna</button>\n";
        echo "\t\t\t</div>\n";
        echo "\t\t</form>\n";
        echo "\t</div>\n";
        echo "</div>\n";
    }
}