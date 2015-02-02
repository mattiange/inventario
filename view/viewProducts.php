<?php
use models\Grid\Grid;

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