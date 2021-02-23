<?php

class LCityMarket{
    
    
    static function getTransList($idCity){
        return selectFromTable("market_transport.*, c1.name AS CityNameFrom, c2.name AS CityNameTo, c1.x AS xCoordFrom, c1.y AS yCoordFrom, c2.x AS xCoordTo, c2.y AS yCoordTo", 
                        "market_transport JOIN city AS c1 ON c1.id_city = market_transport.id_city_from JOIN city AS c2 ON c2.id_city = market_transport.id_city_to", 
                        "market_transport.id_city_from = :idcf OR market_transport.id_city_to = :idct",
                        ["idcf" => $idCity, "idct" => $idCity]
                        );
    }
    static function getTransBackList($idCity){
        return selectFromTable("market_transport_back.*, c1.name AS CityNameFrom, c2.name AS CityNameTo, c1.x AS xCoordFrom, c1.y AS yCoordFrom, c2.x AS xCoordTo, c2.y AS yCoordTo", 
                        "market_transport_back JOIN city AS c1 ON c1.id_city = market_transport_back.id_city_from JOIN city AS c2 ON c2.id_city = market_transport_back.id_city_to", 
                        "market_transport_back.id_city_to = :idc",
                        ["idc" => $idCity]
                        );
    }
    
}
