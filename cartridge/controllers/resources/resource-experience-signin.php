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
    if(isset($_REQUEST['email'])){$request['email'] = clean($_REQUEST['email']);}else{echo json_encode(array('status'=>400,'message'=>'A valid email address is required to sign in.'));exit;}
    if(isset($_REQUEST['authorize'])){$request['authorize'] = clean($_REQUEST['authorize']);}else{echo json_encode(array('status'=>400,'message'=>'Please provide valid .'));exit;}
    //if(isset($_REQUEST['alias'])){$request['alias'] = clean($_REQUEST['alias']);}

    //
    if($_SERVER['REQUEST_METHOD']=='POST') {

        //echo json_encode($_REQUEST);//exit;

        try {

            // 
            $profile = new Profile($pdo);
            $person = new Person($pdo);
            $user = new User($pdo);
            $token = new Token($pdo);

            //
            $request['domain'] = 'persons';
            $request['per'] = 1;
            $request['page'] = 1;
            $request['limit'] = 1;

            // get person ID's details
            $person_details = $person->selectPersons($request);

            //
            $request['person'] = $person_details['data'][0]['id'];

            $request['domain'] = 'users';

            //echo json_encode($request);
            //exit;

            // get person ID's details
            $user_details = $user->selectUsers($request);

            //echo json_encode($user_details);
            //exit;

            // insert a profile and get profile ID
            $request['user'] = $user_details['data'][0]['id'];

            //
            $request['domain'] = 'profiles';

            //
            $profile_details = $profile->selectProfiles($request);

            //echo json_encode($profile_details);
            //exit;

            //
            $results['status'] = 200;
            $results['message'] = 'SUCCESSFUL';

            //
            //$data = array_push(...$person_details['data'],...$user_details['data'],...$profile_details['data']);
            //$results['data']=$data;

            $results['data']=NULL;
            $results['data']['person']=$person_details['data'];
            $results['data']['user']=$user_details['data'];
            $results['data']['profile']=$profile_details['data'];

            $results['log'] = [
                'process' => $process_id = $token->process_id(),
                'event' => $token->event_id($process_id)
            ];

            //
            $results = json_encode($results);

            echo $results;
        
        } catch (\PDOException $e) {

            echo $e->getMessage();

        }

    }

?>
