<?php

class database{

	public $host = DB_HOST;
	public $user = DB_USER;
	public $pass = DB_PASS;
	public $db_name = DB_NAME;

	public $conn;

	public function __construct(){
		$this->init();
	}

	private function init(){

		try{
			$this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->user, $this->pass);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		}catch(PDOException $e){
		    die($e->getMessage());
		}

		//create table
		$sql = "CREATE TABLE IF NOT EXISTS `statistics` ( 
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`title` varchar(300) NOT NULL,
	    	`artist` varchar(100) NOT NULL,
		    `play` int(11) unsigned NOT NULL,
		    `time` int(11) unsigned NOT NULL,
		    `like` int(11) unsigned NOT NULL,
		    `download` int(11) NOT NULL,
		    `date` date NOT NULL,
		    `user_ip` varchar(50) DEFAULT NULL,
		    `media_id` int(11) unsigned NOT NULL,
		    PRIMARY KEY (`id`)
		) ENGINE=InnoDB;";

		try{
			$this->conn->query($sql);
        }catch(PDOException $e) {
            die($e->getMessage());
        }

	}

	public function prepare($query){

		$result = $this->conn->prepare($query);	
		return $result;

	}

	public function select($query){

		$result = $this->conn->query($query);	

		if($result->rowCount() > 0){
			return $result;
		}else{
			return false;
		}
		
	}

	public function lastInsertId(){

        return $this->conn->lastInsertId();

    }


    public function beginTransaction(){

    	$this->conn->beginTransaction();
		
	}

	public function commit(){

    	$this->conn->commit();
		
	}

	public function rollBack(){

    	$this->conn->rollBack();
		
	}



}

