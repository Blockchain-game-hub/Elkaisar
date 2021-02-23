<?php

class LBattelFinish
{
    
    static function timeEndedBattels()
    {
        $time = time();
        $battels = selectFromTable("*", "battel", "time_end <= :ti AND done = 0", ["ti" => $time]);
        $last = end($battels);
        if(isset($last["id_battel"])){
            updateTable("done = 1", "battel", "time_end <= :id" ,  ["id" => $time]);
        }
        return $battels;
    }
    
    public static function addBattelReport(LFight &$Fight)
    {
        
        $report = new LBattelReport($Fight);
        
       
        foreach ($Fight->Heros as $oneHero){
            
            $xp_gain = 0;
            if($oneHero["id_hero"] > 0 ){
                if($oneHero["side"] == BATTEL_SIDE_ATT)
                        $xp_gain = $Fight->TotalKill[BATTEL_SIDE_ATT]["kills"] > 0? $report->getHeroXp($Fight->Unit)*$oneHero["troopsKills"]/$Fight->TotalKill[BATTEL_SIDE_ATT]["kills"] : 0;
                
                updateTable("exp = exp + :e", "hero", "id_hero = :idh", ["idh" => $oneHero["id_hero"], "e" => $xp_gain]);
                $oneHero["gainXp"] = $xp_gain;
            }
            
            $report->addHero($oneHero);
            
        }
        
        foreach ($Fight->Players as $key => $onePlayer)
        {
            
            $report->addPlayer($onePlayer, $key);
           
            
        }
        
        
        
    }
    
    public static function removeBattel($idBattel)
    {
        deleteTable("battel", "id_battel = :idb", ["idb" => $idBattel]);
        deleteTable("battel_member",  "id_battel = :idb", ["idb" => $idBattel]);
    }
    
}
