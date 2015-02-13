<?php

namespace Core;
use PDO;

class Crud{

	public function __construct(){
		try{
			//Db connect
			$this->oConnect = new PDO(
					'mysql:host='.HOST.';dbname='.DB, USER, PASS,
					array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES utf8')
			);
			$this->oConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(\PDOException $e){
			echo $e->getMessage();
		}

		$this->class = strtolower(get_class($this));
		$this->xxx_id = $this->class.'_id';		

	}

	public function getModel($model) {
		require_once MODELS.ucfirst($model).'.php';
		return new $model();
	}

	public function getCurrent($id = 0){

		$query = $this->oConnect->prepare("
			SELECT  *
			FROM    " . $this->class . "
			WHERE   " . $this->xxx_id . " = " . intval($id)
		);

		$fetch = $query->execute();
		$result = $query->fetch(PDO::FETCH_OBJ);

		//set object identifier
		$result->objectKey = $this->class.'/'.$result->{$this->xxx_id};

		return $result;
	}

    public function select($params = array()){

        $columns = (isset($params['columns']))?$params['columns']:'*';
        $table   = (isset($params['table']))?$params['table']:strtolower(get_class($this));
        $orderBy = (isset($params['orderBy']))?"ORDER BY " . $params['orderBy']:'';

        //set fetch mode
        $fetchMode = (isset($params['fetchMode'])) ? $params['fetchMode'] : PDO::FETCH_OBJ ;

        $where = "";

        // prepare query fields requested
        if (is_array($columns)){
            $columns = implode(', ', $columns);
        }

        // prepare where clause
        if (isset($params['where'])){
        	if (sizeof($params['where']) > 1) {
        		$where = "WHERE 1 = 1 ";
	         	foreach ($params['where'] as $col => $val){
		           $where .= "AND $col = $val";
		        }
	        }else{
	        	$where = "WHERE " . key($params['where']) ." = '". current($params['where']) . "'";
	        }
        }

        // prepare request
        $query = $this->oConnect->prepare("SELECT {$columns} FROM {$table} {$where} {$orderBy}");

        if (isset($params['where'])){
	        foreach ($params['where'] as $col => &$val){
	            $query->bindParam(":{$col}", $val);
	        }
        }

        $result = $query->execute();

       	if ($query->rowcount() > 1) {
       		$results = array();
			while($fetch = $query->fetch($fetchMode)){
				$results[] = $fetch;
			};

        	return $results;
       	}else{
			$fetch = $query->fetch($fetchMode);
			$result = array($fetch);

        	return $result;
       	}
    }

	public function create(){

		//get POST fields and POST values
		foreach ($_POST as $sFieldName => $sFieldValue) {
			$aFields[] = $sFieldName;
			$aValues[] = '"'.filter_var($sFieldValue, FILTER_SANITIZE_MAGIC_QUOTES).'"';
		}

		//format values for insert query
		$sFields = implode(', ', $aFields);
		$sValues = implode(', ', $aValues);

		//contatenation
		$sFields .= ", created_at";
		$sValues .= ", NOW()";


		$sql = 'INSERT INTO '.$this->class.' ('.$sFields.')
				VALUE ('.$sValues.')';

		$query = $this->oConnect->exec($sql);
		$this->lastInsert = $this->oConnect->lastInsertId();

		return $this;

	}

	public function update(){

		$oid = $_POST[$this->xxx_id];

		//format values for update query
		$update_values="";
		$where="";
		foreach ($_POST as $sFieldName => $sFieldValue) {

			if($sFieldName !== $this->xxx_id){
				$update_values .= $sFieldName.' = "'.filter_var($sFieldValue, FILTER_SANITIZE_MAGIC_QUOTES).'", ';
			}else{
				$where = 'WHERE '.$this->xxx_id.' = :id';
			}

			//save POST values in class attributes
			$this->$sFieldName = $sFieldValue;
		}

		$sql = 'UPDATE '.$this->class.' SET '.substr($update_values, 0, -2).' '.$where;
		$query = $this->oConnect->prepare($sql);

		$query->execute(array(':id' => $_POST[$this->xxx_id]));

		return $this;
	}

	public function delete($params = array()){

		$table = (isset($params['table']))?$params['table']:strtolower(get_class($this)).'s';

		 //prepare query where requested
        if (isset($params['where'])){
        	if (sizeof($params['where']) > 1) {
        		$where = "WHERE 1 = 1 ";
	         	foreach ($params['where'] as $col => $val){
		           $where .= "AND $col = $val ";
		        }
	        }else{
	        	$where = "WHERE " . key($params['where']) ." = '". current($params['where']) . "'";
	        }
        }

        //set fetch mode
        //default FETCH_OBJ
        $fetchMode = (isset($params['fetchMode'])) ? $params['fetchMode'] : PDO::FETCH_OBJ ;

        //prepare request
        $query = $this->oConnect->prepare("DELETE FROM {$table} {$where}");

        if (isset($params['where'])){
	        foreach ($params['where'] as $col => &$val){
	            $query->bindParam(":{$col}", $val);
	        }
        }

        $result = $query->execute();

		return 'deleted';
	}
}
