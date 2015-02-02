<?php
use models\Grid\Grid;

Grid::table([
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