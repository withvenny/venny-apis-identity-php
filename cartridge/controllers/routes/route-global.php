<?php
 
    //
    header('Content-Type: application/json');

    //
    require 'autoload.php';
    require 'controllers/components/component-functions.php';
    require 'controllers/components/component-environments.php';

    //
    if(isset($_REQUEST['token'])) {

        //
        if(isset($_REQUEST['app'])) {

            $request['app'] = $_REQUEST['app'];
    
            //
            switch ($_REQUEST['domain']) {

                //
                case 'signup': require 'controllers/resources/resource-experience-signup.php'; break;
                case 'signin': require 'controllers/resources/resource-experience-signin.php'; break;
                case 'payment': require 'controllers/resources/resource-experience-payment.php'; break;
                case 'products': require 'controllers/resources/resource-experience-product.php'; break;

                //
                case 'persons': require 'controllers/resources/resource-identity-persons.php'; break;
                case 'partners': require 'controllers/resources/resource-identity-partners.php'; break;
                case 'users': require 'controllers/resources/resource-identity-users.php'; break;
                case 'profiles': require 'controllers/resources/resource-identity-profiles.php'; break;
    
                //
                default: header("Location: template-guest-hello.php");
            
            }

        } else { 

            // connect to the PostgreSQL database

            //$data = NULL;
            $code = 401;
            $message = "Forbidden - Valid App ID required";

            $results = array(
                'status' => $code,
                'message' => $message,
                /*
                'data' => $data,
                'log' => [
                    'process' => $process_id = Token::process_id(),
                    'event' => $event_id = Token::event_id($process_id)
                ]*/
            );
            
            $results = json_encode($results);
        
            echo $results;
        
        }

    } else {

        // connect to the PostgreSQL database

        //$data = NULL;
        $code = 401;
        $message = "Forbidden - Valid token required";

        $results = array(
            'status' => $code,
            'message' => $message,
            /*
            'data' => $data,
            'log' => [
                'process' => $process_id = Token::process_id(),
                'event' => $event_id = Token::event_id($process_id)
            ]*/
        );

        $results = json_encode($results);
        
        echo $results;

    }

?>
