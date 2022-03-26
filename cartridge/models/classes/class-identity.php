<?php

    //
    namespace Identity;

    //
    class Connection {
    
        /**
         * Connection
         * @var type 
         */
        private static $conn;
    
        /**
         * Connect to the database and return an instance of \PDO object
         * @return \PDO
         * @throws \Exception
         */
        public function connect() {

            // read parameters in the ini configuration file
            //$params = parse_ini_file('database.ini');
            $db = parse_url(getenv("DATABASE_URL"));

            //if ($params === false) {throw new \Exception("Error reading database configuration file");}
            if ($db === false) {throw new \Exception("Error reading database configuration file");}
            // connect to the postgresql database
            $conStr = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s", 
                    $db['host'],
                    $db['port'], 
                    ltrim($db["path"], "/"), 
                    $db['user'], 
                    $db['pass']);
    
            $pdo = new \PDO($conStr);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    
            return $pdo;
        }
    
        /**
         * return an instance of the Connection object
         * @return type
         */
        public static function get() {
            if (null === static::$conn) {
                static::$conn = new static();
            }
    
            return static::$conn;
        }
    
        protected function __construct() {
            
        }
    
        private function __clone() {
            
        }
    
        private function __wakeup() {
            
        }
    
    }

    //
    class Token {

        /**
         * PDO object
         * @var \PDO
         */
        private $pdo;
    
        /**
         * init the object with a \PDO object
         * @param type $pdo
         */
        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        /**
         * Return all rows in the stocks table
         * @return array
         */
        public function all() {
            $stmt = $this->pdo->query('SELECT id, symbol, company '
                    . 'FROM stocks '
                    . 'ORDER BY symbol');
            $stocks = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $stocks[] = [
                    'id' => $row['id'],
                    'symbol' => $row['symbol'],
                    'company' => $row['company']
                ];
            }
            return $stocks;
        }

        //
        public function validatedToken() {
            
            //
            return true;
            
            //exit;

        }

        //
        public function process_id() {

            //
            $id = substr(md5(uniqid(microtime(true),true)),0,13);

            //
            return $id;
            
            //exit;

        }
        
        //
        public function event_id() {

            //
            $id = substr(md5(uniqid(microtime(true),true)),0,13);
    
            //
            return $id;
            
            //exit;

        }

        //
        public function new_id($object='obj') {

            //
            $id = substr(md5(uniqid(microtime(true),true)),0,13);
            $id = $object . "_" . $id;
    
            //
            return $id;
            
            //exit;

        }

        /**
         * Find stock by id
         * @param int $id
         * @return a stock object
         */
        public function check($id) {

            //
            $sql = "SELECT person_id FROM persons WHERE id = :id AND active = 1";

            // prepare SELECT statement
            $statement = $this->pdo->prepare($sql);
            // bind value to the :id parameter
            $statement->bindValue(':id', $id);
            
            // execute the statement
            $stmt->execute();
    
            // return the result set as an object
            return $stmt->fetchObject();
        }

        /**
         * Delete a row in the stocks table specified by id
         * @param int $id
         * @return the number row deleted
         */
        public function delete($id) {
            $sql = 'DELETE FROM stocks WHERE id = :id';
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
    
            $stmt->execute();
    
            return $stmt->rowCount();
        }

        /**
         * Delete all rows in the stocks table
         * @return int the number of rows deleted
         */
        public function deleteAll() {
    
            $stmt = $this->pdo->prepare('DELETE FROM stocks');
            $stmt->execute();
            return $stmt->rowCount();
        }

    }

    //
    class Person {

        //
        private $pdo;
    
        //
        public function __construct($pdo) {

            //
            $this->pdo = $pdo;

            //
            $this->token = new \Identity\Token($this->pdo);

        }

        //
        public function insertPerson($request) {

            //generate ID
            if(!isset($request['id'])){$request['id'] = $this->token->new_id('per');}

            // INSERT OBJECT - COLUMNS
            $columns = "";
            if(isset($request['id'])){$columns.="person_id,";}
            if(isset($request['attributes'])){$columns.="person_attributes,";}
            if(isset($request['name_first'])){$columns.="person_name_first,";}
            if(isset($request['name_middle'])){$columns.="person_name_middle,";}
            if(isset($request['name_last'])){$columns.="person_name_last,";}
            if(isset($request['phone'])){$columns.="person_phone,";}
            if(isset($request['email'])){$columns.="person_email,";}
            if(isset($request['address'])){$columns.="person_address,";}
            if(isset($request['entitlements'])){$columns.="person_entitlements,";}

            $columns.= "app_id,";
            $columns.= "event_id,";
            $columns.= "process_id";

            // INSERT OBJECT - VALUES
            $values = "";
            if(isset($request['id'])){$values.=":person_id,";}
            if(isset($request['attributes'])){$values.=":person_attributes,";}
            if(isset($request['name_first'])){$values.=":person_name_first,";}
            if(isset($request['name_middle'])){$values.=":person_name_middle,";}
            if(isset($request['name_last'])){$values.=":person_name_last,";}
            if(isset($request['phone'])){$values.=":person_phone,";}
            if(isset($request['email'])){$values.=":person_email,";}
            if(isset($request['address'])){$values.=":person_address,";}
            if(isset($request['entitlements'])){$values.=":person_entitlements,";}

            $values.= ":app_id,";
            $values.= ":event_id,";
            $values.= ":process_id";

            // prepare statement for insert
            $sql = "INSERT INTO {$request['domain']} (";
            $sql.= $columns;
            $sql.= ") VALUES (";
            $sql.= $values;
            $sql.= ")";
            $sql.= " RETURNING " . prefixed($request['domain']) . "_id";
    
            //
            $statement = $this->pdo->prepare($sql);
            
            // INSERT OBJECT - BIND VALUES pass values to the statement
            if(isset($request['id'])){$statement->bindValue('person_id',$request['id']);}
            if(isset($request['attributes'])){$statement->bindValue('person_attributes',$request['attributes']);}
            if(isset($request['name_first'])){$statement->bindValue('person_name_first',$request['name_first']);}
            if(isset($request['name_middle'])){$statement->bindValue('person_name_middle',$request['name_middle']);}
            if(isset($request['name_last'])){$statement->bindValue('person_name_last',$request['name_last']);}
            if(isset($request['phone'])){$statement->bindValue('person_phone',$request['phone']);}
            if(isset($request['email'])){$statement->bindValue('person_email',$request['email']);}
            if(isset($request['address'])){$statement->bindValue('person_address',$request['address']);}
            if(isset($request['entitlements'])){$statement->bindValue('person_entitlements',$request['entitlements']);}

            $statement->bindValue(':app_id', $request['app']);
            $statement->bindValue(':event_id', $this->token->event_id());
            $statement->bindValue(':process_id', $this->token->process_id());
            
            // UPDATE ID
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['person_id'];

            return $data;
        
        }

        //
        public function selectPersons($request) {

            //echo json_encode($request); exit;

            //$token = new \Identity\Token($this->pdo);
            $token = $this->token->validatedToken($request['token']);

            // Retrieve data ONLY if token  
            if($token) {
                
                // domain, app always present
                if(!isset($request['per'])){$request['per']=20;}
                if(!isset($request['page'])){$request['page']=1;}
                if(!isset($request['limit'])){$request['limit']=100;}

                //
                $conditions = "";
                $domain = $request['domain'];
                $prefix = prefixed($domain);

                // SELECT OBJECT - COLUMNS
                $columns = "

                    person_id,
                    person_attributes,
                    person_name_first,
                    person_name_middle,
                    person_name_last,
                    person_phone,
                    person_email,
                    person_address,
                    person_entitlements,
                    app_id,
                    time_finished

                ";

                $table = $domain;

                //
                $start = 0;

                //
                if(isset($request['page'])) {

                    //
                    $start = ($request['page'] - 1) * $request['per'];
                
                }

                //
                if(!empty($request['id'])) {

                    $conditions.= " WHERE";
                    $conditions.= " " . $prefix . "_id = :id ";
                    $conditions.= " AND active = 1 ";
                    $conditions.= " ORDER BY time_finished DESC ";
                    
                    $subset = " LIMIT 1";

                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= " FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;
                    
                    //echo json_encode($request['id']);
                    //echo '<br/>';
                    //echo $sql; exit;

                    //
                    $statement = $this->pdo->prepare($sql);

                    // bind value to the :id parameter
                    $statement->bindValue(':id', $request['id']);

                    //echo $sql; exit;

                } elseif(!empty($request['email'])) {

                    $conditions.= " WHERE";
                    $conditions.= " person_email = :email ";
                    //$conditions.= " person_email = '{$request['email']}' ";
                    $conditions.= " AND active = 1 ";
                    
                    $subset = " LIMIT 1";

                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= " FROM " . " persons ";//$table;
                    $sql.= $conditions;
                    $sql.= $subset;
                    
                    //echo json_encode($request['id']);
                    //echo '<br/>';
                    //echo $sql; exit;

                    //
                    $statement = $this->pdo->prepare($sql);

                    // bind value to the :id parameter
                    $statement->bindValue(':email', $request['email']);

                    //echo $sql; //exit;

                } else {

                    $conditions = "";
                    $refinements = "";
                    // SELECT OBJECT - WHERE CLAUSES
                    if(isset($request['attributes'])){$refinements.="person_attributes"." ILIKE "."'%".$request['attributes']."%' AND ";}
                    if(isset($request['name_first'])){$refinements.="person_name_first"." ILIKE "."'%".$request['name_first']."%' AND ";}
                    if(isset($request['name_middle'])){$refinements.="person_name_middle"." ILIKE "."'%".$request['name_middle']."%' AND ";}
                    if(isset($request['name_last'])){$refinements.="person_name_last"." ILIKE "."'%".$request['name_last']."%' AND ";}
                    if(isset($request['phone'])){$refinements.="person_phone"." ILIKE "."'%".$request['phone']."%' AND ";}
                    if(isset($request['email'])){$refinements.="person_email"." = "."'".$request['email']."' AND ";}
                    if(isset($request['address'])){$refinements.="person_address"." ILIKE "."'%".$request['address']."%' AND ";}
                    if(isset($request['entitlements'])){$refinements.="person_entitlements"." ILIKE "."'%".$request['entitlements']."%' AND ";}
                    
                    //echo $conditions . 'conditions1<br/>';
                    //echo $refinements . 'refinements1<br/>';
                    
                    $conditions.= " WHERE ";
                    $conditions.= $refinements;
                    $conditions.= " active = 1 ";
                    $conditions.= " ORDER BY time_finished DESC ";
                    $subset = " LIMIT 1";
                    //$subset = " OFFSET {$start}" . " LIMIT {$request['per']}";

                    // build SQL statement
                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= "FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;

                    //echo $conditions . 'conditions2<br/>';
                    //echo $refinements . 'refinements2<br/>';

                    //echo $sql; exit;
                    
                    //
                    $statement = $this->pdo->prepare($sql);

                }

                //echo $sql;//exit;
                    
                // execute the statement
                $statement->execute();

                //
                $results = [];
                $total = $statement->rowCount();
                $pages = ceil($total/$request['per']); //
                //$current = 1; // current page
                //$limit = $result['limit'];
                //$max = $result['max'];

                //
                if($statement->rowCount() > 0) {

                    //
                    $data = array();
                
                    //
                    while($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
        
                        // SELECT OBJECT - DATA ARRAY
                        $data[] = [

                            'id' => $row['person_id'],
                            'attributes' => json_decode($row['person_attributes']),
                            'name_first' => $row['person_name_first'],
                            'name_middle' => $row['person_name_middle'],
                            'name_last' => $row['person_name_last'],
                            'phone' => $row['person_phone'],
                            'email' => $row['person_email'],
                            'address' => $row['person_address'],
                            'entitlements' => json_decode($row['person_entitlements']),
                            'app' => $row['app_id'],
                            'when' => $row['time_finished'],

                        ];

                    }

                    $code = 200;
                    $message = "OK";

                } else {

                    //
                    $data = NULL;
                    $code = 204;
                    $message = "No Content";

                }

            } else {

                //
                $data[] = NULL;
                $code = 401;
                $message = "Forbidden - Valid token required";

            }

            $results = array(

                'status' => $code,
                'message' => $message,
                'metadata' => [
                    'page' => $request['page'],
                    'pages' => $pages,
                    'total' => $total
                ],
                'data' => $data,
                'log' => [
                    'process' => $process_id = $this->token->process_id(),
                    'event' => $event_id = $this->token->event_id($process_id)
                ]

            );

            //
            return $results;

        }

        //
        public function updatePerson($request) {

            //
            $domain = $request['domain'];
            $table = prefixed($domain);
            $id = $request['id'];

            //
            $set = "";
            //if(isset($request['id'])){$set.= " person_id = :person_id, ";}
            if(isset($request['id'])){$set.= " person_id = :person_id ";}
            if(isset($request['attributes'])){$set.= " person_attributes = :person_attributes ";}
            if(isset($request['name_first'])){$set.= " person_name_first = :person_name_first ";}
            if(isset($request['name_middle'])){$set.= " person_name_middle = :person_name_middle ";}
            if(isset($request['name_last'])){$set.= " person_name_last = :person_name_last ";}
            if(isset($request['phone'])){$set.= " person_phone = :person_phone ";}
            if(isset($request['email'])){$set.= " person_email = :person_email ";}
            if(isset($request['address'])){$set.= " person_address = :person_address ";}
            if(isset($request['entitlements'])){$set.= " person_entitlements = :person_entitlements ";}
            
            //
            $set = str_replace('  ',',',$set);

            // GET table name
            $condition = $table."_id = :id";
            $condition.= " RETURNING " . $table . "_id";

            //echo json_encode($set);
            //echo json_encode($condition);
            //exit;

            /**
             * Update stock based on the specified id
             * @param int $id
             * @param string $symbol
             * @param string $company
             * @return int
             */

            // sql statement to update a row in the stock table
            $sql = "UPDATE {$domain} SET ";
            $sql.= $set;
            $sql.= " WHERE ";
            $sql.= $condition;

            //echo $sql; exit;

            $statement = $this->pdo->prepare($sql);
    
            // bind values to the statement
            if(isset($request['id'])){$statement->bindValue(':person_id', $request['id']);}
            if(isset($request['attributes'])){$statement->bindValue(':person_attributes', $request['attributes']);}
            if(isset($request['name_first'])){$statement->bindValue(':person_name_first', $request['name_first']);}
            if(isset($request['name_middle'])){$statement->bindValue(':person_name_middle', $request['name_middle']);}
            if(isset($request['name_last'])){$statement->bindValue(':person_name_last', $request['name_last']);}
            if(isset($request['phone'])){$statement->bindValue(':person_phone', $request['phone']);}
            if(isset($request['email'])){$statement->bindValue(':person_email', $request['email']);}
            if(isset($request['address'])){$statement->bindValue(':person_address', $request['address']);}
            if(isset($request['entitlements'])){$statement->bindValue(':person_entitlements', $request['entitlements']);}

            $statement->bindValue(':id', $id);

            // update data in the database
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['person_id'];

            // return generated id
            return $data;

            // return the number of row affected
            //return $statement->rowCount();

        }

        //
        public function deletePerson($request) {

            $id = $request['id'];
            $domain = $request['domain'];
            $column = prefixed($domain) . '_id';
            $sql = 'DELETE FROM ' . $domain . ' WHERE '.$column.' = :id';
            //echo $id; //exit
            //echo $column; //exit;
            //echo $domain; //exit;
            //echo $sql; //exit

            $statement = $this->pdo->prepare($sql);
            //$statement->bindParam(':column', $column);
            $statement->bindValue(':id', $id);
            $statement->execute();
            return $statement->rowCount();

        }

    }

    //
    class User {

        //
        private $pdo;
    
        //
        public function __construct($pdo) {

            //
            $this->pdo = $pdo;

            //
            $this->token = new \Identity\Token($this->pdo);

        }

        //
        public function insertUser($request) {

            //generate ID
            if(!isset($request['id'])){$request['id'] = $this->token->new_id('usr');}


            $columns = "";

            //2022-03-26 03:08:18.35789
            //date("Y-m-d H:i:s")
            if(isset($request['login_last'])){$request['login_last']=date("Y-m-d H:i:s");}
            if(isset($request['status'])){$request['status']='1';}

            // rearrnging
            if(isset($request['authorize'])){$columns.="user_authorize,";}

            if(isset($request['id'])){$columns.="user_id,";}
            if(isset($request['attributes'])){$columns.="user_attributes,";}
            if(isset($request['alias'])){$columns.="user_alias,";}
            if(isset($request['login_last'])){$columns.="user_login_last,";}
            if(isset($request['status'])){$columns.="user_status,";}
            if(isset($request['validation'])){$columns.="user_validation,";}
            if(isset($request['welcome'])){$columns.="user_welcome,";}
            
            if(isset($request['person'])){$columns.="person_id,";}

            $columns.= "app_id,";
            $columns.= "event_id,";
            $columns.= "process_id";

            $values = "";
            //if(isset($request['authorize'])){$values.=":user_authorize,";}		

            if(isset($request['id'])){$values.=":user_id,";}
            if(isset($request['attributes'])){$values.=":user_attributes,";}
            if(isset($request['alias'])){$values.=":user_alias,";}
            //if(isset($request['authorize'])){$values.=":user_authorize,";}
            if(isset($request['login_last'])){$values.=":user_login_last,";}
            if(isset($request['status'])){$values.=":user_status,";}
            if(isset($request['validation'])){$values.=":user_validation,";}
            if(isset($request['welcome'])){$values.=":user_welcome,";}
            if(isset($request['person'])){$values.=":person_id,";}

            $values.= ":app_id,";
            $values.= ":event_id,";
            $values.= ":process_id";

            // prepare statement for insert
            $sql = "INSERT INTO {$request['domain']} (";
            $sql.= $columns;
            $sql.= ") VALUES (";
            //https://x-team.com/blog/storing-secure-passwords-with-postgresql/
            $sql.= "crypt('".$request['authorize']."', gen_salt('bf')),"; // custom case
            $sql.= $values;
            $sql.= ")";
            $sql.= " RETURNING " . prefixed($request['domain']) . "_id";

            //echo $sql;exit;
    
            //
            $statement = $this->pdo->prepare($sql);
            
            // pass values to the statement
            //if(isset($request['authorize'])){$statement->bindValue('user_authorize',$request['authorize']);}

            if(isset($request['id'])){$statement->bindValue('user_id',$request['id']);}
            if(isset($request['attributes'])){$statement->bindValue('user_attributes',$request['attributes']);}
            if(isset($request['alias'])){$statement->bindValue('user_alias',$request['alias']);}
            if(isset($request['login_last'])){$statement->bindValue('user_login_last',$request['login_last']);}
            if(isset($request['status'])){$statement->bindValue('user_status',$request['status']);}
            if(isset($request['validation'])){$statement->bindValue('user_validation',$request['validation']);}
            if(isset($request['welcome'])){$statement->bindValue('user_welcome',$request['welcome']);}      	
            if(isset($request['person'])){$statement->bindValue('person_id',$request['person']);}

            $statement->bindValue(':app_id', $request['app']);
            $statement->bindValue(':event_id', $this->token->event_id());
            $statement->bindValue(':process_id', $this->token->process_id());
            
            // execute the insert statement
            //echo json_encode($statement); exit;
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['0'];

            // return generated id
            return $data;
        
        }

        //
        public function selectUsers($request) {

            //echo json_encode($request); //exit;

            //$token = new \Identity\Token($this->pdo);
            $token = $this->token->validatedToken($request['token']);

            // Retrieve data ONLY if token  
            if($token) {
                
                // domain, app always present
                if(!isset($request['per'])){$request['per']=20;}
                if(!isset($request['page'])){$request['page']=1;}
                if(!isset($request['limit'])){$request['limit']=100;}
                //
                $conditions = "";
                $domain = $request['domain'];
                $prefix = prefixed($domain);

                //
                $columns = "

                    user_id,
                    user_attributes,
                    user_alias,
                    user_authorize,
                    user_login_last,
                    user_status,
                    user_validation,
                    user_welcome,
                    person_id,
                    app_id,
                    time_finished

                ";

                $table = $domain;

                //
                $start = 0;

                //
                if(isset($request['page'])) {

                    //
                    $start = ($request['page'] - 1) * $request['per'];
                
                }

                //
                if(!empty($request['id'])) {

                    $conditions.= " WHERE";
                    $conditions.= " " . $prefix . "_id = :id ";
                    $conditions.= " AND active = 1 ";
                    $conditions.= " ORDER BY time_finished DESC ";
                    
                    $subset = " LIMIT 1";

                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= " FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;
                    
                    //echo json_encode($request['id']);
                    //echo '<br/>';
                    //echo $sql; exit;

                    //
                    $statement = $this->pdo->prepare($sql);

                    // bind value to the :id parameter
                    $statement->bindValue(':id', $request['id']);

                    //echo $sql; exit;

                } elseif(isset($request['email']) && isset($request['authorize']) && isset($request['person'])) {

                    //echo json_encode($request);//exit;

                    $conditions.= " WHERE";
                    $conditions.= " person_id = :person ";
                    $conditions.= " AND " . " user_authorize = crypt(:authorize, user_authorize)";
                    $conditions.= " AND active = 1 ";
                    
                    $subset = " LIMIT 1";

                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= " FROM " . "users";
                    $sql.= $conditions;
                    $sql.= $subset;
                    
                    //echo $sql; //exit;

                    //
                    $statement = $this->pdo->prepare($sql);

                    // bind value to the :id parameter
                    $statement->bindValue(':person', $request['person']);
                    $statement->bindValue(':authorize', $request['authorize']);

                    //echo $sql; //exit;

                } else {

                    $conditions = "";
                    $refinements = "";

                    if(isset($request['attributes'])){$refinements.="user_attributes"." ILIKE "."'%".$request['attributes']."%' AND ";}
                    if(isset($request['alias'])){$refinements.="user_alias"." ILIKE "."'%".$request['alias']."%' AND ";}
                    if(isset($request['authorize'])){$refinements.="user_authorize"." = "."'".$request['authorize']."' AND ";}
                    if(isset($request['login_last'])){$refinements.="user_login_last"." ILIKE "."'%".$request['login_last']."%' AND ";}
                    if(isset($request['status'])){$refinements.="user_status"." ILIKE "."'%".$request['status']."%' AND ";}
                    if(isset($request['validation'])){$refinements.="user_validation"." ILIKE "."'%".$request['validation']."%' AND ";}
                    if(isset($request['welcome'])){$refinements.="user_welcome"." ILIKE "."'%".$request['welcome']."%' AND ";}

                    //echo $conditions . 'conditions1<br/>';
                    //echo $refinements . 'refinements1<br/>';
                    
                    $conditions.= " WHERE ";
                    $conditions.= $refinements;
                    $conditions.= " active = 1 ";
                    $conditions.= " ORDER BY time_finished DESC ";
                    $subset = " OFFSET {$start}" . " LIMIT {$request['per']}";
                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= "FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;

                    //echo $conditions . 'conditions2<br/>';
                    //echo $refinements . 'refinements2<br/>';

                    //echo $sql; exit;
                    
                    //
                    $statement = $this->pdo->prepare($sql);

                }
                    
                // execute the statement
                $statement->execute();

                //
                $results = [];
                $total = $statement->rowCount();
                $pages = ceil($total/$request['per']); //
                //$current = 1; // current page
                //$limit = $result['limit'];
                //$max = $result['max'];

                //echo var_dump($statement->rowCount()); exit;

                //
                if($statement->rowCount() > 0) {

                    //
                    $data = array();
                
                    //
                    while($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
        
                        //
                        $data[] = [

                            'id' => $row['user_id'],
                            'attributes' => json_decode($row['user_attributes']),
                            'alias' => $row['user_alias'],
                            //'authorize' => $row['user_authorize'],
                            'login_last' => $row['user_login_last'],
                            'status' => $row['user_status'],
                            'validation' => $row['user_validation'],
                            'welcome' => json_decode($row['user_welcome']),
                            'person' => $row['person_id'],
                            'app' => $row['app_id'],
                            'when' => $row['time_finished'],

                        ];

                    }

                    $code = 200;
                    $message = "OK";

                } else {

                    //
                    $data = NULL;
                    $code = 204;
                    $message = "No Content";

                }

            } else {

                //
                $data[] = NULL;
                $code = 401;
                $message = "Forbidden - Valid token required";

            }

            $results = array(

                'status' => $code,
                'message' => $message,
                'metadata' => [
                    'page' => $request['page'],
                    'pages' => $pages,
                    'total' => $total
                ],
                'data' => $data,
                'log' => [
                    'process' => $process_id = $this->token->process_id(),
                    'event' => $event_id = $this->token->event_id($process_id)
                ]

            );

            //
            return $results;

        }

        //
        public function updateUser($request) {

            //
            $domain = $request['domain'];
            $table = prefixed($domain);
            $id = $request['id'];

            //
            $set = "";

            if(isset($request['id'])){$set.= " user_id = :user_id ";}		
            if(isset($request['attributes'])){$set.= " user_attributes = :user_attributes ";}		
            if(isset($request['alias'])){$set.= " user_alias = :user_alias ";}		
            if(isset($request['authorize'])){$set.= " user_authorize = :user_authorize ";}		
            if(isset($request['login_last'])){$set.= " user_login_last = :user_login_last ";}		
            if(isset($request['status'])){$set.= " user_status = :user_status ";}		
            if(isset($request['validation'])){$set.= " user_validation = :user_validation ";}		
            if(isset($request['welcome'])){$set.= " user_welcome = :user_welcome ";}
	
            //
            $set = str_replace('  ',',',$set);

            // GET table name
            $condition = $table."_id = :id";
            $condition.= " RETURNING " . $table . "_id";

            //echo json_encode($set);
            //echo json_encode($condition);
            //exit;

            /**
             * Update stock based on the specified id
             * @param int $id
             * @param string $symbol
             * @param string $company
             * @return int
             */

            // sql statement to update a row in the stock table
            $sql = "UPDATE {$domain} SET ";
            $sql.= $set;
            $sql.= " WHERE ";
            $sql.= $condition;

            //echo $sql; exit;

            $statement = $this->pdo->prepare($sql);
    
            // bind values to the statement

            if(isset($request['id'])){$statement->bindValue(':user_id', $request['id']);}		
            if(isset($request['attributes'])){$statement->bindValue(':user_attributes', $request['attributes']);}		
            if(isset($request['alias'])){$statement->bindValue(':user_alias', $request['alias']);}		
            if(isset($request['authorize'])){$statement->bindValue(':user_authorize', $request['authorize']);}		
            if(isset($request['login_last'])){$statement->bindValue(':user_login_last', $request['login_last']);}		
            if(isset($request['status'])){$statement->bindValue(':user_status', $request['status']);}		
            if(isset($request['validation'])){$statement->bindValue(':user_validation', $request['validation']);}		
            if(isset($request['welcome'])){$statement->bindValue(':user_welcome', $request['welcome']);}

            $statement->bindValue(':id', $id);

            // update data in the database
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['0'];

            // return generated id
            return $data;

            // return the number of row affected
            //return $statement->rowCount();

        }

        //
        public function deleteUser($request) {

            $id = $request['id'];
            $domain = $request['domain'];
            $column = prefixed($domain) . '_id';
            $sql = 'DELETE FROM ' . $domain . ' WHERE '.$column.' = :id';
            //echo $id; //exit
            //echo $column; //exit;
            //echo $domain; //exit;
            //echo $sql; //exit

            $statement = $this->pdo->prepare($sql);
            //$statement->bindParam(':column', $column);
            $statement->bindValue(':id', $id);
            $statement->execute();
            return $statement->rowCount();

        }

    }

    //
    class Profile {

        //
        private $pdo;
    
        //
        public function __construct($pdo) {

            //
            $this->pdo = $pdo;

            //
            $this->token = new \Identity\Token($this->pdo);
            $this->user = new \Identity\User($this->pdo);
            $this->person = new \Identity\Person($this->pdo);

        }

        //
        public function insertProfile($request) {

            //generate ID
            if(!isset($request['id'])){$request['id'] = $this->token->new_id('prf');}

            $columns = "";
            if(isset($request['id'])){$columns.="profile_id,";}
            if(isset($request['attributes'])){$columns.="profile_attributes,";}
            if(isset($request['images'])){$columns.="profile_images,";}
            if(isset($request['bio'])){$columns.="profile_bio,";}
            if(isset($request['headline'])){$columns.="profile_headline,";}
            if(isset($request['access'])){$columns.="profile_access,";}
            if(isset($request['status'])){$columns.="profile_status,";}

            if(isset($request['user'])){$columns.="user_id,";}

            $columns.= "app_id,";
            $columns.= "event_id,";
            $columns.= "process_id";

            $values = "";
            if(isset($request['id'])){$values.=":profile_id,";}
            if(isset($request['attributes'])){$values.=":profile_attributes,";}
            if(isset($request['images'])){$values.=":profile_images,";}
            if(isset($request['bio'])){$values.=":profile_bio,";}
            if(isset($request['headline'])){$values.=":profile_headline,";}
            if(isset($request['access'])){$values.=":profile_access,";}
            if(isset($request['status'])){$values.=":profile_status,";}

            if(isset($request['user'])){$values.=":user_id,";}

            $values.= ":app_id,";
            $values.= ":event_id,";
            $values.= ":process_id";

            // prepare statement for insert
            $sql = "INSERT INTO {$request['domain']} (";
            $sql.= $columns;
            $sql.= ") VALUES (";
            $sql.= $values;
            $sql.= ")";
            $sql.= " RETURNING " . prefixed($request['domain']) . "_id";

            //echo $sql;exit;
    
            //
            $statement = $this->pdo->prepare($sql);
            
            // pass values to the statement
            if(isset($request['id'])){$statement->bindValue('profile_id',$request['id']);}
            if(isset($request['attributes'])){$statement->bindValue('profile_attributes',$request['attributes']);}
            if(isset($request['images'])){$statement->bindValue('profile_images',$request['images']);}
            if(isset($request['bio'])){$statement->bindValue('profile_bio',$request['bio']);}
            if(isset($request['headline'])){$statement->bindValue('profile_headline',$request['headline']);}
            if(isset($request['access'])){$statement->bindValue('profile_access',$request['access']);}
            if(isset($request['status'])){$statement->bindValue('profile_status',$request['status']);}

            if(isset($request['user'])){$statement->bindValue('user_id',$request['user']);}

            $statement->bindValue(':app_id', $request['app']);
            $statement->bindValue(':event_id', $this->token->event_id());
            $statement->bindValue(':process_id', $this->token->process_id());
            
            // execute the insert statement
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['0'];

            // return generated id
            return $data;
        
        }

        //
        public function selectProfiles($request) {

            //echo json_encode($request); exit;

            //$token = new \Identity\Token($this->pdo);
            $token = $this->token->validatedToken($request['token']);

            // Retrieve data ONLY if token  
            if($token) {
                
                // domain, app always present
                if(!isset($request['per'])){$request['per']=20;}
                if(!isset($request['page'])){$request['page']=1;}
                if(!isset($request['limit'])){$request['limit']=100;}
                //
                $conditions = "";
                $domain = $request['domain'];
                $prefix = prefixed($domain);

                //
                $columns = "

                profile_id,
                profile_attributes,
                profile_images,
                profile_bio,
                profile_headline,
                profile_access,
                profile_status,
                user_id,
                app_id,
                time_finished

                ";

                $table = $domain;

                //
                $start = 0;

                //
                if(isset($request['page'])) {

                    //
                    $start = ($request['page'] - 1) * $request['per'];
                
                }

                //
                if(!empty($request['id'])) {

                    $conditions.= " WHERE";
                    $conditions.= " " . $prefix . "_id = :id ";
                    $conditions.= " AND active = 1 ";
                    $conditions.= " ORDER BY time_finished DESC ";
                    
                    $subset = " LIMIT 1";

                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= " FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;
                    
                    //echo json_encode($request['id']);
                    //echo '<br/>';
                    //echo $sql; exit;

                    //
                    $statement = $this->pdo->prepare($sql);

                    // bind value to the :id parameter
                    $statement->bindValue(':id', $request['id']);

                    //echo $sql; exit;

                } elseif(!empty($request['user'])) {

                    $conditions.= " WHERE";
                    $conditions.= " user_id = :user ";
                    $conditions.= " AND active = 1 ";
                    $conditions.= " ORDER BY time_finished DESC ";
                    
                    $subset = " LIMIT 1";

                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= " FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;
                    
                    //echo json_encode($request['id']);
                    //echo '<br/>';
                    //echo $sql; exit;

                    //
                    $statement = $this->pdo->prepare($sql);

                    // bind value to the :id parameter
                    $statement->bindValue(':user', $request['user']);

                    //echo $sql; exit;

                } else {

                    $conditions = "";
                    $refinements = "";
                    
                    if(isset($request['id'])){$refinements.="profile_id"." = "."'".$request['id']."' AND ";}
                    if(isset($request['attributes'])){$refinements.="profile_attributes"." ILIKE "."'%".$request['attributes']."%' AND ";}
                    if(isset($request['images'])){$refinements.="profile_images"." ILIKE "."'%".$request['images']."%' AND ";}
                    if(isset($request['bio'])){$refinements.="profile_bio"." ILIKE "."'%".$request['bio']."%' AND ";}
                    if(isset($request['headline'])){$refinements.="profile_headline"." ILIKE "."'%".$request['headline']."%' AND ";}
                    if(isset($request['access'])){$refinements.="profile_access"." = "."'".$request['access']."' AND ";}
                    if(isset($request['status'])){$refinements.="profile_status"." = "."'".$request['status']."' AND ";}
                    if(isset($request['user'])){$refinements.="user_id"." = "."'".$request['user']."' AND ";}

                    //echo $conditions . 'conditions1<br/>';
                    //echo $refinements . 'refinements1<br/>';
                    
                    $conditions.= " WHERE ";
                    $conditions.= $refinements;
                    $conditions.= " active = 1 ";
                    $conditions.= " ORDER BY time_finished DESC ";
                    $subset = " OFFSET {$start}" . " LIMIT {$request['per']}";
                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= "FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;

                    //echo $conditions . 'conditions2<br/>';
                    //echo $refinements . 'refinements2<br/>';

                    //echo $sql; exit;
                    
                    //
                    $statement = $this->pdo->prepare($sql);

                }
                    
                // execute the statement
                $statement->execute();

                //
                $results = [];
                $total = $statement->rowCount();
                $pages = ceil($total/$request['per']); //
                //$current = 1; // current page
                //$limit = $result['limit'];
                //$max = $result['max'];

                //
                if($statement->rowCount() > 0) {

                    //
                    $data = array();
                
                    //
                    while($row = $statement->fetch(\PDO::FETCH_ASSOC)) {

                        //echo var_dump($data);

                        //exit;

                        // User information...
                        $request['id'] = $row['user_id']; // get user ID for selectUsers
                        //echo "user: " . $request['id'];
                        $request['domain'] = 'users'; // switch domain
                        $user_details = $this->user->selectUsers($request); // collect all user details returned from selectUsers

                        //echo var_dump($user_details);

                        //exit;

                        //$data[]['alias'] = $user_details['data'][0]['alias']; // add alias to $data

                        $request['id'] = $user_details['data'][0]['person']; // get user ID for selectUsers
                        //echo "person: " . $request['id'];
                        $request['domain'] = 'persons'; // switch domain
                        $person_details = $this->person->selectPersons($request); // collect all user details returned from selectUsers
                        //$data[]['name_first'] = $person_details['data'][0]['name_first']; // add first name to $data
                        //$data[]['name_last'] = $person_details['data'][0]['name_last']; // add last name to $data

                        //
                        $data[] = [

                            'id' => $row['profile_id'],
                            'attributes' => json_decode($row['profile_attributes']),
                            'images' => json_decode($row['profile_images']),
                            'bio' => $row['profile_bio'],
                            'headline' => $row['profile_headline'],
                            'access' => $row['profile_access'],
                            'status' => $row['profile_status'],
                            'user' => $row['user_id'],
                            'app' => $row['app_id'],
                            'when' => $row['time_finished'],
                            'name_first' => $person_details['data'][0]['name_first'],
                            'name_last' => $person_details['data'][0]['name_last'],
                            'alias' => $user_details['data'][0]['alias']

                        ];

                    }

                    //
                    //exit;

                    $code = 200;
                    $message = "OK";

                } else {

                    //
                    $data = NULL;
                    $code = 204;
                    $message = "No Content";

                }

            } else {

                //
                $data[] = NULL;
                $code = 401;
                $message = "Forbidden - Valid token required";

            }

            $results = array(

                'status' => $code,
                'message' => $message,
                'metadata' => [
                    'page' => $request['page'],
                    'pages' => $pages,
                    'total' => $total
                ],
                'data' => $data,
                'log' => [
                    'process' => $process_id = $this->token->process_id(),
                    'event' => $event_id = $this->token->event_id($process_id)
                ]

            );

            //
            return $results;

        }

        //
        public function updateProfile($request) {

            //
            $domain = $request['domain'];
            $table = prefixed($domain);
            $id = $request['id'];

            //
            $set = "";

            if(isset($request['id'])){$set.= " profile_id = :profile_id ";}
            if(isset($request['attributes'])){$set.= " profile_attributes = :profile_attributes ";}
            if(isset($request['images'])){$set.= " profile_images = :profile_images ";}
            if(isset($request['bio'])){$set.= " profile_bio = :profile_bio ";}
            if(isset($request['headline'])){$set.= " profile_headline = :profile_headline ";}
            if(isset($request['access'])){$set.= " profile_access = :profile_access ";}
            if(isset($request['status'])){$set.= " profile_status = :profile_status ";}

            //
            $set = str_replace('  ',',',$set);

            // GET table name
            $condition = $table."_id = :id";
            $condition.= " RETURNING " . $table . "_id";

            //echo json_encode($set);
            //echo json_encode($condition);
            //exit;

            /**
             * Update stock based on the specified id
             * @param int $id
             * @param string $symbol
             * @param string $company
             * @return int
             */

            // sql statement to update a row in the stock table
            $sql = "UPDATE {$domain} SET ";
            $sql.= $set;
            $sql.= " WHERE ";
            $sql.= $condition;

            //echo $sql; exit;

            $statement = $this->pdo->prepare($sql);
    
            // bind values to the statement
            if(isset($request['id'])){$statement->bindValue(':profile_id', $request['id']);}
            if(isset($request['attributes'])){$statement->bindValue(':profile_attributes', $request['attributes']);}
            if(isset($request['images'])){$statement->bindValue(':profile_images', $request['images']);}
            if(isset($request['bio'])){$statement->bindValue(':profile_bio', $request['bio']);}
            if(isset($request['headline'])){$statement->bindValue(':profile_headline', $request['headline']);}
            if(isset($request['access'])){$statement->bindValue(':profile_access', $request['access']);}
            if(isset($request['status'])){$statement->bindValue(':profile_status', $request['status']);}
            $statement->bindValue(':id', $id);

            // update data in the database
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['0'];

            // return generated id
            return $data;

            // return the number of row affected
            //return $statement->rowCount();

        }

        //
        public function deleteProfile($request) {

            $id = $request['id'];
            $domain = $request['domain'];
            $column = prefixed($domain) . '_id';
            $sql = 'DELETE FROM ' . $domain . ' WHERE '.$column.' = :id';
            //echo $id; //exit
            //echo $column; //exit;
            //echo $domain; //exit;
            //echo $sql; //exit

            $statement = $this->pdo->prepare($sql);
            //$statement->bindParam(':column', $column);
            $statement->bindValue(':id', $id);
            $statement->execute();
            return $statement->rowCount();

        }

    }

    //
    class Partner {

        //
        private $pdo;
    
        //
        public function __construct($pdo) {

            //
            $this->pdo = $pdo;

            //
            $this->token = new \Identity\Token($this->pdo);

        }

        //
        public function insertPartner($request) {

            //generate ID
            if(!isset($request['id'])){$request['id'] = $this->token->new_id('par');}

            // INSERT OBJECT - COLUMNS
            $columns = "";
            if(isset($request['id'])){$columns.="partner_id,";}		
            if(isset($request['attributes'])){$columns.="partner_attributes,";}		
            if(isset($request['type'])){$columns.="partner_type,";}		
            if(isset($request['status'])){$columns.="partner_status,";}		
            if(isset($request['organization'])){$columns.="partner_organization,";}		
            if(isset($request['headquarters'])){$columns.="partner_headquarters,";}		
            if(isset($request['locations'])){$columns.="partner_locations,";}		
            if(isset($request['user'])){$columns.="user_id,";}		
            
            $columns.= "app_id,";
            $columns.= "event_id,";
            $columns.= "process_id";

            // INSERT OBJECT - VALUES
            $values = "";
            if(isset($request['id'])){$values.=":partner_id,";}		
            if(isset($request['attributes'])){$values.=":partner_attributes,";}		
            if(isset($request['type'])){$values.=":partner_type,";}		
            if(isset($request['status'])){$values.=":partner_status,";}		
            if(isset($request['organization'])){$values.=":partner_organization,";}		
            if(isset($request['headquarters'])){$values.=":partner_headquarters,";}		
            if(isset($request['locations'])){$values.=":partner_locations,";}
            if(isset($request['user'])){$values.=":user,";}
            
            $values.= ":app_id,";
            $values.= ":event_id,";
            $values.= ":process_id";

            // prepare statement for insert
            $sql = "INSERT INTO {$request['domain']} (";
            $sql.= $columns;
            $sql.= ") VALUES (";
            $sql.= $values;
            $sql.= ")";
            $sql.= " RETURNING " . prefixed($request['domain']) . "_id";
    
            //
            $statement = $this->pdo->prepare($sql);
            
            // INSERT OBJECT - BIND VALUES pass values to the statement
            if(isset($request['id'])){$statement->bindValue('partner_id',$request['id']);}		
            if(isset($request['attributes'])){$statement->bindValue('partner_attributes',$request['attributes']);}		
            if(isset($request['type'])){$statement->bindValue('partner_type',$request['type']);}		
            if(isset($request['status'])){$statement->bindValue('partner_status',$request['status']);}		
            if(isset($request['organization'])){$statement->bindValue('partner_organization',$request['organization']);}		
            if(isset($request['headquarters'])){$statement->bindValue('partner_headquarters',$request['headquarters']);}		
            if(isset($request['locations'])){$statement->bindValue('partner_locations',$request['locations']);}
            if(isset($request['user'])){$statement->bindValue('user',$request['user']);}		
                  
            $statement->bindValue(':app_id', $request['app']);
            $statement->bindValue(':event_id', $this->token->event_id());
            $statement->bindValue(':process_id', $this->token->process_id());
            
            // UPDATE ID
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['partner_id'];

            return $data;
        
        }

        //
        public function selectPartners($request) {

            //echo json_encode($request); exit;

            //$token = new \Identity\Token($this->pdo);
            $token = $this->token->validatedToken($request['token']);

            // Retrieve data ONLY if token  
            if($token) {
                
                // domain, app always present
                if(!isset($request['per'])){$request['per']=20;}
                if(!isset($request['page'])){$request['page']=1;}
                if(!isset($request['limit'])){$request['limit']=100;}

                //
                $conditions = "";
                $domain = $request['domain'];
                $prefix = prefixed($domain);

                // SELECT OBJECT - COLUMNS
                $columns = "

                partner_ID,
                partner_attributes,
                partner_type,
                partner_status,
                partner_organization,
                partner_headquarters,	
                partner_locations,
                user_id,
                app_id,			
                time_updated,		
                time_finished	

                ";

                $table = $domain;

                //
                $start = 0;

                //
                if(isset($request['page'])) {

                    //
                    $start = ($request['page'] - 1) * $request['per'];
                
                }

                //
                if(!empty($request['id'])) {

                    $conditions.= " WHERE";
                    $conditions.= " " . $prefix . "_id = :id ";
                    $conditions.= " AND active = 1 ";
                    $conditions.= " ORDER BY time_finished DESC ";
                    
                    $subset = " LIMIT 1";

                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= " FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;
                    
                    //echo json_encode($request['id']);
                    //echo '<br/>';
                    //echo $sql; exit;

                    //
                    $statement = $this->pdo->prepare($sql);

                    // bind value to the :id parameter
                    $statement->bindValue(':id', $request['id']);

                    //echo $sql; exit;

                } elseif(!empty($request['email'])) {

                    $conditions.= " WHERE";
                    $conditions.= " person_email = :email ";
                    //$conditions.= " person_email = '{$request['email']}' ";
                    $conditions.= " AND active = 1 ";
                    
                    $subset = " LIMIT 1";

                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= " FROM " . " persons ";//$table;
                    $sql.= $conditions;
                    $sql.= $subset;
                    
                    //echo json_encode($request['id']);
                    //echo '<br/>';
                    //echo $sql; exit;

                    //
                    $statement = $this->pdo->prepare($sql);

                    // bind value to the :id parameter
                    $statement->bindValue(':email', $request['email']);

                    //echo $sql; //exit;

                } else {

                    $conditions = "";
                    $refinements = "";
                    // SELECT OBJECT - WHERE CLAUSES
                    // SKIP ID		
                    if(isset($request['attributes'])){$refinements.="partner_attributes"." ILIKE "."'%".$request['attributes']."%' AND ";}		
                    if(isset($request['type'])){$refinements.="partner_type"." = "."'".$request['type']."' AND ";}		
                    if(isset($request['status'])){$refinements.="partner_status"." = "."'".$request['status']."' AND ";}		
                    if(isset($request['organization'])){$refinements.="partner_organization"." ILIKE "."'%".$request['organization']."%' AND ";}		
                    if(isset($request['headquarters'])){$refinements.="partner_headquarters"." ILIKE "."'%".$request['headquarters']."%' AND ";}		
                    //if(isset($request['locations'])){$refinements.="partner_locations"." ILIKE "."'%".$request['locations']."%' AND ";}
                    if(isset($request['user'])){$refinements.="user_id"." = "."'".$request['user']."' AND ";}		

                    //echo $conditions . 'conditions1<br/>';
                    //echo $refinements . 'refinements1<br/>';
                    
                    $conditions.= " WHERE ";
                    $conditions.= $refinements;
                    $conditions.= " active = 1 ";
                    $conditions.= " ORDER BY time_finished DESC ";
                    $subset = " LIMIT 1";
                    //$subset = " OFFSET {$start}" . " LIMIT {$request['per']}";

                    // build SQL statement
                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= "FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;

                    //echo $conditions . 'conditions2<br/>';
                    //echo $refinements . 'refinements2<br/>';

                    //echo $sql; exit;
                    
                    //
                    $statement = $this->pdo->prepare($sql);

                }

                //echo $sql;//exit;
                    
                // execute the statement
                $statement->execute();

                //
                $results = [];
                $total = $statement->rowCount();
                $pages = ceil($total/$request['per']); //
                //$current = 1; // current page
                //$limit = $result['limit'];
                //$max = $result['max'];

                //
                if($statement->rowCount() > 0) {

                    //
                    $data = array();
                
                    //
                    while($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
        
                        // SELECT OBJECT - DATA ARRAY
                        $data[] = [

                            'id' => $row['partner_id'],		
                            'attributes' => json_decode($row['partner_attributes']),		
                            'type' => $row['partner_type'],		
                            'status' => $row['partner_status'],		
                            'organization' => $row['partner_organization'],		
                            'headquarters' => $row['partner_headquarters'],		
                            'locations' => json_decode($row['partner_locations']),
                            'user' => $row['user_id'],		
                            'app' => $row['app_id'],
                            'updated' => $row['time_updated'],		
                            'when' => $row['time_finished'],

                        ];

                    }

                    $code = 200;
                    $message = "OK";

                } else {

                    //
                    $data = NULL;
                    $code = 204;
                    $message = "No Content";

                }

            } else {

                //
                $data[] = NULL;
                $code = 401;
                $message = "Forbidden - Valid token required";

            }

            $results = array(

                'status' => $code,
                'message' => $message,
                'metadata' => [
                    'page' => $request['page'],
                    'pages' => $pages,
                    'total' => $total
                ],
                'data' => $data,
                'log' => [
                    'process' => $process_id = $this->token->process_id(),
                    'event' => $event_id = $this->token->event_id($process_id)
                ]

            );

            //
            return $results;

        }

        //
        public function updatePartner($request) {

            //
            $domain = $request['domain'];
            $table = prefixed($domain);
            $id = $request['id'];

            //
            $set = "";
            //if(isset($request['id'])){$set.= " person_id = :person_id, ";}
            // SKIP as ID won't be getting UPDATED		
            if(isset($request['attributes'])){$set.= " partner_attributes = :partner_attributes ";}		
            if(isset($request['type'])){$set.= " partner_type = :partner_type ";}		
            if(isset($request['status'])){$set.= " partner_status = :partner_status ";}		
            if(isset($request['organization'])){$set.= " partner_organization = :partner_organization ";}		
            if(isset($request['headquarters'])){$set.= " partner_headquarters = :partner_headquarters ";}		
            if(isset($request['locations'])){$set.= " partner_locations = :partner_locations ";}
            if(isset($request['active'])){$set.= " active = :active ";}		

            //
            $set = str_replace('  ',',',$set);

            // GET table name
            $condition = $table."_id = :id";
            $condition.= " RETURNING " . $table . "_id";

            //echo json_encode($set);
            //echo json_encode($condition);
            //exit;

            /**
             * Update stock based on the specified id
             * @param int $id
             * @param string $symbol
             * @param string $company
             * @return int
             */

            // sql statement to update a row in the stock table
            $sql = "UPDATE {$domain} SET ";
            $sql.= $set;
            $sql.= " WHERE ";
            $sql.= $condition;

            //echo $sql; exit;

            $statement = $this->pdo->prepare($sql);
    
            // bind values to the statement
            //if(isset($request['id'])){$statement->bindValue(':partner_id', $request['id']);}
            if(isset($request['attributes'])){$statement->bindValue(':partner_attributes', $request['attributes']);}
            if(isset($request['type'])){$statement->bindValue(':partner_type', $request['type']);}
            if(isset($request['status'])){$statement->bindValue(':partner_status', $request['status']);}
            if(isset($request['organization'])){$statement->bindValue(':partner_organization', $request['organization']);}
            if(isset($request['headquarters'])){$statement->bindValue(':partner_headquarters', $request['headquarters']);}
            if(isset($request['locations'])){$statement->bindValue(':partner_locations', $request['locations']);}
            if(isset($request['active'])){$statement->bindValue(':active', $request['active']);}
            $statement->bindValue(':id', $id);

            // update data in the database
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['partner_id'];

            // return generated id
            return $data;

            // return the number of row affected
            //return $statement->rowCount();

        }

        //
        public function deletePartner($request) {

            $id = $request['id'];
            $domain = $request['domain'];
            $column = prefixed($domain) . '_id';
            $sql = 'DELETE FROM ' . $domain . ' WHERE '.$column.' = :id';
            //echo $id; //exit
            //echo $column; //exit;
            //echo $domain; //exit;
            //echo $sql; //exit

            $statement = $this->pdo->prepare($sql);
            //$statement->bindParam(':column', $column);
            $statement->bindValue(':id', $id);
            $statement->execute();
            return $statement->rowCount();

        }

    }
    

?>