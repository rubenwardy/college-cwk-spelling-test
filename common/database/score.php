<?php
class Score {
	public $id = null;
	public $testID = null;
	public $userID = null;
	public $score = null;
	
	// Score Constructor
	// Reads the values from the database if provided,
	// or creates an empty new record
	public function Score($db) {
		if ($db){
			// Get values from database record
			$this->id = $db->scoreID;
			$this->testID = $db->testID;
			$this->userID = $db->userID;
			$this->score = $db->score;
		}else{
			// Create new record
			$this->id = -1;
		}
	}
	
	// Get class fields in string format for debuging
	public function __tostring(){
		$res = "scoreID: '".$this->id."',<br>";
		$res .= "testID: '".$this->testID."',<br>";
		$res .= "userID: '".$this->userID."',<br>";
		$res .= "score: '".$this->score."',<br>";
		return "{$res}";
	}
	
	public function wrongWords(){
		if (!is_numeric($this->id)){
			return null;
		}
		
		return WrongWord::_search("WHERE scoreID = {$this->id}");
	}
	
	// Save the record
	public function save(){
		global $handle;
		if ($this->id == -1){
			// Create SQL query
			$h = $handle->prepare("INSERT INTO score(testID,userID,score) VALUES (?,?,?)")  or die("SQL Prepare: ".mysqli_error($handle));
			$h->bind_param('iii',$this->testID,$this->userID,$this->score) or die("SQL Param: ".mysqli_error($handle));
			
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
					$res = "UPDATE score SET $col = $value WHERE scoreID = $id";
				}else{
					// Update string field
					$res = "UPDATE score SET $col = '$value' WHERE scoreID = $id";
				}

				// Run update query
				$handle->query($res) or die("<br><br>Query Error: ".mysqli_error($handle));
			}
			
			// Update each field using the function above
			update($this->id,"testID",$this->testID,true);
			update($this->id,"userID",$this->userID,true);
			update($this->id,"score",$this->score,true);
		}
	}
	
	// Get a score by their testID and userID
	public static function get($id){
		global $handle;
		
		// Validate for type and presence
		if (!is_numeric($id)){
			return null;
		}
		
		// Execute SQL to find record
		$res = $handle->query("SELECT * FROM score WHERE scoreID = $id") or die("Query Error: ".mysqli_error($handle));

		// Get the record from the SQL query
		$obj = $res->fetch_object();
		
		// Check the record exists
		if (!$obj){
			return null;
		}
		
		// Convert the record into a score class
		$test = new Score($obj);
		if (!$test){
			return null;
		}
		
		return $test;
	}
	
	// Function for searching and returning a list
	public static function _search($query){
		global $handle;

		$res = $handle->query("SELECT * FROM score ".$query);
		if (!$res){
			return;
		}

		$result = array();
		while($obj = $res->fetch_object()){ 
			array_push($result,new Score($obj));
		}
		return $result;
	}
	
	// Get all scores
	public static function all(){
		return Score::_search("");
	}
}