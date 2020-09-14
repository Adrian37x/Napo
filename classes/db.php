<?php
	
	class Database {
		private $conn;

		// tries to open a connection
		public function open() {
			$this->conn = new PDO("mysql:host=localhost;dbname=napo", "root", "");

			if (!$this->conn) {
				error("Couldn't connect to the database");
			}
		}

		// closes the connection
		public function close() {
			$this->conn = null;
		}

		// creates prepared statement
		public function prepare($statement) {
			return $this->conn->prepare($statement);
		}

		// executes sql statement
		public function run($statement, $args) {
			// replaces every "placeholder" in statement with the corresponding argument in array
			for ($i = 0; $i < count($args); $i++) {
				$statement->bindParam(':value' . $i, $args[$i]);
			}

			$statement->execute();

			$result = $statement->fetchAll();

			if (!$result) {
				return [];
			}

			return $result;
		}
	}

?>