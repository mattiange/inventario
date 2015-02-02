<?php
use models\Grid\Grid;

Grid::update([
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