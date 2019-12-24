<?php
// the try/catch for these actions is in the caller
function add_topping($db, $topping_name,$is_meat)  {
    $query = 'INSERT INTO menu_toppings(topping, is_meat)
    VALUES (:topping_name, :is_meat)';
    $statement = $db->prepare($query);
    $statement->bindValue(':topping_name', $topping_name);
    $statement->bindValue(':is_meat', $is_meat);
    $statement->execute();
    $statement->closeCursor();
}

//delete 
function delete_topping($db, $topping_id)  {
    $query = 'DELETE FROM menu_toppings WHERE id=:topping_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':topping_id', $topping_id);
    $success = $statement->execute();
    $statement->closeCursor();
}

function get_toppings($db) {
    $query = 'SELECT * FROM menu_toppings';
    $statement = $db->prepare($query);
    $statement->execute();
    $toppings = $statement->fetchAll();
    return $toppings;    
}

function get_meat($db) {
    $query = 'SELECT topping FROM menu_toppings WHERE is_meat=1' ;
    $statement = $db->prepare($query);
    $statement->execute();
    $meat_order = $statement->fetchAll();
    return $meat_order;    
}

function get_meatless($db) {
    $query = 'SELECT topping FROM menu_toppings WHERE is_meat=0';
    $statement = $db->prepare($query);
    $statement->execute();
    $meatless_order = $statement->fetchAll();
    return $meatless_order;    
}