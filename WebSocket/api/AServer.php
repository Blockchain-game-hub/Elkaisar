<?php

class AServer
{
    
    function getServerData()
    {
        
        return selectFromTable("*", "server_data", "1")[0];
        
    }
    
    function serverJustOppend()
    {
        updateTable("`online` = 0", "player", "1");
        
    }
    
   
}
