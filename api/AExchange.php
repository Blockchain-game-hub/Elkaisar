<?php

class AExchange
{
    
    function getExchangeItem()
    {
        
        return json_encode(
                selectFromTable("*", "exchange", "1"),
                JSON_NUMERIC_CHECK
                );
        
    }
    
}



//echo json_encode(selectFromTable("*", "exchange", "1"), JSON_PRETTY_PRINT |  JSON_NUMERIC_CHECK  | JSON_UNESCAPED_SLASHES);