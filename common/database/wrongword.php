<?php
class WrongWord {
	public $id = null;
	public $scoreID = null;
	public $wordID = null;
	public $word = null;
	
	// WrongWord Constructor
	// Reads the values from the database if provided,
	// or creates an empty new record
	public function WrongWord($db) {
		if ($db){
			// Get values from database record
			$this->id = $db->wrongID;
			$this->scoreID = $db->scoreID;
			$this->wordID = $db->wordID;
			$this->word = $db->word;
		}else{
			// Create new record
			$this->id = -1;
		}
	}
	
	// Get class fields in string format for debuging
	public function __tostring(){
		$res = "wrongID: '".$this->id."',<br>";
		$res .= "scoreID: '".$this->scoreID."',<br>";
		$res .= "wordID: '".$this->wordID."',<br>";
		$res .= "word: '".$this->word."'";
		return "{$res}";
	}
	
	// Save the record
	public function save(){
		global $handle;
		if ($this->id == -1){
			// Create SQL query
			$h = $handle->prepare("INSERT INTO wrongword(scoreID,wordID,word) VALUES (?,?,?)")  or die("SQL Prepare: ".mysqli_error($handle));
			$h->bind_param('iis',$this->scoreID,$this->wordID,$this->word) or die("SQL Param: ".mysqli_error($handle));
			
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
					$res = "UPDATE wrongword SET $col = $value WHERE wrongID = $id";
				}else{
					// Update string field
					$res = "UPDATE wrongword SET $col = '$value' WHERE wrongID = $id";
				}

				// Run update query
				$handle->query($res) or die("<br><br>Query Error: ".mysqli_error($handle));
			}
			
			// Update each field using the function above
			update($this->id,"scoreID",$this->scoreID,true);
			update($this->id,"wordID",$this->wordID,true);
			update($this->id,"word",$this->word,false);
		}
	}
	
	// Get a test by their wrongID
	public static function get($id){
		global $handle;
		
		// Validate for type and presence
		if (!is_numeric($id)){
			return null;
		}
		
		// Execute SQL to find record
		$res = $handle->query("SELECT * FROM wrongword WHERE wordID = $id") or die("Query Error: ".mysqli_error($handle));

		// Get the record from the SQL query
		$obj = $res->fetch_object();
		
		// Check the record exists
		if (!$obj){
			return null;
		}
		
		// Convert the record into a Test class
		$word = new WrongWord($obj);
		if (!$word){
			return null;
		}
		
		return $word;
	}
	
	// Function for searching and returning a list
	public static function _search($query){
		global $handle;

		$res = $handle->query("SELECT * FROM wrongword ".$query);
		if (!$res){
			return;
		}

		$result = array();
		while($obj = $res->fetch_object()){ 
			array_push($result,new WrongWord($obj));
		}
		return $result;
	}
	
	// Get all tests
	public static function all(){
		return WrongWord::_search("");
	}
}