<?php

class AEquip{
    
    
    function getEquipPower(){
        
        $idEquip = validateGameNames($_GET["idEquip"]);
        
        $EquipData = explode("_", $idEquip);
        return selectFromTable("*", "equip_power", "equip = :e AND part = :p AND lvl = :l", ["e" => $EquipData[0], "p" => $EquipData[1], "l" => $EquipData[2]]);
        
    }
    function changeEquipPower(){
        /*  idEquip   : idEquip,
            attack    :$("#point-attack").val(),
            defence   :$("#point-defence").val(),
            vitality  :$("#point-vitality").val(),
            damage    :$("#point-damage").val(),
            break     :$("#point-break").val(),
            anti_break:$("#point-anti-break").val(),
            strike    :$("#point-strike").val(),
            immunity  :$("#point-immunity").val(),
            sp_attr   : $("#point-sp_attr").val()*/
        /*attack	defence	vitality	damage	break	anti_break	strike	immunity	sp_attr 	*/
        $idEquip     = validateGameNames($_POST["idEquip"]);
        $EquipData   = explode("_", $idEquip);
        $attack      = validateID($_POST["attack"]);
        $defence     = validateID($_POST["defence"]);
        $vit         = validateID($_POST["vitality"]);
        $dam         = validateID($_POST["damage"]);
        $break       = validateID($_POST["break"]);
        $anti_break  = validateID($_POST["anti_break"]);
        $strike      = validateID($_POST["strike"]);
        $immunity    = validateID($_POST["immunity"]);
        $sp_attr     = validateID($_POST["sp_attr"]);
        
        
        updateTable(
                "attack = $attack, defence = $defence, vitality = $vit, damage = $dam, break = $break, anti_break = $anti_break, strike = $strike, immunity = $immunity, sp_attr = $sp_attr",
                "equip_power", "equip = :e AND part = :p AND lvl = :l", ["e" => $EquipData[0], "p" => $EquipData[1], "l" => $EquipData[2]]);
        
        
    }
    
}
