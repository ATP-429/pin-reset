<?php
    class DataHandler
    {
        const SERVER_NAME = 'localhost';
        const USER = 'root';
        const PASS = 'admin';
        const DB_NAME = 'users';

        public $conn;

        //Establishes connection with mysql server and selects 'users' database
        function connect()
        {
            $this->conn = new mysqli(self::SERVER_NAME, self::USER, self::PASS);
            $this->conn->query("use ".self::DB_NAME);
        }

        //Add email and verification id to database
        function register($email, $verification_id)
        {
            if($this->exists($email))
                return 'Email already in use.';

            $query = $this->conn->prepare("INSERT INTO info(email, verification_id) VALUES(?, ?);");
            $query->bind_param("si", $email, $verification_id);
            $query->execute();

            return 'Email successfully registered.';
        }

        function exists($email)
        {
            $query = $this->conn->prepare("SELECT * FROM info WHERE email=?");
            $query->bind_param("s", $email);
            $query->execute();
            $result = $query->get_result();
            return $result->num_rows > 0;
        }

        //Closes connection with database
        function close() { $conn->close(); }
    }
?>
