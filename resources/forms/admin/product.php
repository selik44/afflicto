<?php

$form = new stdClass();

$form->name = Former::text('name');
$form->slug = Former::text('slug')->help("As it appears in url's.");

$form->brand = Former::text('brand');
$form->model = Former::text('model');
$form->summary = Former::textarea('summary')->help("Short description in plain text.");


$form->weight = Former::number('weight');
$form->price = Former::number('price');
$form->in_price = Former::number('in_price');
$form->tax_percentage = Former::number('tax_percentage');
$form->stock = Former::number('stock');
$form->enabled = Former::checkbox('enabled');

$form->description = Former::textarea('description')->rows(8);

return $form;