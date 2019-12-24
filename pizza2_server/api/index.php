<?php
require __DIR__ . '/../vendor/autoload.php';
require 'initial.php';
// provide aliases for long classname--
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

set_local_error_log(); // redirect error_log to ../php_server_errors.log
// Instantiate the app
$app = new \Slim\App();
// Add middleware that can add CORS headers to response (if uncommented)
// These CORS headers allow any client to use this service (the wildcard star)
// We don't need CORS for the ch05_gs client-server project, because
// its network requests don't come from the browser. Only requests that
// come from the browser need these headers in the response to satisfy
// the browser that all is well. Even in that case, the headers are not
// needed unless the server for the REST requests is different than
// the server for the HTML and JS. When we program in Javascript we do
// send requests from the browser, and then the server may need to
// generate these headers.
// Also specify JSON content-type, and overcome default Allow of GET, PUT
// Note these will be added on failing cases as well as sucessful ones
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
                    ->withHeader('Access-Control-Allow-Origin', '*')
                    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
                    ->withHeader('Content-Type', 'application/json')
                    ->withHeader('Allow', 'GET, POST, PUT, DELETE');
});
// Turn PHP errors and warnings (div by 0 is a warning!) into exceptions--
// From https://stackoverflow.com/questions/1241728/can-i-try-catch-a-warning
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // error was suppressed with the @-operator--
    // echo 'in error handler...';
    if (0 === error_reporting()) {
        return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Slim has default error handling, but not super useful
// so we'll override those handlers so we can handle errors 
// in this code, and report file and line number.
// This also means we don't set $config['displayErrorDetails'] = true;
// because that just affects the default error handler.
// See https://akrabat.com/overriding-slim-3s-error-handling/
// To see this in action, put a parse error in your code
$container = $app->getContainer();
$container['errorHandler'] = function ($container) {
    return function (Request $request, Response $response, $exception) {
        // retrieve logger from $container here and log the error
        $response->getBody()->rewind();
        $errorJSON = '{"error":{"text":' . $exception->getMessage() .
                ', "line":' . $exception->getLine() .
                ', "file":' . $exception->getFile() . '}}';
        //     echo 'error JSON = '. $errorJSON;           
        error_log("server error: $errorJSON");
        return $response->withStatus(500)
                        //            ->withHeader('Content-Type', 'text/html')
                        ->write($errorJSON);
    };
};

// This function should not be called because errors are turned into exceptons
// but it still is, on error 'Call to undefined function' for example
$container['phpErrorHandler'] = function ($container) {
    return function (Request $request, Response $response, $error) {
        // retrieve logger from $container here and log the error
        $response->getBody()->rewind();
        echo 'PHP error:  ';
        print_r($error->getMessage());
        $errorJSON = '{"error":{"text":' . $error->getMessage() .
                ', "line":' . $error->getLine() .
                ', "file":' . $error->getFile() . '}}';
        error_log("server error: $errorJSON");
        return $response->withStatus(500)
                        //  ->withHeader('Content-Type', 'text/html)
                        ->write($errorJSON);
    };
};
$app->get('/day', 'getDay');
$app->post('/day', 'postDay');
$app->get('/toppings/{id}', 'getToppingId');
$app->get('/toppings', 'getAllToppings');
$app->get('/sizes', 'getAllSizes');
$app->get('/users', 'getAllUsers');
$app->get('/orders', 'getAllOrders');
$app->get('/orders/{id}', 'getOrderId');
$app->post('/orders', 'postAddOrder');
$app->put('/orders/{id}', 'putOrderId' );


// Take over response to URLs that don't match above rules, to avoid sending
// HTML back in these cases
$app->map(['GET', 'POST', 'PUT', 'DELETE'], '/{routes:.+}', function($req, $res) {
    $uri = $req->getUri();
    $errorJSON = '{"error": "HTTP 404 (URL not found) for URL ' . $uri . '"}';
    return $res->withStatus(404)
                    ->write($errorJSON);
});
$app->run();

function getDay(Request $request, Response $response) {
    error_log("server getDay");
    $sql = "select current_day FROM pizza_sys_tab";
    $db = getConnection();
    $stmt = $db->query($sql);
    return $stmt->fetch(PDO::FETCH_COLUMN, 0);
}

function postDay(Request $request, Response $response) {
    error_log("server postDay");
    $db = getConnection();
    initial_db($db);
    return "1";  // new day value
}

function getToppingId(Request $request, Response $response, $args ){
    error_log('server getSecondTopping');
    $id = $args['id'];
    $db = getConnection();
    $sql = 'SELECT topping FROM menu_toppings where id = :id';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(":id", $id);
    $stmt ->execute();
    $toppingId = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt ->closeCursor();
    echo json_encode($toppingId);
}

function getAllToppings(Request $request, Response $response){
    error_log('server getAllToppings');
    $db = getConnection();
    $sql = 'SELECT * FROM menu_toppings';
    $stmt = $db->prepare($sql);
    $stmt ->execute();
    $toppings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt ->closeCursor();
    echo json_encode($toppings);
}


function getAllUsers(Request $request, Response $response){
    error_log('server getAllUsers');
    $db = getConnection();
    $sql = 'SELECT * FROM shop_users';
    $stmt = $db->prepare($sql);
    $stmt ->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt ->closeCursor();
    echo json_encode($users);
}

function getAllOrders(Request $request, Response $response){
    error_log('server getAllOrder');
    $db = getConnection();
    $sql = 'SELECT pizza_orders.id, pizza_orders.user_id, pizza_orders.size, pizza_orders.day, pizza_orders.status, order_topping.topping as toppings FROM pizza_orders, order_topping where pizza_orders.id = order_topping.order_id';
    $stmt = $db->prepare($sql);
    $stmt ->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //echo $order[0];
    foreach ($orders as $ord => $order){
        $toppings = explode('|', $order['toppings']);//getToppingId($request, $response, $order);
        $orders[$ord]['toppings'] = $toppings;
    }
    $stmt ->closeCursor();
    echo json_encode($orders);
}

function getAllSizes(Request $request, Response $response){
    error_log('server getAllSizes');
    $db = getConnection();
    $sql = 'SELECT * FROM menu_sizes';
    $stmt = $db->prepare($sql);
    $stmt ->execute();
    $sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt ->closeCursor();
    echo json_encode($sizes);
}

function getOrderId(Request $request, Response $response, $args){
    error_log('server getOrderId');
    $id = $args['id'];
    $db = getConnection();
    $sql = 'SELECT pizza_orders.id, pizza_orders.user_id, pizza_orders.size, pizza_orders.day, pizza_orders.status, order_topping.topping as toppings FROM pizza_orders, order_topping where pizza_orders.id = order_topping.order_id and pizza_orders.id = :id';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(":id", $id);
    $stmt ->execute();
    $order = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt ->closeCursor();
    echo json_encode($order); 
}

    
function postAddOrder(Request $request, Response $response) {
    error_log("server postOrder");
    error_log("server:body:".$request->getBody());
    $order = $request->getParsedBody();
    error_log('server:parsed order ='.print_r($order));
    if ($order==NULL){
        $errorJson = '{"error":{"text":"bad JSON in request"}}';
        error_log("server error $errorJson");
        return $response->withStatus(400)
                ->write($errorJson);        
    }
    try {
        $db = getConnection();
        $orderId = add_order($db, $order['user_id'], $order['size'], $order['day'], $order['status'], $order["toppings"]);
    } catch (Exception $e) {
        if (strstr($e->getMessage(),'SQLSTATE[23000]')){
            $errorJson = '{"error":{"text":'.$e->getMessage().',"line":'.$e->getLine().',"file":'.$e->getFile().'}}';
            error_log("server error $errorJson");
            return $response->withStatus(400)
                    ->write($errorJson);
        } else {
        throw ($e);
        }
    }
    $order['orderId'] = $orderId;
    $JSONcontent = json_encode($order);
    $location = $request->getUri().'/'.$order["orderId"];
    return $response->withHeader('Location',$location)
            ->withStatus(200)
            ->write($JSONcontent);
}

function add_order($db, $user_id, $size, $current_day, $status, $topping_name) {
    error_log("server addOrder");
//    $db = getConnection();
//    $sql = 'SELECT * FROM menu_toppings';
//    $stmt = $db->prepare($sql);
//    $stmt ->execute();
//    $toppings = $stmt->fetchAll(PDO::FETCH_ASSOC);
//    $stmt ->closeCursor();
//    foreach ($topping_name as $name) {
//        foreach ($toppings as $t) {
//            if ($name == $t["topping"]) {
//                $topping_id[] = $t["id"];
//                break;
//            }
//        }
//    }
    $query1 = 'INSERT INTO pizza_orders
                 (user_id, size, day, status)
              VALUES
                 (:user_id, :size, :current_day, :status)';
    $query2 = 'insert into order_topping(order_id, topping) values (last_insert_id(), :topping)';
    $statement = $db->prepare($query1);
    $statement->bindValue(':user_id', $user_id);
    $statement->bindValue(':size', $size);
    $statement->bindValue(':status', $status);
    $statement->bindValue(':current_day', $current_day);
    $statement->execute();
    $statement = $db->prepare($query2);
    $statement->bindValue(':topping', implode('|', $topping_name));
    $statement->execute();
    $statement->closeCursor();
    $id = $db->lastInsertId();
    return $id;
}

function putOrderId(Request $request, Response $response, $args){
    error_log('server putOrderId');
    $db = getConnection();
    $orderId = $args['id'];
    $query = 'UPDATE pizza_orders SET status="Finished" WHERE id = :orderId and status = "Baked"'  ;
    $statement = $db->prepare($query);
    $statement->bindValue(':orderId', $orderId);
    $statement->execute();
    $statement->closeCursor();
}




// set up to execute on XAMPP or at topcat.cs.umb.edu:
// --set up a mysql user named pizza_user on your own system
// --see database/dev_setup.sql and database/createdb.sql
// --load your mysql database on topcat with the pizza db
// Then this code figures out which setup to use at runtime
function getConnection() {
//    if (gethostname() === 'topcat') {
//        $dbuser = 'guangy';  // CHANGE THIS to your cs.umb.edu username
//        $dbpass = 'PIG19980730';  // CHANGE THIS to your mysql DB password on topcat 
//        $dbname = $dbuser . 'db'; // our convention for mysql dbs on topcat   
//    } else {  // dev machine, can create pizzadb
        $dbuser = 'guangy';
        $dbpass = 'guangy';  // or your choice
        $dbname = 'pizzadb';
//    }
    $dsn = 'mysql:host=localhost;dbname=' . $dbname;
    $dbh = new PDO($dsn, $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}
