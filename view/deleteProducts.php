<?php
use models\DB\DB;

DB::init([
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'my_teatralgioia'
        ]);

$result = DB::delete([
    'tables' => [
        "tgi_products"
    ],
    'pk' => [
        'id' => 'id',
        'value' => $_GET['id']
    ]
]);

if($result!=false){ ?>
    <div class="btn-success">
        Il record è stato correttamente eliminato!
    </div> <br />
    
<?php }else{ ?>
    <div class="btn-success">
        Il record non è stato correttamente eliminato!
    </div>
<?php } ?>
