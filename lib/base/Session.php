<?php

class Session
{
    
    public function __construct(){
        // Set handler to overide SESSION  
        session_set_save_handler(  
        array($this, "_open"),  
        array($this, "_close"),  
        array($this, "_read"),  
        array($this, "_write"),  
        array($this, "_destroy"),  
        array($this, "_gc")  
        );

        // Start the session  
        session_start();  
    }  
    
    public function _open(){  
        return TRUE; 
    } 
    
    public function _read($id){  
         
        $raw = selectFromTable("val", "_session", "id = :id", ["id"=>$id]);
        if(count($raw) > 0){
            return $raw[0]["val"];
        }
        
        return "";
    } 
    public function _close(){  
         
        return true;  
         
    }  
    public function _write($id, $val){  
    // Create time stamp  
        $access = time();

        $ret = queryExe(
                "REPLACE INTO _session VALUES (:id, :access, :val)",
                ["id"=>$id, "access"=>$access, "val"=>$val]);
          
        return $ret["suc"];
    }  
    public function _destroy($id){  
        

        $ret = queryExe(
                "DELETE FROM _session WHERE id = :id",
                ["id"=>$id]);
          
        return $ret["suc"];
        
    }  
    
    public function _gc(){  
       
       $ret = queryExe(
                "DELETE FROM q_session WHERE access < :old",
                ["old"=> time() - 30*24*60*60]);
        return $ret["suc"];
        
    }  
}
