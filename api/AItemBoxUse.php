<?php

class AItemBoxUse
{
    
    function useGiftBox()
    {
        $amount = validateID($_POST["amount"]);
        $Item   = validateGameNames($_POST["Item"]);
        
        if(!LItem::useItem("gift_box", $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        $lux_1  = 0; $lux_2  = 0; $lux_3  = 0;
        for($ii = 0; $ii < $amount; $ii++):
            $lux_1 += rand(0, 4) ; $lux_2 += rand(0, 4) ; $lux_3 += rand(0, 4) ;
        endfor;
        
        if($lux_1 > 0)
            LItem::addItem ("luxury_1", $lux_1);
        if($lux_2 > 0)
            LItem::addItem ("luxury_2", $lux_2);
        if($lux_3 > 0)
            LItem::addItem ("luxury_3", $lux_3);
        return [
            "state" => "ok",
            "Item" =>[
                ["Item" => "luxury_1", "amount" => $lux_1],
                ["Item" => "luxury_2", "amount" => $lux_2],
                ["Item" => "luxury_3", "amount" => $lux_3]
            ]
        ];
    }
    
    function useWoodBox()
    {
        $amount = validateID($_POST["amount"]);
        $Item   = validateGameNames($_POST["Item"]);
        if(!LItem::useItem("wood_box", $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        $lux_1  = 0; $lux_2  = 0; $lux_3  = 0;
        for($ii = 0; $ii < $amount; $ii++):
            $lux_1 += rand(0, 4) ; $lux_2 += rand(0, 4) ; $lux_3 += rand(0, 4) ;
        endfor;
        
        if($lux_1 > 0)
            LItem::addItem ("luxury_4", $lux_1);
        if($lux_2 > 0)
            LItem::addItem ("luxury_5", $lux_2);
        if($lux_3 > 0)
            LItem::addItem ("luxury_6", $lux_3);
        return [
            "state" => "ok",
            "Item" =>[
                ["Item" => "luxury_4", "amount" => $lux_1],
                ["Item" => "luxury_5", "amount" => $lux_2],
                ["Item" => "luxury_6", "amount" => $lux_3]
            ]
        ];
        
    }
    
    function useGoldenBox()
    {
        $amount = validateID($_POST["amount"]);
        $Item   = validateGameNames($_POST["Item"]);
        if(!LItem::useItem("golden_box", $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        $lux_1  = 0; $lux_2  = 0; $lux_3  = 0;
        for($ii = 0; $ii < $amount; $ii++):
            $lux_1 += rand(0, 4) ; $lux_2 += rand(0, 4) ; $lux_3 += rand(0, 4) ;
        endfor;
        
        if($lux_1 > 0)
            LItem::addItem ("luxury_7", $lux_1);
        if($lux_2 > 0)
            LItem::addItem ("luxury_8", $lux_2);
        if($lux_3 > 0)
            LItem::addItem ("luxury_9", $lux_3);
        return [
            "state" => "ok",
            "Item" =>[
                ["Item" => "luxury_7", "amount" => $lux_1],
                ["Item" => "luxury_8", "amount" => $lux_2],
                ["Item" => "luxury_9", "amount" => $lux_3]
            ]
        ];
        
    }
    
    function useBeginnerPack(){
        $amount = validateID($_POST["amount"]);
        $Item   = validateGameNames($_POST["Item"]);
        $Items = [];
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        if($Item == "beginner_back_1")
            $Items = [
                ["Item" => "expan_plan", "amount" => $amount], ["Item" => "army_a_100", "amount" => $amount*2],
                ["Item" => "army_b_100", "amount" => $amount], ["Item" => "beginner_back_2", "amount" => 1],
            ];
        else if($Item == "beginner_back_2")
            $Items = [
                ["Item" => "archit_a",        "amount" => $amount], ["Item" => "army_d_100", "amount" => $amount],
                ["Item" => "army_e_100",      "amount" => $amount], ["Item" => "army_f_100", "amount" => $amount],
                ["Item" => "beginner_back_3", "amount" => 1]
            ];
        else if($Item == "beginner_back_3")
            $Items = [
                ["Item" => "archit_a",       "amount" => $amount*3], ["Item" => "exp_hero_8",  "amount" => $amount*3],
                ["Item" => "army_a_1000",     "amount" => $amount],  ["Item" => "army_b_1000", "amount" => $amount],
                ["Item" => "beginner_back_4", "amount" => 1]
            ];
        else if($Item == "beginner_back_4")
            $Items = [
                ["Item" => "shopping_car",    "amount" => $amount*5], ["Item" => "bread",       "amount" => $amount*5],
                ["Item" => "army_d_1000",     "amount" => $amount],   ["Item" => "army_e_1000", "amount" => $amount],
                ["Item" => "beginner_back_5", "amount" => 1]
            ];
        else if($Item == "beginner_back_5")
            $Items = [
                ["Item" => "motiv_60",      "amount" => $amount],    ["Item" => "rec_letter", "amount" => $amount*5],
                ["Item" => "retreat_point", "amount" => $amount*10], ["Item" => "wood_box",    "amount" => $amount],
                ["Item" => "medal_silver",  "amount" => $amount*10]
            ];
        foreach ($Items as $oneItem):
            LItem::addItem($oneItem["Item"], $oneItem["amount"]);
        endforeach;
        
        return [
            "state" => "ok",
            "Item"  => $Items
        ];
        
    }
    
    
    function useHiddenBox(){
        
        $amount = validateID($_POST["amount"]);
        
        if(!LItem::useItem("hidden_box", $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        $ItemList = [];
        for($ii = 0; $ii  < $amount; $ii++){
            $Prize = selectFromTable("*", "item", "1 ORDER BY RAND() LIMIT 1");
            LItem::addItem($Prize[0]["id_item"], 1);
            $ItemList[] = [
                "Item" => $Prize[0]["id_item"],
                "amount" => 1
            ];
        }
        
        
        return [
            "state" => "ok",
            "Item" => $ItemList
        ];
    }
    function useArmyBox(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        
        if(!LItem::useItem("army_box", $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        $army_b = 2000*$amount;
        $army_e = 1000*$amount;
        $army_f = 300*$amount;
        updateTable("army_b = army_b + :ab ,  army_e = army_e + :ae  , army_f = army_f + :af ", "city", "id_city = :idc  AND id_player = :idp", ["ab"=>$army_b, "ae"=>$army_e, "af"=>$army_f, "idc"=>$idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        LSaveState::foodOutState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    
    function useHeroPacks()
    {
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        if(!isset(CHero::$HeroPacksPoints[$Item]))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        $Theater = LCityBuilding::buildingWithHeighestLvl($idCity, CITY_BUILDING_THEATER);
        
        for($iii = 0 ; $iii < $amount ; $iii++){
            $avatar = rand(0, 19);
            $name = CHero::$HeroNames[rand(0, count(CHero::$HeroNames) - 1)];
            $lvl = rand(1, max($Theater["Lvl"]*5  , 5));
            LHero::addNew($idCity, $lvl, $avatar, $name, CHero::$HeroPacksPoints[$Item]);
        }
        LSaveState::saveCityState($idCity);
        LSaveState::coinOutState($idCity);
        
        return [
            "state" => "ok"
        ];
    }
    
    function useResourcePacks()
    {
        global $idPlayer;
        $Item   = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        if(!isset(CItem::$ItemResource[$Item]))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        LSaveState::saveCityState($idCity);
        
        updateTable(
                "`".CItem::$ItemResource[$Item]["for"]."` = `".CItem::$ItemResource[$Item]["for"]."` + :a ",
                "city",
                "id_city = :idc AND id_player = :idp", ["a" =>CItem::$ItemResource[$Item]["a"]*$amount, "idc" => $idCity, "idp" => $idPlayer]);
        
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
        
    }
}
