<?php
function get_orders_baked($db) {
    $query = 'SELECT pizza_orders.id as order_id, shop_users.username, pizza_orders.status FROM `pizza_orders` join shop_users WHERE shop_users.id= pizza_orders.user_id and pizza_orders.status="Baked" and pizza_orders.day = :current_day';
    $temp= get_current_day($db);
    $statement = $db->prepare($query);
    $statement->bindValue(':current_day', $temp[0]);
    $statement->execute();
    $baked_orders = $statement->fetchAll();
    return $baked_orders;    
}

function get_orders_preparing($db) {
    $query = 'SELECT pizza_orders.id as order_id, shop_users.username, pizza_orders.status FROM `pizza_orders` join shop_users WHERE shop_users.id= pizza_orders.user_id and pizza_orders.status="Preparing" and pizza_orders.day = :current_day ';
    $temp= get_current_day($db);
    $statement = $db->prepare($query);
    $statement->bindValue(':current_day', $temp[0]);
    $statement->execute();
    $preparing_orders = $statement->fetchAll();
    return $preparing_orders;    
}

function get_personal_order($db,$username) {
    $query = 'SELECT pizza_orders.id as order_id, shop_users.username, pizza_orders.status FROM `pizza_orders` join shop_users WHERE shop_users.id= pizza_orders.user_id and shop_users.username=:username and pizza_orders.status <>"Finished" and pizza_orders.day = :current_day' ;
    $temp= get_current_day($db);
    $statement = $db->prepare($query);
    $statement->bindValue(':current_day', $temp[0]);
    $statement->bindValue(':username', $username);
    $statement->execute();
    $get_personal_order = $statement->fetchAll();
    return $get_personal_order;    
}

function get_orders($db,$current_day) {
    $query = 'SELECT pizza_orders.id as order_id, shop_users.username, pizza_orders.status FROM `pizza_orders` join shop_users WHERE shop_users.id= pizza_orders.user_id and pizza_orders.day = :current_day';
    $statement = $db->prepare($query);
    $statement->bindValue(':current_day', $current_day);
    $statement->execute();
    $get_orders = $statement->fetchAll();
    return $get_orders;    
}


//order pizza .php  page 
function add_order($db,$username,$size, $topping) {
    $userid = get_userid_by_name($db, $username);
    $query1 = 'insert into pizza_orders(user_id, size, day, status) values (:userid, :size, :current_day, "Preparing")';
    $query2 = 'insert into order_topping(order_id, topping) values (last_insert_id(), :topping)';
    $temp= get_current_day($db);
    $statement = $db->prepare($query1);
    $statement->bindValue(':current_day', $temp[0]);
    $statement->bindValue(':userid', $userid[0]);
    $statement->bindValue(':size', $size);
    $statement->execute();
    
    $statement = $db->prepare($query2);
    $statement->bindValue(':topping', $topping);
    $statement->execute();
    $statement->closeCursor();

}


function update_status($db){
    $query = 'UPDATE pizza_orders SET status="Baked" WHERE id = ( select min(A.id) FROM (SELECT * FROM pizza_orders WHERE status="Preparing") A GROUP BY status)'  ;
    $statement = $db->prepare($query);
    $statement->execute();
    $statement->closeCursor();  
}
function finish_pizza($db, $userid){
    $query = 'UPDATE pizza_orders SET status="Finished" WHERE user_id = :userid and status = "Baked"'  ;
    $statement = $db->prepare($query);
    $statement->bindValue(':userid', $userid);
    $statement->execute();
    $statement->closeCursor();  
}

