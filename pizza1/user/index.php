<?php

require('../model/database.php');
require('../model/user_db.php');

$action = filter_input(INPUT_POST, 'action');
if($action == NULL){
    $action = filter_input(INPUT_GET, 'action');
    if($action == NULL){
        $action = 'list_users';
    }
}

if($action == 'list_users'){
    try{
        $users = get_users($db);
        include ('user_list.php');
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        include('../errors/database_error.php');
        
    }
} else if ($action == 'show_add_form'){
    include('user_add.php');
    
    
} else if ($action == 'add_user') {
    $username = filter_input(INPUT_POST, 'username');
    $room = filter_input(INPUT_POST, 'room');
    if($username == NULL || $username == FALSE) {
        $error = "Invalid username";
        include ('../errors/error.php');
    }
    if($room == NULL || $room == FALSE) {
        $error = "Invalid room";
        include ('../errors/error.php');
    }
    try {
        add_user($db,$username,$room);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        include('../errors/database_error.php');
        exit();
    }
    header("Location: .");
} else if ($action == 'delete_user'){
    $user_id = filter_input(INPUT_POST, 'user_id');
    if($user_id == NULL || $user_id == FALSE) {
        $error = "Invalid user id";
        include ('../errors/error.php');
    } else {
        delete_user($db, $user_id);
    }
    header("Location: .");
}


