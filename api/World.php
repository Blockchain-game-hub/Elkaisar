<?php

class World
{
    
   function get()
   {
    
       return json_encode(
               ["test"=> (file_get_contents(BASE_BATH."js/json/FIXED_WORLD_UNITS.json"))]
               );       
   }
   
   
    
}




