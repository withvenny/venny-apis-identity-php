<?php

    //
    header('Content-Type: application/json');

    //
    use Identity\Connection as Connection;
    use Identity\Token as Token;
    use Identity\Profile as Profile;
    use Identity\Person as Person;
    use Identity\User as User;

    // connect to the PostgreSQL database
    $pdo = Connection::get()->connect();

    // STEP 1. Receive passed variables / information
    if(isset($_REQUEST['app'])){$request['app'] = clean($_REQUEST['app']);}
    if(isset($_REQUEST['domain'])){$request['domain'] = clean($_REQUEST['domain']);}
    if(isset($_REQUEST['token'])){$request['token'] = clean($_REQUEST['token']);}

    /*
    if (empty($_REQUEST['email']) || empty($_REQUEST['firstName']) || empty($_REQUEST['lastName']) || 
    empty($_REQUEST['password']) || empty($_REQUEST['birthday']) || empty($_REQUEST['gender'])) {
    */

    // data cleanse
    if(isset($_REQUEST['name_first'])){$request['name_first'] = clean($_REQUEST['name_first']);}
    if(isset($_REQUEST['email'])){$request['email'] = clean($_REQUEST['email']);}
    if(isset($_REQUEST['authorize'])){$request['authorize'] = clean($_REQUEST['authorize']);}
    if(isset($_REQUEST['alias'])){$request['alias'] = clean($_REQUEST['alias']);}

    //
    if($_SERVER['REQUEST_METHOD']=='POST') {

        //echo json_encode($_REQUEST);exit;

        try {

            // 
            $profile = new Profile($pdo);
            $person = new Person($pdo);
            $user = new User($pdo);

            /*
            name_first
            name_last
            email
            access
            */

            //check if email exists
            $request['domain'] = 'persons';
            
            $good_email = $person->selectPersons($request);
            //echo json_encode($good_email); exit;
            if($good_email['status'] == 200) {
                echo "not new email"; exit;
            }
        
            // insert a person and get person ID
            $request['domain'] = 'persons';
            $person_id = $person->insertPerson($request);

            //echo json_encode($person_id) . '<br/>';

            // add new person ID to overall request
            $request['id'] = $person_id;

            // get person ID's details
            $person_details = $person->selectPersons($request);

            //echo json_encode($person_details) . '<br/>';
            //echo json_encode($person_details['data']) . '<br/>';
            //echo json_encode($person_details['data'][0]) . '<br/>';

            $request['person'] = $person_details['data'][0]['id'];

            // insert a user and get user ID
            $request['domain'] = 'users';
            
            //Set ID
            $request['id']=NULL;

            //
            $user_id = $user->insertUser($request);

            //echo json_encode($user_id) . '<br/>';

            // add new person ID to overall request
            $request['id'] = $user_id;

            // get person ID's details
            $user_details = $user->selectUsers($request);

            //echo json_encode($user_details) . '<br/>';

            // insert a profile and get profile ID
            $request['user'] = $user_details['data'][0]['id'];

            //
            $request['domain'] = 'profiles';

            //Set ID
            $request['id']=NULL;
            
            $profile_id = $profile->insertProfile($request);

            //echo json_encode($profile_id).'<br/>';
            //echo json_encode($user_details['data'][0]['id']).'<br/>';

            // add new person ID to overall request
            $request['id'] = $profile_id;

            // get person ID's details
            $profile_details = $profile->selectProfiles($request);
            //echo json_encode($profile_details) . '<br/>';

            //
            $results['data']['persons']=$person_details['data'];
            $results['data']['user']=$user_details['data'];
            $results['data']['profile']=$profile_details['data'];

            //
            $results = json_encode($results);

            echo $results;
        
        } catch (\PDOException $e) {

            echo $e->getMessage();

        }

    }

?>
