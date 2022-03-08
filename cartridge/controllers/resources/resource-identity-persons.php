<?php

    //
    header('Content-Type: application/json');

    //
    use Identity\Connection as Connection;
    use Identity\Token as Token;
    use Identity\Person as Person;

    // connect to the PostgreSQL database
    $pdo = Connection::get()->connect();

    // STEP 1. Receive passed variables / information
    if(isset($_REQUEST['app'])){$request['app'] = clean($_REQUEST['app']);}
    if(isset($_REQUEST['domain'])){$request['domain'] = clean($_REQUEST['domain']);}
    if(isset($_REQUEST['token'])){$request['token'] = clean($_REQUEST['token']);}

    // data cleanse
    if(isset($_REQUEST['id'])){$request['id'] = clean($_REQUEST['id']);}
    if(isset($_REQUEST['attributes'])){$request['attributes'] = clean($_REQUEST['attributes']);}
    if(isset($_REQUEST['name_first'])){$request['name_first'] = clean($_REQUEST['name_first']);}
    if(isset($_REQUEST['name_middle'])){$request['name_middle'] = clean($_REQUEST['name_middle']);}
    if(isset($_REQUEST['name_last'])){$request['name_last'] = clean($_REQUEST['name_last']);}
    if(isset($_REQUEST['phone'])){$request['phone'] = clean($_REQUEST['phone']);}
    if(isset($_REQUEST['email'])){$request['email'] = clean($_REQUEST['email']);}
    if(isset($_REQUEST['address'])){$request['address'] = clean($_REQUEST['address']);}
    if(isset($_REQUEST['entitlements'])){$request['entitlements'] = clean($_REQUEST['entitlements']);}
    //
    switch ($_SERVER['REQUEST_METHOD']) {

        //
        case 'POST':

            try {

                // 
                $person = new Person($pdo);
            
                // insert a stock into the stocks table
                $id = $person->insertPerson($request);

                $request['id'] = $id;

                $results = $person->selectPersons($request);

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
                $person = new Person($pdo);

                // get all stocks data
                $results = $person->selectPersons($request);

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
                $person = new Person($pdo);
            
                // insert a stock into the stocks table
                $id = $person->updatePerson($request);

                $request['id'] = $id;

                $results = $person->selectPersons($request);

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
                $person = new Person($pdo);
            
                // insert a stock into the stocks table
                $id = $person->deletePerson($request);

                echo 'The record ' . $id . ' has been deleted';
            
            } catch (\PDOException $e) {

                echo $e->getMessage();

            }

        break;

    }

?>
