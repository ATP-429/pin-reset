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

        //This function should be called when user resets pin in his locker successfully
        function update_id($email) //Generates the next iteration of verification id
        {
            $query = $this->conn->prepare("SELECT * FROM info WHERE email=?");
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

            $query = $this->conn->prepare("UPDATE info SET verification_id=? WHERE email=?");
            $query->bind_param("is", $id, $email);
            $query->execute();
            return "Verification id successfully updated to ".$id;
        }

        //Closes connection with database
        function close() { $conn->close(); }
    }
?>
