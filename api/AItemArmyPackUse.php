<?php

class AItemArmyPack
{
    
    function useArmyPackMini(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        $army_a = rand(200, 300)*$amount;
        $army_d = rand(100, 200)*$amount;
        $spies = rand(30, 50)*$amount;
        updateTable("army_a = army_a + :aa ,  army_d = army_d + :ad  , spies = spies + :as ", "city", "id_city = :idc  AND id_player = :idp", ["aa"=>$army_a, "ad"=>$army_d, "as"=>$spies, "idc"=>$idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    
    function useArmyPackMedium(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        $army_b = rand(30, 50)*$amount;
        $army_c = rand(10, 20)*$amount;
        $army_d = rand(200, 300)*$amount;
        $army_e = rand(100, 200)*$amount;
        updateTable("army_b = army_b + :ab, army_c = army_c + :ac, army_d = army_d + :ad, army_e = army_e + :ae",
                    "city", "id_city = :idc  AND   id_player = :idp",
                    ["ab" => $army_b, "ac"=> $army_c, "ad" => $army_d, "ae"=> $army_e, "idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    
    function useArmyPackLarge(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        $army_b = rand(100, 200)*$amount;
        $army_e = rand(200, 300)*$amount;
        $army_c = rand(30, 50)*$amount;
        $army_f = rand(10, 20)*$amount;
        updateTable("army_b = army_b + :ab ,  army_e = army_e + :ae  , army_c = army_c + :ac, army_f = army_f + :af", "city", "id_city = :idc  AND id_player = :idp",
                    ["ab"=>$army_b, "ae"=>$army_e, "ac"=> $army_e, "ac"=>$army_c, "af" => $army_f, "idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    
    function useArmyPackA100(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        updateTable("army_a = army_a + :a ", "city", "id_city = :idc  AND id_player = :idp",
                    ["a"=> 100*$amount,"idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    function useArmyPackB100(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        updateTable("army_b = army_b + :a ", "city", "id_city = :idc  AND id_player = :idp",
                    ["a"=> 100*$amount,"idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    function useArmyPackC100(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        updateTable("army_c = army_c + :a ", "city", "id_city = :idc  AND id_player = :idp",
                    ["a"=> 100*$amount,"idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    function useArmyPackD100(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        updateTable("army_d = army_d + :a ", "city", "id_city = :idc  AND id_player = :idp",
                    ["a"=> 100*$amount,"idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    function useArmyPackE100(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        updateTable("army_e = army_e + :a ", "city", "id_city = :idc  AND id_player = :idp",
                    ["a"=> 100*$amount,"idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    function useArmyPackF100(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        updateTable("army_f = army_f + :a ", "city", "id_city = :idc  AND id_player = :idp",
                    ["a"=> 100*$amount,"idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    function useArmyPackA1000(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        updateTable("army_a = army_a + :a ", "city", "id_city = :idc  AND id_player = :idp",
                    ["a"=> 1000*$amount,"idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    function useArmyPackB1000(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        updateTable("army_b = army_b + :a ", "city", "id_city = :idc  AND id_player = :idp",
                    ["a"=> 1000*$amount,"idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    function useArmyPackC1000(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        updateTable("army_c = army_c + :a ", "city", "id_city = :idc  AND id_player = :idp",
                    ["a"=> 1000*$amount,"idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    function useArmyPackD1000(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        updateTable("army_d = army_d + :a ", "city", "id_city = :idc  AND id_player = :idp",
                    ["a"=> 1000*$amount,"idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    function useArmyPackE1000(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        updateTable("army_e = army_e + :a ", "city", "id_city = :idc  AND id_player = :idp",
                    ["a"=> 1000*$amount,"idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    function useArmyPackF1000(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        updateTable("army_f = army_f + :a ", "city", "id_city = :idc  AND id_player = :idp",
                    ["a"=> 1000*$amount,"idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    
}
