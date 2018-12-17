<?php

class Blog {
    protected $db;
    protected $response;
    public function __construct($db){
        $this->db=$db;
        $this->response['result']="success";
    }
    public function get_all(){
        $all= $this->db->get_all();
        $this->response['data']=  json_encode($all);
        return json_encode($this->response);
    }
    public function get_one(){
        if(!empty($_GET["id"]) && is_numeric($_GET["id"])){
            $id=$_GET['id'];
            $one= $this->db->get_one($id);
            if(!empty($one)){
                $this->response['data']=  json_encode($one);
            }
            else{
                $this->response['result']="fail";
                $this->response['errors']="Record not found";
            }
        }
        else{
            $this->response['result']="fail";
            $this->response['errors']="Missing or Invalid Parameters";
        }
        return json_encode($this->response);
    }
    protected function authenticate(){
        if(!empty($_SERVER["PHP_AUTH_USER"]) && !empty($_SERVER["PHP_AUTH_PW"])){
            $auth_user=$_SERVER["PHP_AUTH_USER"];
            $auth_pass=  base64_decode($_SERVER["PHP_AUTH_PW"]);
            $auth_pass=  explode(":", $auth_pass);
            if($auth_user=="Basic" && count($auth_pass)>1){
                $username=$auth_pass[0];
                $password=$auth_pass[1];
                $user= $this->db->get_user($username);
                if(!empty($user)){
                    $user_password= base64_encode($user["username"].$user["token"].$user["username"]);
                    if($user_password==$password){
                        return TRUE;
                    }
                }
                else{
                    $this->response['result']="fail";
                    $this->response['errors'][]="Authentication failed";
                }
            }
            else{
                $this->response['result']="fail";
                $this->response['errors'][]="Authentication failed";
            }
            return FALSE;
        }
    }
}
