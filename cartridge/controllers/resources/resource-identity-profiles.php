<?php

    //
    header('Content-Type: application/json');

    //
    use Identity\Connection as Connection;
    use Identity\Token as Token;
    use Identity\Profile as Profile;

    // connect to the PostgreSQL database
    $pdo = Connection::get()->connect();

    // STEP 1. Receive passed variables / information
    if(isset($_REQUEST['app'])){$request['app'] = clean($_REQUEST['app']);}
    if(isset($_REQUEST['domain'])){$request['domain'] = clean($_REQUEST['domain']);}
    if(isset($_REQUEST['token'])){$request['token'] = clean($_REQUEST['token']);}

    // data cleanse

    if(isset($_REQUEST['id'])){$request['id'] = clean($_REQUEST['id']);}
    if(isset($_REQUEST['attributes'])){$request['attributes'] = clean($_REQUEST['attributes']);}
    if(isset($_REQUEST['images'])){$request['images'] = clean($_REQUEST['images']);}
    if(isset($_REQUEST['bio'])){$request['bio'] = clean($_REQUEST['bio']);}
    if(isset($_REQUEST['headline'])){$request['headline'] = clean($_REQUEST['headline']);}
    if(isset($_REQUEST['access'])){$request['access'] = clean($_REQUEST['access']);}
    if(isset($_REQUEST['status'])){$request['status'] = clean($_REQUEST['status']);}

    if(isset($_REQUEST['user'])){$request['user'] = clean($_REQUEST['user']);}

    //
    switch ($_SERVER['REQUEST_METHOD']) {

        //
        case 'POST':

            try {

                // 
                $profile = new Profile($pdo);
            
                // insert a stock into the stocks table
                $id = $profile->insertProfile($request);

                $request['id'] = $id;

                $results = $profile->selectProfiles($request);

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
                $profile = new Profile($pdo);

                // get all stocks data
                $results = $profile->selectProfiles($request);

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
                $profile = new Profile($pdo);
            
                // insert a stock into the stocks table
                $id = $profile->updateProfile($request);

                $request['id'] = $id;

                $results = $profile->selectProfiles($request);

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
                $profile = new Profile($pdo);
            
                // insert a stock into the stocks table
                $id = $profile->deleteProfile($request);

                echo 'The record ' . $id . ' has been deleted';
            
            } catch (\PDOException $e) {

                echo $e->getMessage();

            }

        break;

    }

?>
