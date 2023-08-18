<?php

require_once "src/locomotive.php";
require_once "src/carriage.php";
require_once "src/train.php";

function get_data()
{
    $l1 = new Locomotive(
        tractive_force: 100, // tons
        empty_weight: 10, // tons
        length: 15, // meter
        type: Locomotive::TYPE_ELEKTRISCH,
        max_passengers_capacity: 2,
        max_load_capacity: 15,
        manufacturer: 'Test Loco El',
        release_year: 1997
    );
    $l2 = new Locomotive(
        tractive_force: 200, //tons
        empty_weight: 20, // tons
        length: 25, // meter
        type: Locomotive::TYPE_DIESEL,
        max_passengers_capacity: 3,
        max_load_capacity: 25,
        manufacturer: 'Test Loco Di',
        release_year: 1998
    );

    $c1 = new Carriage(
        empty_weight: 10, // tons
        length: 15, // meter
        type: Carriage::TYPE_PERSONEN,
        max_passengers_capacity: 20,
        max_load_capacity: 10,
        manufacturer: 'Test Carriage P',
        release_year: 1998
    );
    $c2 = new Carriage(
        empty_weight: 20, // tons
        length: 20, // meter
        type: Carriage::TYPE_SCHLAFEN,
        max_passengers_capacity: 50,
        max_load_capacity: 15,
        manufacturer: 'Test Carriage Sc',
        release_year: 1998
    );
    $c3 = new Carriage(
        empty_weight: 20, // tons
        length: 20, // meter
        type: Carriage::TYPE_SCHLAFEN,
        max_passengers_capacity: 50,
        max_load_capacity: 15,
        manufacturer: 'Test Carriage Sc',
        release_year: 1998
    );
    $c4 = new Carriage(
        empty_weight: 10, // tons
        length: 15, // meter
        type: Carriage::TYPE_SPEISE,
        max_passengers_capacity: 0,
        max_load_capacity: 0,
        manufacturer: 'Test Carriage Sp',
        release_year: 1998
    );
    $c5 = new Carriage(
        empty_weight: 5, // tons
        length: 15, // meter
        type: Carriage::TYPE_GUETER,
        max_passengers_capacity: 0,
        max_load_capacity: 100,
        manufacturer: 'Test Carriage Sp',
        release_year: 2003
    );

    return [
        'l1' => $l1,
        'l2' => $l2,
        'c1' => $c1,
        'c2' => $c2,
        'c3' => $c3,
        'c4' => $c4,
        'c5' => $c5,
        'locomotives' => [$l1, $l2],
        'carriages' => [$c1, $c2, $c3, $c4, $c5]
    ];
}


$data = get_data();
$train_1 = new Train(50, 20, $data['locomotives'], $data['carriages']);
$train_1->form_train();
var_dump($train_1->train_parts);

// $train_2 = new Train(50, 20, $data['locomotives'], $data['carriages']);
// $train_2->form_train();
// var_dump($train_2->train_parts);
