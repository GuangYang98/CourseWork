<?php

function get_users($db){
    $query = 'select * from shop_users';
    $statement = $db->prepare($query);
    $statement->execute();
    $users = $statement->fetchAll();
    return $users;
}

function get_userid_by_name($db,$username){
    $query = 'select id from shop_users where username = :username';
    $statement = $db->prepare($query);
    $statement->bindValue(':username', $username);
    $statement->execute();
    $userid = $statement->fetch();
    return $userid;
}

function add_user($db, $username, $room) {
    $query = 'insert into shop_users(username, room) values (:username, :room)';
    $statement = $db->prepare($query);
    $statement->bindValue(':username', $username);
    $statement->bindValue(':room', $room);
    $statement->execute();
    $statement->closeCursor();
}
?>
