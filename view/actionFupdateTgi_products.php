<?php
use models\Grid\Grid;
use models\DB\DB;

DB::init([
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'my_teatralgioia'
        ]);

foreach($_POST[$_GET['r']] as $key => $value){
    $set[]      = $key;
    $values[]    = $value;
}

$result = DB::update([
    'set' => $set,
    'value' => $values,
    'tables' => [
        $_GET['r']
    ],
    'referenced' => [
        'table'=>'tgi_categories',
        
    ],
    'pk' => [
        'id' => 'id',
        'value' => $_GET['id']
    ]
]);

if($result!=false){ ?>
    <div class="btn-success">
        Il record è stato correttamente modificato!
    </div> <br />
    
    <?php
    $_GET['r'] = "products";
    Grid::view([
        'tables' => [
            'tgi_products',
        ],
        'columns' => [
            'code',
            'name',
            'quantity',
            'category'
        ],
        'actions' => true,
    ]);
    ?>
    
<?php }else{ ?>
    <div class="btn-success">
        Il record non è stato correttamente modificato!
    </div>
<?php } ?>
