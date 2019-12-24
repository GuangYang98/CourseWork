<?php
require('../model/database.php');
require('../model/order_db.php');
require('../model/user_db.php');
require('../model/day_db.php');

$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL) {
        $action = 'list_orders';
    }
}
// TODO: actions list_orders, changed_to_baked
if($action == 'list_orders'){
    try{
        $baked_orders = get_orders_baked($db);
        $preparing_orders = get_orders_preparing($db);
        include ('order_list.php');
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        include('../errors/database_error.php');
    }
}else if ($action == 'baked') {
    try{
        update_status($db);
        $baked_orders = get_orders_baked($db);
        $preparing_orders = get_orders_preparing($db);
        include ('order_list.php');
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        include('../errors/database_error.php');
    }
}