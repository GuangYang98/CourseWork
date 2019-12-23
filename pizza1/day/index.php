<?php

require('../model/database.php');
require('../model/initial.php');
require('../model/day_db.php');
require('../model/order_db.php');

$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action==NULL){
        $action = 'day_list';
    }
}

if ($action == 'day_list'){
    try{
        $current_day = get_current_day($db);
        $get_orders = get_orders($db,$current_day[0]); 
        $current_day  = get_current_day($db);
        include ('day_list.php');
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
        include ('../errors/database_error.php');
        exit();
    }
}


if ($action == 'next_day') {
    try {
        update_current_day($db);
        $current_day = get_current_day($db);
        $get_orders = get_orders($db,$current_day[0]);
        include ('day_list.php');
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
        include ('../errors/database_error.php');
        exit();
    }
}

if ($action == 'initial_db') {
    try {
        initial_db($db);
        $get_orders = get_orders($db,$current_day);
        include ('day_list.php');
        header("Location: .");
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
        include ('../errors/database_error.php');
        exit();
    }
}

