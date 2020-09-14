<?php

    class User {
        public $id;
        public $firstname;
        public $lastname;
        public $handy;
        public $email;
        public $password;
        public $hashalg;

        // get all except current
        public static function get_all($userId) {
            global $db;

            $getAll = $db->prepare('SELECT id, firstname, lastname, handy, email FROM user WHERE id != :value0');

            $raw = $db->run($getAll, [$userId]);

            $users = [];

            if ($raw) {
                foreach ($raw as $row){
                    $user = new User();
    
                    $user->id = $row["id"];
                    $user->firstname = $row["firstname"];
                    $user->lastname = $row["lastname"];
                    $user->handy = $row["handy"];
                    $user->email = $row["email"];
    
                    array_push($users, $user);
                }
            }

            return $users;
        }

        // get all users by search pattern
        public static function search_by_name($userId, $firstname, $lastname) {
            global $db;

            $getAll = $db->prepare('SELECT id, firstname, lastname, handy, email FROM user WHERE id != :value0 AND (firstname LIKE :value1 OR lastname LIKE :value2)');

            $firstname = '%' . $firstname . '%';
            $lastname = '%' . $lastname . '%';

            $raw = $db->run($getAll, [$userId, $firstname, $lastname]);

            $users = [];

            if ($raw) {
                foreach ($raw as $row){
                    $user = new User();
    
                    $user->id = $row["id"];
                    $user->firstname = $row["firstname"];
                    $user->lastname = $row["lastname"];
                    $user->handy = $row["handy"];
                    $user->email = $row["email"];
    
                    array_push($users, $user);
                }
            }

            return $users;
        }

        // register user
        public static function register($firstname, $lastname, $handy, $email, $password) {
            global $db;

            // is there already is an account with that email
            if (User::get_by_email($email)) {
                return false;
            }

            $addUser = $db->prepare('INSERT INTO user (firstname, lastname, handy, email, password, hashalg) VALUES (:value0, :value1, :value2, :value3, :value4, :value5)');
    
            $db->run($addUser, [$firstname, $lastname, $handy, $email, hash("sha256", $password), "sha256"]);
    
            return User::login($email, $password);
        }

        // try to login user
		public static function login($email, $password) {
            global $db;
            
            $info = $db->run($db->prepare('SELECT password, hashalg, id FROM user WHERE email = :value0'), [$email]);
			
			if (count($info) == 0) {
				return false;
			}
			
			$info = $info[0];
			
			if ($info["password"] === hash($info["hashalg"], $password)) {
                $_SESSION["user_id"] = $info["id"];
				
				return true;
			} else {
				return false;
			}
        }

        // logout user
        public static function logout() {
            $_SESSION["user_id"] = null;
        }
        
        // get the user who's currently logged in
        public static function get_current() {
            if (!session("user_id")) {
                return;
            }
            
            return User::get_by_id(session("user_id"));
        }

        // get a user by his id
        public static function get_by_id($id) {
            global $db;

            $user = new User();
			$user->id = $id;
			
			$raw = $db->run($db->prepare('SELECT firstname, lastname, handy, email FROM user WHERE id = :value0'), [
                $id
            ])[0];

			$user->firstname = $raw["firstname"];
            $user->lastname = $raw["lastname"];
            $user->handy = $raw["handy"];
            $user->email = $raw["email"];

            return $user;
        }

        // get a user by his email
        public static function get_by_email($email) {
            global $db;
            
            $raw = $db->run($db->prepare('SELECT id FROM user WHERE email = :value0'), [
                $email
            ]);

            if (count($raw) == 1) {
                return User::get_by_id($raw[0]["id"]);
            }

            return false;
        }

        // saves edited user
        public static function save($id, $firstname, $lastname, $handy, $email, $currentEmail) {
            global $db;

            // is there already is an account with that email (except)
            if (User::get_by_email($email) && $currentEmail != $email) {
                return false;
            }

            $save = $db->prepare('UPDATE user SET firstname = :value0, lastname = :value1, handy = :value2, email = :value3 WHERE id = :value4');

            $db->run($save, [$firstname, $lastname, $handy, $email, $id]);

            return true;
        }

        // change password
        public static function change_password($id, $newPassword, $oldPassword) {
            global $db;

            $info = $db->run($db->prepare('SELECT password, hashalg FROM user WHERE id = :value0'), [$id])[0];
            
            // correct old password ?
			if ($info["password"] !== hash($info["hashalg"], $oldPassword)) {
				return false;
			}

            $db->run($db->prepare('UPDATE user SET password = :value0 WHERE id = :value1'), [hash("sha256", $newPassword), $id]);

            return true;
        }
    }

?>