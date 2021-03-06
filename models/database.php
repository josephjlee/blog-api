<?php
require_once 'config/db_config.php';
require_once 'helpers/log_helper.php';
/**
 * Database class has all the basic database queries and connection
 */
class Database{
    protected $db_config;
    protected $db;
    protected $table;
    public function __construct($db_config,$table) {
        $this->db_config=$db_config;
        $this->connect();
        $this->table=$table;
    }
    public function get_table(){
        return $this->table;
    }
    public function set_table($table){
        $this->table=$table;
    }
    public function connect(){
        $this->db = null;
        try{
            $this->db = new PDO("mysql:host=" . $this->db_config['host'] . ";dbname=" . $this->db_config['db_name'], $this->db_config['username'], $this->db_config['password']);
        }catch(PDOException $exception){
            $response['result']="fail";
            $response['errors']= "Database connection error: " . $exception->getMessage();
            echo json_encode($response);
            die;
        }
    }
    public function insert($data){
        $keys=[];
        $values=[];
        //create custom columns and values strings for dynamic insertion
        foreach ($data as $key=>$value){
            $keys[]=$key;
            $values[]="'".$value."'";
        }
        $keys=  implode(",", $keys);
        $values=  implode(",", $values);
        $query = "INSERT INTO ".$this->table." (".$keys.") VALUES (".$values.") ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $this->db->lastInsertId();
    }
    public function update($id,$data){
        $set="";
        //create custom set string for dynamic update
        foreach ($data as $key=>$value){
            $set.=$key."='".$value."',";
        }
        $set= substr($set, 0,-1);
        $query = "UPDATE ".$this->table." SET ".$set." WHERE id=".$id." ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
    }
    public function get_one($id){
        $query = "SELECT * FROM ". $this->table." WHERE id=".$id." and deleted!=1 ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result=$stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    public function get_all(){
        $query = "SELECT * FROM ". $this->table." WHERE deleted!=1 ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get_all_hard(){
        $query = "SELECT * FROM ". $this->table." ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function soft_delete($id){
        $query = "UPDATE ".$this->table." SET deleted=1 WHERE id=".$id." ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
    }
    
}