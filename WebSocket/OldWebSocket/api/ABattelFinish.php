<?php

class ABattelFinish
{
    
    function Finish()
    {
     
        $Battels = LBattelFinish::timeEndedBattels();
       // print_r($Battels);
        $JsonBattels = [];
        
        foreach ($Battels as $oneBattel)
        {
            $Fight      = new LFight($oneBattel);
            $Heros      = $Fight->startFight();
            $Unit       = $Fight->Unit;
            $AfterFight = new LAfterFight($Unit, $oneBattel);
            $AfterFight->heroBattelBack($Heros);
            
            if($Fight->sideWin == BATTEL_SIDE_ATT)
                $AfterFight->afterWin($Fight);
            else 
                $AfterFight->afterLose($Fight);
            
           
            LBattelFinish::addBattelReport($Fight);
            LBattelFinish::removeBattel($oneBattel["id_battel"]);
            $AfterFight->afterWinAnnounce($Fight->Players);
            $Fight->killHeroArmy();
            
            
            $JsonBattels[] = [
                "Battel"  => $Fight->Battel,
                "Players" => array_values($Fight->Players) ,
                "FireOff" => LWorld::fireOffUnit($Unit)
            ];
            if(CWorldUnit::isLastLvl($Unit))
                $AfterFight->lastLvlDone();
        }
        
        return $JsonBattels;
    }
    
}
