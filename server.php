<?php
    class Server
    {
        const SERVER_NAME = 'localhost';
        const USER = 'root';
        const PASS = 'admin';
        const DB_NAME = 'pin_reset';

        public $conn;

        //Establishes connection with mysql server and selects 'users' database
        function connect()
        {
            $this->conn = new mysqli(self::SERVER_NAME, self::USER, self::PASS);
            $this->conn->query("use ".self::DB_NAME);
        }

        //Add email and verification id to database
        function register($username, $email, $password)
        {
            if($this->exists($email))
                return 'Email already in use.';
			
			$hash = password_hash($password, PASSWORD_DEFAULT);
            $query = $this->conn->prepare("INSERT INTO user_info(username, email, password) VALUES(?, ?, ?);");
            $query->bind_param("sss", $username, $email, $hash);
            $query->execute();

            return 'Email successfully registered.';
        }
		
		function login($email, $password)
		{
			$auth = $this->auth($email, $password);
			if($auth['success'])
				$_SESSION['username'] = $auth['username'];
		}

        function exists($email)
        {
            $query = $this->conn->prepare("SELECT * FROM user_info WHERE email=?");
            $query->bind_param("s", $email);
            $query->execute();
            $result = $query->get_result();
            return $result->num_rows > 0;
        }
		
        //This function should be called when user resets pin in his locker successfully
        function update_id($email) //Generates the next iteration of verification id
        {
            $query = $this->conn->prepare("SELECT * FROM user_info WHERE email=?");
            $query->bind_param("s", $email);
            $query->execute();
            $result = $query->get_result();

            $old_id = $result->fetch_assoc()['verification_id'];
            $id = $old_id;
            for($i = 0; $i < 10_000; $i++)
            {
                //Get the first 4 digits of cube of the number
                $id = substr(strval($id*$id*$id), 0, 4);
            }

            $query = $this->conn->prepare("UPDATE user_info SET verification_id=? WHERE email=?");
            $query->bind_param("is", $id, $email);
            $query->execute();
            return "Verification id successfully updated to ".$id;
        }
		
		function set_id($email, $verification_id)
		{
			$query = $this->conn->prepare("UPDATE user_info SET verification_id=? WHERE email=?");
            $query->bind_param("is", $verification_id, $email);
            $query->execute();
            return "Verification id successfully updated to ".$verification_id;
		}

		function auth($email, $password)
		{
			$query = $this->conn->prepare("SELECT * FROM user_info WHERE email=?");
			$query->bind_param("s", $email);
			$query->execute();
			$result = $query->get_result();
			$values = $result->fetch_assoc();
			if($result->num_rows == 0)
				return Array('success'=>false);
			
			if(!password_verify($password, $values['password']))
				return Array('success'=>false);
			return Array(
				'username'=>$values['username'],
				'success'=>true
			);
		}
	
		function is_user_logged() { return isset($_SESSION['username']); }
		
        //Closes connection with database
        function close() { $conn->close(); }
    }
?>
