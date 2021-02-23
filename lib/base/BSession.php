<?php

class BSession implements SessionHandlerInterface
{
    
    public function __construct(){
        // Set handler to overide SESSION  
        session_set_save_handler(  
        array($this, "open"),  
        array($this, "close"),  
        array($this, "read"),  
        array($this, "write"),  
        array($this, "destroy"),  
        array($this, "gc")  
        );
        register_shutdown_function('session_write_close');

        // Start the session  
        session_start();  
    }  
    
    public function open($savePath,  $id){  
        
        $raw = selectFromTableIndex("val", "_session", "id = :id", ["id"=>$id]);
        if(count($raw))
           return TRUE; 
        
        return true;
    } 
    
    public function read($id){  
         
        $raw = selectFromTableIndex("val", "_session", "id = :id", ["id"=>$id]);
        if(count($raw) > 0){
            return $raw[0]["val"];
        }
        
        return "";
    } 
    public function close(){  
         
        return true;  
         
    }  
    public function write($id, $val){  
    // Create time stamp  
        $access = time();

        queryExeIndex(
                "REPLACE INTO _session VALUES (:id, :access, :val)",
                ["id"=>$id, "access"=>$access, "val"=>$val]);
          
        return true;
    }  
    public function destroy($id){  
        

        $ret = queryExeIndex(
                "DELETE FROM _session WHERE id = :id",
                ["id"=>$id]);
          
        return true;
        
    }  
    
    public function gc($MaxLife){  
       
       $ret = queryExeIndex(
                "DELETE FROM _session WHERE access < :old",
                ["old"=> time() - 30*24*60*60]);
        return true;
        
    }  
    public function __destruct()
    {
        $this->close();
    }
}
