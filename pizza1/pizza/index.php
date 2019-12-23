<?php

require('../model/database.php');
require('../model/user_db.php');
require('../model/topping_db.php');
require('../model/size_db.php');
require('../model/order_db.php');
require('../model/day_db.php');


$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL) {
        $action = 'welcome_page';
    }
}

if ($action == 'welcome_page') {
    try {
        //using filter_input is better than using _post in none value case.
        $username = filter_input(INPUT_GET, 'username');
        //if no if cluase, it will die when there is no username
        if ($username != NULL) {
            $get_personal_order = get_personal_order($db,$username);
        }
        $size_list = get_sizes($db);
        $topping_list= get_toppings($db);
        $user_list=get_users($db);
        include('student_welcome.php');
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
        include('../errors/database_error.php');
    }
}


else if ($action=='order_pizza'){
    try{
        $size_list = get_sizes($db);
        $topping_list= get_toppings($db);
        $meat_list=get_meat($db);
        $meatless_list=get_meatless($db);
        $user_list=get_users($db);
        include('order_pizza.php');
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
        include('../errors/database_error.php');
    }
 //finish order, turn to welcome page with the order in userid and a table    
}else if ($action=='finish'){    
    //no size or topping -->incomplete
    try {
        $username = filter_input(INPUT_POST, 'username');
        $size = filter_input(INPUT_POST, 'size');
        $meat = filter_input(INPUT_POST, 'meat');
        $meatless = filter_input(INPUT_POST, 'meatless');
        $topping = $meat . $meatless;
        

        $userid = get_userid_by_name($db, $username);
        $size_list = get_sizes($db);
        $topping_list= get_toppings($db);
        $user_list=get_users($db);
        include('student_welcome.php');
        add_order($db,$username,$size, $topping);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        include('../errors/database_error.php');
        exit();
    }
    header ("Location: .?username=$username");
} else if ($action == 'check_status') {
    try{
        $username = filter_input(INPUT_POST, 'username');
        $userid = get_userid_by_name($db, $username);
        $size_list = get_sizes($db);
        $topping_list= get_toppings($db);
        $user_list=get_users($db);
        include('student_welcome.php');  
    } catch (Exception $ex) {
        $error_message = $e->getMessage();
        include('../errors/database_error.php');
    }
    header ("Location: .?username=$username");
}else if ($action == 'acknow') {
    try{
        $username = filter_input(INPUT_POST, 'username');
        $userid = get_userid_by_name($db, $username);
        finish_pizza($db, $userid[0]);
        $size_list = get_sizes($db);
        $topping_list= get_toppings($db);
        $user_list=get_users($db);
        include('student_welcome.php'); 
       
    } catch (Exception $ex) {
        $error_message = $e->getMessage();
        include('../errors/database_error.php');
    }
    header ("Location: .?username=$username");
}
