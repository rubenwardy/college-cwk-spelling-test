<?php
class TestAssign {
	public $id = null;
	public $testID = null;
	public $year = null;
	public $group = null;
	
	// TestAssign Constructor
	// Reads the values from the database if provided,
	// or creates an empty new record
	public function TestAssign($db) {
		if ($db){
			// Get values from database record
			$this->id = $db->assignID;
			$this->testID = $db->testID;
			$this->year = $db->year;
			$this->group = $db->ugroup;
		}else{
			// Create new record
			$this->id = -1;
		}
	}
	
	// Get class fields in string format for debuging
	public function __tostring(){
		$res = "assignID: '".$this->id."',<br>";
		$res .= "testID: '".$this->testID."',<br>";
		$res .= "year: '".$this->year."',<br>";
		$res .= "group: '".$this->group."',<br>";
		return "{$res}";
	}
	
	// Save the record
	public function save(){
		global $handle;
		if ($this->id == -1){
			// Create SQL query
			$h = $handle->prepare("INSERT INTO testassign(testID,year,ugroup) VALUES (?,?,?)")  or die("SQL Prepare: ".mysqli_error($handle));
			$h->bind_param('iis',$this->testID,$this->year,$this->group) or die("SQL Param: ".mysqli_error($handle));
			
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
					$res = "UPDATE testassign SET $col = $value WHERE assignID = $id";
				}else{
					// Update string field
					$res = "UPDATE testassign SET $col = '$value' WHERE assignID = $id";
				}

				// Run update query
				$handle->query($res) or die("<br><br>Query Error: ".mysqli_error($handle));
			}
			
			// Update each field using the function above
			update($this->id,"testID",$this->testID,true);
			update($this->id,"userID",$this->year,true);
			update($this->id,"ugroup",$this->group,false);
		}
	}
	
	// Get a user by their assignID
	public static function get($id){
		global $handle;
		
		// Validate for type and presence
		if (!is_numeric($id)){
			return null;
		}
		
		// Execute SQL to find record
		$res = $handle->query("SELECT * FROM testassign WHERE assignID = $id") or die("Query Error: ".mysqli_error($handle));

		// Get the record from the SQL query
		$obj = $res->fetch_object();
		
		// Check the record exists
		if (!$obj){
			return null;
		}
		
		// Convert the record into a Test class
		$test = new TestAssign($obj);
		if (!$test){
			return null;
		}
		
		return $test;
	}
	
	public function test(){
		return Test::get($this->testID);
	}
	
	// Function for searching and returning a list
	public static function _search($query){
		global $handle;
		
		$res = $handle->query("SELECT * FROM testassign ".$query);
		if (!$res){
			return;
		}

		$result = array();
		while($obj = $res->fetch_object()){ 
			array_push($result,new TestAssign($obj));
		}
		return $result;
	}
	
	// Get all users
	public static function all(){
		return TestAssign::_search("");
	}
}