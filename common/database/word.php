<?php
class Word {
	public $id = null;
	public $testID = null;
	public $word = null;
	public $def = null;
	
	// Word Constructor
	// Reads the values from the database if provided,
	// or creates an empty new record
	public function Word($db) {
		if ($db){
			// Get values from database record
			$this->id = $db->wordID;
			$this->testID = $db->testID;
			$this->word = $db->word;
			$this->def = $db->definition;
		}else{
			// Create new record
			$this->id = -1;
		}
	}
	
	// Get class fields in string format for debuging
	public function __tostring(){
		$res = "wordID: '".$this->id."',<br>";
		$res .= "testID: '".$this->testID."',<br>";
		$res .= "word: '".$this->word."',<br>";
		$res .= "def: '".$this->def."',<br>";
		return "{$res}";
	}
	
	// Save the record
	public function save(){
		global $handle;
		if ($this->id == -1){
			// Create SQL query
			$h = $handle->prepare("INSERT INTO word(testID,word,definition) VALUES (?,?,?)")  or die("SQL Prepare: ".mysqli_error($handle));
			$h->bind_param('iss',$this->testID,$this->word,$this->def) or die("SQL Param: ".mysqli_error($handle));
			
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
					$res = "UPDATE word SET $col = $value WHERE wordID = $id";
				}else{
					// Update string field
					$res = "UPDATE word SET $col = '$value' WHERE wordID = $id";
				}

				// Run update query
				$handle->query($res) or die("<br><br>Query Error: ".mysqli_error($handle));
			}
			
			// Update each field using the function above
			update($this->id,"testID",$this->testID,true);
			update($this->id,"word",$this->word,false);
			update($this->id,"definition",$this->def,false);
		}
	}
	
	
	// Returns all the nearwords for this word
	public function nearwords(){
		if (!is_numeric($this->id)){
			throw("word id is not numeric");
			return 0;
		}
		return Nearword::_search("WHERE wordID = ".$this->id);
	}
	
	// Get a word by their wordID
	public static function get($id){
		global $handle;
		
		// Validate for type and presence
		if (!is_numeric($id)){
			return null;
		}
		
		// Execute SQL to find record
		$res = $handle->query("SELECT * FROM word WHERE wordID = $id") or die("Query Error: ".mysqli_error($handle));

		// Get the record from the SQL query
		$obj = $res->fetch_object();
		
		// Check the record exists
		if (!$obj){
			return null;
		}
		
		// Convert the record into a Test class
		$word = new Word($obj);
		if (!$word){
			return null;
		}
		
		return $word;
	}
	
	// Function for searching and returning a list
	public static function _search($query){
		global $handle;

		$res = $handle->query("SELECT * FROM word ".$query);
		if (!$res){
			return;
		}

		$result = array();
		while($obj = $res->fetch_object()){ 
			array_push($result,new Word($obj));
		}
		return $result;
	}
	
	// Get all tests
	public static function all(){
		return Test::_search("");
	}
}