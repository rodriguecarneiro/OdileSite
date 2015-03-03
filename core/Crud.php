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

		$this->class = strtolower(str_replace('Models\\', '', get_class($this)));

		//foreign key
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
			WHERE   id = " . intval($id)
		);

		$fetch = $query->execute();
		$result = $query->fetch(PDO::FETCH_OBJ);

		//set object identifier
		$result->objectKey = $this->class.'/'.$result->id;

		return $result;
	}

    public function select($params = array()){

        $columns = (isset($params['columns']))?$params['columns']:'*';
        $table   = (isset($params['table']))?$params['table']:$this->class;
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
        $stmt = $this->oConnect->prepare("SELECT {$columns} FROM {$table} {$where} {$orderBy}");

        if (isset($params['where'])){
	        foreach ($params['where'] as $col => &$val){
				$stmt->bindParam(":{$col}", $val);
	        }
        }


        $result = $stmt->execute();

       	if ($stmt->rowcount() == 1) {

			$fetch = $stmt->fetch($fetchMode);
			$result = [$fetch];

			return $result;

		}elseif($stmt->rowcount() > 1){

			$results = [];
			while($fetch = $stmt->fetch($fetchMode)){
				$results[] = $fetch;
			};
			return $results;

       	}else{

			return $stmt->fetch($fetchMode);
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

	public function update(array $data = []){

		$oid = !empty($data) ? $data['id'] : $_POST['id'];

		//format values for update query
		$update_values = "";

		//custom fields
		if(!empty($data)){

			foreach ($data as $fieldName => $fieldValue) {
				if ($fieldName !== 'id') {
					$update_values .= $fieldName . ' = "' . filter_var($fieldValue, FILTER_SANITIZE_MAGIC_QUOTES) . '", ';
				}
				//save POST values in class attributes
				$this->$fieldName = $fieldValue;
			}

		}else{

			foreach ($_POST as $fieldName => $fieldValue) {
				if($fieldName !== 'id'){
					//fields from form
					$update_values .= $fieldName . ' = "' . filter_var($fieldValue, FILTER_SANITIZE_MAGIC_QUOTES) . '", ';

				}
				//save POST values in class attributes
				$this->$fieldName = $fieldValue;
			}
		}

		$where = 'WHERE id = :id';

		$sql = 'UPDATE '.$this->class.' SET '.substr($update_values, 0, -2).' '.$where;

		$query = $this->oConnect->prepare($sql);
		$query->execute([':id' => $oid]);

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
