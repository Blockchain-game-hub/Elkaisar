<?php

class ACity {

    public function getAllCities() {
        $idPlayer = validateID($_GET["idPlayer"]);
       
        return json_encode(
            selectFromTable(
                    "*",
                    "city",
                    "id_player = :idp LIMIT " . PLAYER_LIMIT_CITY_COUNT,
                    ["idp" => $idPlayer])
            
                ,
                JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT
        );
    }

}
