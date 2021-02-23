<?php

class ACityMarket {

    function getOffersList() {
        $resource = validateGameNames($_GET["offerFor"]);
        return [
            "sellOffers" => selectFromTable("*", "market_deal", " deal = 'sell' AND resource = :res ORDER BY unit_price ASC  LIMIT 5", ["res" => $resource]),
            "buyOffers"  => selectFromTable("*", "market_deal", " deal = 'buy' AND  resource = :res ORDER BY unit_price DESC  LIMIT 5", ["res" => $resource])
        ];
    }
    
    function getCityOffers()
    {
        global $idPlayer;
        $idCity = validateID($_GET["idCity"]);
        return selectFromTable("*", "market_deal", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]);
    }
    
    function getCityOffersTrans()
    {
        global $idPlayer;
        $idCity = validateID($_GET["idCity"]);
        return selectFromTable("*", "market_buy_transmit", "id_city_to = :idc", ["idc" => $idCity]);
    }
    
    function proposeSellOffer()
    {
        global $idPlayer;
        $idCity    = validateID($_POST["idCity"]);
        $ResType   = validateGameNames($_POST["ResType"]);
        $unitPrice = validateNumber($_POST["unitPrice"]);
        $amount    = validateID($_POST["amount"]);
        $fees      = $unitPrice*$amount*0.75/100;
        
        if($amount <= 0)
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($unitPrice <= 0 )
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if(!in_array($ResType, ["food", "wood", "stone", "metal"]))
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        if(!LCity::isResourceTaken(["$ResType" => $amount, "coin" => $fees], $idCity))
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        
        insertIntoTable(
                "deal = 'sell', unit_price = :up, amount = :a, done = 0, resource = :res, id_player = :idp, id_city = :idc",
                "market_deal",
                [
                    "up" => $unitPrice, "a" => $amount, "res" => $ResType,
                    "idp" => $idPlayer, "idc" => $idCity
                ]);
        $this->buyAuctionOffer($ResType);
        
        return [
            "state"      =>"ok",
            "cityRes"    => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0],
            "cityOffers" => selectFromTable("*", "market_deal", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])
        ];
    }
    
    
    function proposeBuyOffer()
    {
        global $idPlayer;
        $idCity    = validateID($_POST["idCity"]);
        $ResType   = validateGameNames($_POST["ResType"]);
        $unitPrice = validateNumber($_POST["unitPrice"]);
        $amount    = validateID($_POST["amount"]);
        $fees      = $unitPrice*$amount*0.75/100;
        
        if($amount <= 0)
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($unitPrice <= 0 )
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if(!in_array($ResType, ["food", "wood", "stone", "metal"]))
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        if(!LCity::isResourceTaken(["coin" => $fees], $idCity))
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        
        insertIntoTable(
                "deal = 'buy', unit_price = :up, amount = :a, done = 0, resource = :res, id_player = :idp, id_city = :idc",
                "market_deal",
                [
                    "up" => $unitPrice, "a" => $amount, "res" => $ResType,
                    "idp" => $idPlayer, "idc" => $idCity
                ]);
        $this->buyAuctionOffer($ResType);
        
        return [
            "state"      =>"ok",
            "cityRes"    => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0],
            "cityOffers" => selectFromTable("*", "market_deal", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])
        ];
    }

    private function buyAuctionOffer($ResType)
    {
        
        $sellOffers = selectFromTable("*", "market_deal", "resource = :res AND deal = 'sell' ORDER BY unit_price ASC LIMIT 10", ["res" => $ResType]);
        $timeArrive = time() + 30 *60;
        $now = time();
        foreach ($sellOffers as $oneSellOffer){
            
            $buyOffers = selectFromTable("*", "market_deal", "resource = :res AND deal = 'buy' AND unit_price >= :up ORDER BY unit_price DESC LIMIT 10", ["res" => $ResType, "up" => $oneSellOffer["unit_price"]]);
            if(!count($buyOffers))
                break;
            
            foreach ($buyOffers as $oneBuyOffer){
                
                $idCityBuyer  = $oneBuyOffer["id_city"];
                $idBuyer      = $oneBuyOffer["id_player"];
                $amountNeeded = $oneBuyOffer["amount"] - $oneBuyOffer["done"];
                $amountSellerHas = $oneSellOffer["amount"] - $oneSellOffer["done"];
                $amountToAdd     = 0;
                
                
                if($amountSellerHas < $amountNeeded)
                    $amountToAdd = $amountSellerHas;
                else
                    $amountToAdd = $amountNeeded;
                
                $oneBuyOffer["done"] += $amountToAdd;
                $oneSellOffer["done"] += $amountToAdd;
                
                if($oneBuyOffer["done"] >= $oneBuyOffer["amount"])
                    deleteTable ("market_deal", "id_deal = :idd", ["idd" => $oneBuyOffer["id_deal"]]);
                else
                    updateTable("done = :d", "market_deal", "id_deal = :idd", ["idd" => $oneBuyOffer["id_deal"], "d" => $oneBuyOffer["done"]]);
                
                insertIntoTable("id_to = $idBuyer ,"
                        . " head = 'تقرير شراء الموارد'  ,"
                        . " body = 'تمت عملية شراء {$amountToAdd} وحدة من {Resource} بسعر {$oneSellOffer["unit_price"]}  '"
                        . " , time_stamp = $now ", "msg_diff");
                insertIntoTable("id_to = {$oneSellOffer["id_player"]} ,"
                        . " head = 'تقرير بيع الموارد'  ,"
                        . " body = 'تمت عملية بيع {$amountToAdd} وحدة من {Resource} بسعر {$oneSellOffer["unit_price"]}  '"
                        . " , time_stamp = $now ", "msg_diff");
                        
                insertIntoTable("id_city_to = $idCityBuyer , "
                        . " amount = $amountToAdd , id_player_to = $idBuyer ,resource = :res , time_arrive = $timeArrive , unit_price = {$oneSellOffer["unit_price"]} ", "market_buy_transmit", ["res" => $ResType]);


                updateTable("coin = coin + :tp", "city", "id_city = :idc AND id_player = :idp", ["tp" => $oneSellOffer["unit_price"]*$amountToAdd, "idc" => $oneSellOffer["id_city"], "idp" => $oneSellOffer["id_player"]]);
                
                if($oneSellOffer["done"] >= $oneSellOffer["amount"])
                    break;
                
            }
            
            
            if($oneSellOffer["done"] >= $oneSellOffer["amount"])
                deleteTable ("market_deal", "id_deal = :idd", ["idd" => $oneSellOffer["id_deal"]]);
            else 
                updateTable("done = :d", "market_deal", "id_deal = :idd", ["idd" => $oneSellOffer["id_deal"], "d" => $oneSellOffer["done"]]);
            
        }
    }
    
    function cancelMyOffer()
    {
        global $idPlayer;
        $idOffer = validateID($_POST["idOffer"]);
        $Offer = selectFromTable("*", "market_deal", "id_deal = :idd AND id_player = :idp", ["idd" => $idOffer, "idp" => $idPlayer]);
        
        if(!count($Offer))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        deleteTable("market_deal", "id_deal = :idd AND id_player = :idp", ["idd" => $idOffer, "idp" => $idPlayer]);
        
        $amount = $Offer[0]["amount"] - $Offer[0]["amount"];
        $GainRes = [];
        
        if($Offer[0]["deal"] == "sell")
            $GainRes = [$Offer[0]["resource"] => $amount];
        else if($Offer[0]["deal"] == "buy")
            $GainRes = ["coin" => $amount*$Offer[0]["unit_price"]];
        
        LSaveState::saveCityState($Offer[0]["id_city"]);
        LCity::addResource($GainRes, $Offer[0]["id_city"]);
        
        return [
            "state"      =>"ok",
            "cityRes"    => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0],
            "cityOffers" => selectFromTable("*", "market_deal", "id_city = :idc AND id_player = :idp", ["idc" => $Offer[0]["id_city"], "idp" => $idPlayer])
        ];
    
    }
    
    
    function speedUpDealTrans()
    {
        global $idPlayer;
        $idTrans = validateID($_POST["idOffer"]);
        $Trans   = selectFromTable("*", "market_buy_transmit", "id_deal = :id AND id_player_to = :idp", ["id" => $idTrans, "idp" => $idPlayer]);
        
        if(!count($Trans))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!LItem::useItem("shopping_car"))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        
        LSaveState::saveCityState($Trans[0]["id_city_to"]);
        deleteTable("market_buy_transmit", "id_deal = :id AND id_player_to = :idp", ["id" => $idTrans, "idp" => $idPlayer]);
        LCity::addResource([$Trans[0]["resource"] => $Trans[0]["amount"]], $Trans[0]["id_city_to"]);
        
        return [
            "state" => "ok",
            "cityRes" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0],
            "cityBuyTransMits" => selectFromTable("*", "market_buy_transmit", "id_city_to = :idc", ["idc" => $Trans[0]["id_city_to"]])
        ];
    }
  
    
}
