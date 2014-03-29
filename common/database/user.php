<?php
class User {
	public $id = null;
	public $username = null;
	public $firstname = null;
	public $password = null;
	public $surname = null;
	public $year = null;
	public $group = null;
	public $rank = null;
	
	// User Constructor
	// Reads the values from the database if provided,
	// or creates an empty new record
	public function User($db=0) {
		if ($db){
			// Get values from database record
			$this->id = $db->userID;
			$this->username = $db->username;
			$this->firstname = $db->firstname;
			$this->surname = $db->surname;
			$this->password = $db->password;
			$this->year = $db->year;
			$this->group = $db->ugroup;
			$this->rank = $db->rank;
		}else{
			// Create new record
			$this->id = -1;
		}
	}
	
	// Get class fields in string format for debuging
	public function __tostring(){
		$res = "id: '".$this->id."',<br>";
		$res .= "username: '".$this->username."',<br>";
		$res .= "password: '".$this->password."',<br>";
		$res .= "firstname: '".$this->firstname."',<br>";
		$res .= "surname: '".$this->surname."',<br>";
		$res .= "year: '".$this->year."',<br>";
		$res .= "group: '".$this->group."',<br>";
		$res .= "rank: '".$this->rank."'";
		return "{$res}";
	}
	
	// Save the record
	public function save(){
		global $handle;
		if ($this->id == -1){
			// Create SQL query
			$h = $handle->prepare("INSERT INTO user(username,password,firstname,surname,year,ugroup,rank) VALUES (?, ?, ?, ?, ?, ?, ?)")  or die("SQL Prepare: ".mysqli_error($handle));
			$h->bind_param('ssssisi',$this->username,$this->password,$this->firstname,$this->surname,$this->year,$this->group,$this->rank) or die("SQL Param: ".mysqli_error($handle));
			
			// Insert the new record into the table
			$h->execute() or die("<br><br>SQL Execute: ".mysqli_error($handle));
			$h->close();
			$this->id = $handle->insert_id;
		}else{
			// Update existing record
			function update($id, $col, $value, $int=false){
				// Update a field in an existing record
				global $handle;
				$res = "";
				if ($int){
					// Update integer field
					if (!is_numeric($value)){
						echo "<p>inputed value is not numeric! ($id, $col, $value, $int) </p>";
						return;
					}
					$res = "UPDATE user SET $col = $value WHERE userID = $id";
				}else{
					// Update string field
					$res = "UPDATE user SET $col = '$value' WHERE userID = $id";
				}

				// Run update query
				$handle->query($res) or die("<br><br>Query Error: ".mysqli_error($handle));
			}
			
			// Update each field using the function above
			update($this->id,"username",$this->username,false);
			update($this->id,"password",$this->password,false);
			update($this->id,"firstname",$this->firstname,false);
			update($this->id,"surname",$this->surname,false);
			update($this->id,"year",$this->year,true);
			update($this->id,"ugroup",$this->group,false);
			update($this->id,"rank",$this->rank,true);
		}
	}
	
	// Get a user by their userID
	public static function get($id){
		global $handle;
		
		// Validate for type and presence
		if (!is_numeric($id)){
			return null;
		}
		
		// Execute SQL to find record
		$res = $handle->query("SELECT * FROM user WHERE userID = $id") or die("Query Error: ".mysqli_error($handle));

		// Get the record from the SQL query
		$obj = $res->fetch_object();
		
		// Check the record exists
		if (!$obj){
			return null;
		}
		
		// Convert the record into a User class
		$user = new User($obj);
		if (!$user){
			return null;
		}
		
		return $user;
	}
	
	# Returns a boolean indicating if the user is a pupil
	public function isPupil(){
		return $this->rank == AUTH_PUPIL;
	}
	
	# Returns a boolean indicating if the user is a staff member (inc. admin)
	public function isStaff(){
		return $this->rank >= AUTH_STAFF;
	}

	# Returns a boolean indicating if the user is an admin
	public function isAdmin(){
		return $this->rank == AUTH_ADMIN;
	}
	
	public function tests(){
		return TestAssign::_search("WHERE year = {$this->year} OR year = -1 OR ugroup = '{$this->group}' ORDER BY assignID DESC");
	}
	
	// Get a user by their username
	public static function getUsername($un){
		global $handle;

		// Execute SQL to find record
		$res = $handle->query("SELECT * FROM user WHERE username = '$un'") or die("Query Error: ".mysqli_error($handle));

		// Get the record from the SQL query
		$obj = $res->fetch_object();
		
		// Check the record exists
		if (!$obj){
			return null;
		}
		
		// Convert the record into a User class
		$user = new User($obj);
		if (!$user){
			return null;
		}
		
		return $user;
	}
	
	// Function for searching and returning a list
	public static function _search($query){
		global $handle;

		$res = $handle->query("SELECT * FROM user ".$query);
		if (!$res){
			return;
		}

		$result = array();
		while($obj = $res->fetch_object()){ 
			array_push($result,new User($obj));
		}
		return $result;
	}
	
	// Get all users
	public static function all(){
		return User::_search("");
	}
	
	// Get all users
	public static function searchForSurname($surname){
		return User::_search("WHERE surname = '$surname'");
	}
}