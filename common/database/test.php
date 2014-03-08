<?php
class Test {
	public $id = null;
	public $title = null;
	public $userID = null;
	public $datecreated = null;
	
	// Test Constructor
	// Reads the values from the database if provided,
	// or creates an empty new record
	public function Test($db) {
		if ($db){
			// Get values from database record
			$this->id = $db->testID;
			$this->title = $db->title;
			$this->userID = $db->userID;
			$this->datecreated = $db->datecreated;
		}else{
			// Create new record
			$this->id = -1;
		}
	}
	
	// Get class fields in string format for debuging
	public function __tostring(){
		$res = "testID: '".$this->id."',<br>";
		$res .= "title: '".$this->title."',<br>";
		$res .= "userID: '".$this->userID."',<br>";
		$res .= "datecreated: '".$this->datecreated."',<br>";
		return "{$res}";
	}
	
	// Save the record
	public function save(){
		global $handle;
		if ($this->id == -1){
			// Create SQL query
			$h = $handle->prepare("INSERT INTO test(title,userID,datecreated) VALUES (?,?,?)")  or die("SQL Prepare: ".mysqli_error($handle));
			$h->bind_param('sis',$this->title,$this->userID,$this->datecreated) or die("SQL Param: ".mysqli_error($handle));
			
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
					$res = "UPDATE test SET $col = $value WHERE testID = $id";
				}else{
					// Update string field
					$res = "UPDATE test SET $col = '$value' WHERE testID = $id";
				}

				// Run update query
				$handle->query($res) or die("<br><br>Query Error: ".mysqli_error($handle));
			}
			
			// Update each field using the function above
			update($this->id,"title",$this->title,false);
			update($this->id,"userID",$this->userID,true);
			update($this->id,"datecreated",$this->datecreated,false);
		}
	}
	
	// Get a test by their testID
	public static function get($id){
		global $handle;
		
		// Validate for type and presence
		if (!is_numeric($id)){
			return null;
		}
		
		// Execute SQL to find record
		$res = $handle->query("SELECT * FROM test WHERE testID = $id") or die("Query Error: ".mysqli_error($handle));

		// Get the record from the SQL query
		$obj = $res->fetch_object();
		
		// Check the record exists
		if (!$obj){
			return null;
		}
		
		// Convert the record into a Test class
		$test = new Test($obj);
		if (!$test){
			return null;
		}
		
		return $test;
	}
	
	public function words(){
		if (!is_numeric($this->id)){
			throw("test id is not numeric");
			return 0;
		}
		return Word::_search("WHERE testID = ".$this->id);
	}
	
	public function users(){
		if (!is_numeric($this->id)){
			throw("test id is not numeric");
			return 0;
		}
		$ass = TestAssign::_search("WHERE testID = ".$this->id);
		if (!$ass)
			return null;
			
		$all_users = [];
		foreach ($ass as $a){
			$cond = "";
			
			if ($a->year != null){
				$cond = "year = ".$a->year;
			}
			if ($a->group != null){
				if ($cond!="")
					$cond .= " AND ";
		
				$cond .= "ugroup = '".$a->group."'";
			}
			$users = User::_search("WHERE ".$cond);
			if ($users){
				foreach ($users as $u){
					array_push($all_users,$u);
				}
			}
		}
		return $all_users;
	}
	
	// Function for searching and returning a list
	public static function _search($query){
		global $handle;

		$res = $handle->query("SELECT * FROM test ".$query);
		if (!$res){
			return;
		}

		$result = array();
		while($obj = $res->fetch_object()){ 
			array_push($result,new Test($obj));
		}
		return $result;
	}
	
	// Get all tests
	public static function all(){
		return Test::_search("");
	}
}