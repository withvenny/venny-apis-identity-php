<?php

    //
    header('Content-Type: application/json');

    //
    use Identity\Connection as Connection;
    use Identity\Token as Token;
    use Identity\User as User;

    // connect to the PostgreSQL database
    $pdo = Connection::get()->connect();

    // STEP 1. Receive passed variables / information
    if(isset($_REQUEST['app'])){$request['app'] = clean($_REQUEST['app']);}
    if(isset($_REQUEST['domain'])){$request['domain'] = clean($_REQUEST['domain']);}
    if(isset($_REQUEST['token'])){$request['token'] = clean($_REQUEST['token']);}

    // data cleanse

    if(isset($_REQUEST['id'])){$request['id'] = clean($_REQUEST['id']);}
    if(isset($_REQUEST['attributes'])){$request['attributes'] = clean($_REQUEST['attributes']);}
    if(isset($_REQUEST['alias'])){$request['alias'] = clean($_REQUEST['alias']);}
    if(isset($_REQUEST['authorize'])){$request['authorize'] = clean($_REQUEST['authorize']);}
    if(isset($_REQUEST['login_last'])){$request['login_last'] = clean($_REQUEST['login_last']);}
    if(isset($_REQUEST['status'])){$request['status'] = clean($_REQUEST['status']);}
    if(isset($_REQUEST['validation'])){$request['validation'] = clean($_REQUEST['validation']);}
    if(isset($_REQUEST['welcome'])){$request['welcome'] = clean($_REQUEST['welcome']);}
    if(isset($_REQUEST['person'])){$request['person'] = clean($_REQUEST['person']);}

    //
    switch ($_SERVER['REQUEST_METHOD']) {

        //
        case 'POST':

            try {

                // 
                $user = new User($pdo);
            
                // insert a stock into the stocks table
                $id = $user->insertUser($request);

                $request['id'] = $id;

                $results = $user->selectUsers($request);

                $results = json_encode($results);
                
                //
                echo $results;
            
            } catch (\PDOException $e) {

                echo $e->getMessage();

            }

        break;

        //
        case 'GET':

            //
            if(isset($_REQUEST['per'])){$request['per'] = clean($_REQUEST['per']);}
            if(isset($_REQUEST['page'])){$request['page'] = clean($_REQUEST['page']);}
            if(isset($_REQUEST['limit'])){$request['limit'] = clean($_REQUEST['limit']);}        

            try {

                // 
                $user = new User($pdo);

                // get all stocks data
                $results = $user->selectUsers($request);

                $results = json_encode($results);

                echo $results;

            } catch (\PDOException $e) {

                echo $e->getMessage();

            }

        break;

        //
        case 'PUT':

            try {

                // 
                $user = new User($pdo);
            
                // insert a stock into the stocks table
                $id = $user->updateUser($request);

                $request['id'] = $id;

                $results = $user->selectUsers($request);

                $results = json_encode($results);

                echo $results;
            
            } catch (\PDOException $e) {

                echo $e->getMessage();

            }

        break;

        //
        case 'DELETE':

            try {

                // 
                $user = new User($pdo);
            
                // insert a stock into the stocks table
                $id = $user->deleteUser($request);

                echo 'The record ' . $id . ' has been deleted';
            
            } catch (\PDOException $e) {

                echo $e->getMessage();

            }

        break;

    }

?>
