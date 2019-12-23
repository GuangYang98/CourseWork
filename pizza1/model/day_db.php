<?php
function get_current_day ($db){
    $query = 'SELECT current_day FROM `pizza_sys_tab`';
    $statement = $db->prepare($query);
    $statement->execute();
    $current_day = $statement->fetch();
    return $current_day;    
}

function update_current_day ($db){
    $query = 'UPDATE pizza_sys_tab SET current_day = :current_day +1';
    $temp= get_current_day($db);
    $statement = $db->prepare($query);
    $statement->bindValue(':current_day', $temp[0]);
    $statement->execute();
    $statement->closeCursor();   
}
