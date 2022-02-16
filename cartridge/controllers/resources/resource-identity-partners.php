<?php

    //
    header('Content-Type: application/json');

    //
    use Identity\Connection as Connection;
    use Identity\Token as Token;
    use Identity\Partner as Partner;

    // connect to the PostgreSQL database
    $pdo = Connection::get()->connect();

    // STEP 1. Receive passed variables / information
    if(isset($_REQUEST['app'])){$request['app'] = clean($_REQUEST['app']);}
    if(isset($_REQUEST['domain'])){$request['domain'] = clean($_REQUEST['domain']);}
    if(isset($_REQUEST['token'])){$request['token'] = clean($_REQUEST['token']);}

    // data cleanse
    if(isset($_REQUEST['id'])){$request['id'] = clean($_REQUEST['id']);}		
    if(isset($_REQUEST['attributes'])){$request['attributes'] = clean($_REQUEST['attributes']);}		
    if(isset($_REQUEST['type'])){$request['type'] = clean($_REQUEST['type']);}		
    if(isset($_REQUEST['status'])){$request['status'] = clean($_REQUEST['status']);}		
    if(isset($_REQUEST['organization'])){$request['organization'] = clean($_REQUEST['organization']);}		
    if(isset($_REQUEST['headquarters'])){$request['headquarters'] = clean($_REQUEST['headquarters']);}		
    if(isset($_REQUEST['locations'])){$request['locations'] = clean($_REQUEST['locations']);}		
    if(isset($_REQUEST['user'])){$request['user'] = clean($_REQUEST['user']);}
    
    //
    switch ($_SERVER['REQUEST_METHOD']) {

        //
        case 'POST':

            try {

                // 
                $partner = new Partner($pdo);
            
                // insert a stock into the stocks table
                $id = $partner->insertPartner($request);

                $request['id'] = $id;

                $results = $partner->selectPartners($request);

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
                $partner = new Partner($pdo);

                // get all stocks data
                $results = $partner->selectPartners($request);

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
                $partner = new Partner($pdo);
            
                // insert a stock into the stocks table
                $id = $partner->updatePartner($request);

                $request['id'] = $id;

                $results = $partner->selectPartners($request);

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
                $partner = new Partner($pdo);
            
                // insert a stock into the stocks table
                $id = $partner->deletePartner($request);

                echo 'The record ' . $id . ' has been deleted';
            
            } catch (\PDOException $e) {

                echo $e->getMessage();

            }

        break;

    }

?>
