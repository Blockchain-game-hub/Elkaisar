<?php

class LFight
{
    public $Players = [];    
    public $sideWin ;   
    public $Heros ;    
    public $Unit ;  
    public $roundNum = 0 ;  
    public $totalHonor = 0;
    public $Battel;
    public $TotalKill = [
        BATTEL_SIDE_ATT => [
            "killed" => 0,
            "kills" => 0
        ],
        BATTEL_SIDE_DEF => [
            "killed" => 0,
            "kills" => 0
        ]
    ];
    public $TotalHonor = [
        BATTEL_SIDE_ATT => 0,
        BATTEL_SIDE_DEF => 0
    ];


    public function __construct($Battel) 
    {
        $this->Battel = $Battel;
        $this->Unit        = LWorld::UnitData($this->Battel["x_coord"], $this->Battel["y_coord"]);
        
    }
    
   
    public function startFight()
    {
        
        $check_loop_overflow = 0; 
        $Heros = LBattel::getHeros($this->Battel, $this->Unit);
        $this->Players = LBattel::getPlayers($Heros);
        
        do{
            
            /*
             * 
             */
            $def_heros = [];
            $attack_heros = [];
            
            foreach ($Heros as &$hero):
              
                if(!$this->checkHeroSweped($hero) && $hero["side"] == BATTEL_SIDE_DEF && count($def_heros) <= 3){

                    $def_heros[] = &$hero;
                    

                }
                elseif(!$this->checkHeroSweped($hero) && $hero["side"] == BATTEL_SIDE_ATT  && count($attack_heros) <= 3){

                    $attack_heros[] = &$hero;
                    
                    
                }
                
                if(count($attack_heros) == 3 && count($def_heros) == 3)
                    break;

            endforeach;
            
             /*
              * condetion    to check if arrays contains data
              */
            if(count($attack_heros) > 0 && count($def_heros) > 0){
                
                /* icrement round number*/
                $this->roundNum ++;
                /* start new round*/
                $this->roundFight($attack_heros, $def_heros);
                
            }
            
            $check_loop_overflow ++;
            
            
        }while (count($attack_heros) > 0 && count($def_heros) > 0 && $check_loop_overflow < 2500);
        
       
        
        $this->endFight($Heros); 
        
        $this->Heros = $Heros;
        return $Heros;
    }
    
    
    
    
    


    /*
     *  this function is  called after attack finished
     *  first i get all players joind_battel
     *  then i distribute prize , honor fo each player
     */
    
    
    
    
    /*
     * {multi-D-Array} $attack side (contain at most 3 heros to fight)
     * {multi-D-Array} $def_side (contain at most 3 heros to fight)
     * 
     * this function fight each hero as infront one 
     * attack side hero fight only its opposite side hero
     */
    public function roundFight(&$attack_side  ,  &$def_side)
    {
        
        
        foreach ($attack_side as $place => &$hero):
            
            
            /* if the defence side has hero in opposite cell the current hero will fight him*/
            if(isset($def_side[$place])){
                
                $this->heroFight($hero, $def_side[$place]);
                
            }
            /* if the defence side has hero in sec oppsoite cell  the current hero will fight him
             * 
             * this condetion  true if the current hero is the first or secound one
             * and false if the current is the 3rd hero
             */
            elseif (isset($def_side[$place +1])) {
                
                $this->heroFight($hero, $def_side[$place + 1]);
                
            }
            /*
             * if the side defence has hero in the third place affter the opposite to current hero
             * 
             * this condtion true if the current hero is only the first hero
             */
            elseif (isset($def_side[$place +2])) {
                
                $this->heroFight($hero, $def_side[$place + 2]);
                
            }
            
            /*
             * if the side defence has hero in the one  place before the opposite to current hero
             * 
             * this condtion true if the current hero is the secound heror or third one
             */
            elseif (isset($def_side[$place -1])) {
                
                $this->heroFight($hero, $def_side[$place - 1]);
                
            }
            /*
             * if the side defence has hero in  two  places before the opposite to current hero
             * 
             * this condtion true if the current hero is  third one
             */
            elseif (isset($def_side[$place - 2])) {
                
                $this->heroFight($hero, $def_side[$place - 2]);
                
            }
            
            
        endforeach;
        
        foreach ($def_side as $place => &$hero):
            
            
            if(isset($attack_side[$place])){
                
                $this->heroFight($hero, $attack_side[$place]);
                
            }elseif (isset($attack_side[$place +1])) {
                
                $this->heroFight($hero, $attack_side[$place + 1]);
                
            }elseif (isset($attack_side[$place +2])) {
                
                $this->heroFight($hero, $attack_side[$place + 2]);
                
            }elseif (isset($attack_side[$place -1])) {
                
                $this->heroFight($hero, $attack_side[$place - 1]);
                
            }elseif (isset($attack_side[$place - 2])) {
                
                $this->heroFight($hero, $attack_side[$place - 2]);
                
            }
            
            
        endforeach;
        
        
        /*   afetr round finish i should scane heros to remove the  dead heros */
        
        $this->scanHero($def_side);
        $this->scanHero($attack_side);
        
        //$this->addSpesialPower($def_side);
        //$this->addSpesialPower($attack_side);
        
    }
    

    
    
    
    
    /*
     *  this function loop through array of heros
     *      get equipment effect
     *      get normal army equipment
     */
    
   

    

    /*  الفنكشن دى انا هديها الخانتين الى هيحربوا بعض
    والداتا هتتبعت  بالريفرنس 
     * انا كل الى هعملة انى هغير عدد ال وحداات الى فى كل خانة     */
    public function cellFight(&$cell_attack , &$cell_def)
    {
        
        /* trimnate condetion*/
        if($cell_attack["unit"] <= 0 )
            return;
        
        
        $attack = $cell_attack["attack"];
        $def   = $cell_def["def"] ;
        $vitality = $cell_def["vit"];
        //  first calculate  how many unit there defence has been broken
        $undefence_unit = ceil($cell_attack["unit"]*$attack/$def);

        //then calculate how many unit  die
        $deadUnits = ceil($cell_attack["unit"]*$cell_attack["dam"]/$vitality);

        // get the min number
        $total_dead = min($undefence_unit , $deadUnits);
        
        /* this condetion will check  if the two cells have not fight done*/
      
            
            
        $cell_def["dead_unit"]  += $total_dead;
        $amountDead = CArmy::$ArmyCap[$cell_def["armyType"]]*$total_dead;
        $cell_attack["troopsKills"]  += $amountDead;
        $cell_def   ["troopsKilled"] += $amountDead;
        $cell_attack ["honor"]       +=  ceil($total_dead/$cell_def["def"]);
        $cell_attack ["points"]      +=  ceil($total_dead);
        
    }
    
    
    /**/
    public function heroFight(&$hero_attck , &$hero_def)
    {
        /* لو البطل ششايل اكتر من ثلاثة خانات كدة انا هخلى كل خانة تضرب الخانة الى قصادها 
والخانة الى وراها         */
        
            
            foreach ($hero_attck["real_eff"] as $place => &$cell):
                
                if($cell["unit"] == 0){
                    continue;
                }
                
                /* first if i loop throght all cells */
                if(isset($hero_def["real_eff"][$place]) 
                        && $hero_def["real_eff"][$place]["unit"] > $hero_def["real_eff"][$place]["dead_unit"] ){ // fight FIRST row                                     ATTACK SIDE
                                                                                                                  //                                              
                    $this->cellFight($cell, $hero_def["real_eff"][$place]);                                       //                       +------------------------+------------------------+-------------------------+
                                                                                                                  //                       |                        |                        |                         |                
                }elseif(isset ($hero_def["real_eff"][$place + 3] )  // this will fight the back cell to him
                        && $hero_def["real_eff"][$place+3]["unit"]>$hero_def["real_eff"][$place+3]["dead_unit"] ){//                       |            4           |            5           |             6           |                
                                                                                                                  //                       |                        |                        |                         |                
                    $this->cellFight($cell, $hero_def["real_eff"][$place + 3 ]);                                  //                       |________________________|________________________|_________________________|
                                                                                                                  //                       |                        |                        |                         |               
                } elseif (isset ($hero_def["real_eff"][$place + 1] ) 
                        && $hero_def["real_eff"][$place+1]["unit"]>$hero_def["real_eff"][$place+1]["dead_unit"] ) { //                     |            1           |            2           |              3          |                
                                                                                                                  //                       |                        |                        |                         |                
                    $this->cellFight($cell, $hero_def["real_eff"][$place + 1 ]);                                  //                       +------------------------+------------------------+-------------------------+                       
                                                                                                                  //
                } elseif ( isset ($hero_def["real_eff"][$place + 4] ) 
                        && $hero_def["real_eff"][$place+4]["unit"]>$hero_def["real_eff"][$place+4]["dead_unit"] ) { //
                                                                                                                  //
                    $this->cellFight($cell, $hero_def["real_eff"][$place + 4 ]);                                  //
                                                                                                                  //
                } elseif ( isset ($hero_def["real_eff"][$place + 2] )
                        && $hero_def["real_eff"][$place+2]["unit"]>$hero_def["real_eff"][$place+2]["dead_unit"] ) { //
                                                                                                                  //_____________________________________________________________________________________________________________________________
                    $this->cellFight($cell, $hero_def["real_eff"][$place + 2 ]);                                  //
                                                                                                                  //
                } elseif ( isset ($hero_def["real_eff"][$place + 5] )
                        && $hero_def["real_eff"][$place+5]["unit"]>$hero_def["real_eff"][$place+5]["dead_unit"] ) { //
                                                                                                                  //
                    $this->cellFight($cell, $hero_def["real_eff"][$place + 5 ]);                                  //
                                                                                                                  //                       +------------------------+------------------------+-------------------------+
                } elseif ( isset ($hero_def["real_eff"][$place - 1] )
                        && $hero_def["real_eff"][$place-1]["unit"]>$hero_def["real_eff"][$place-1]["dead_unit"] ) { //                     |                        |                        |                         |
                                                                                                                  //                       |           1            |           2            |            3            |
                    $this->cellFight($cell, $hero_def["real_eff"][$place - 1 ]);                                  //                       |                        |                        |                         |
                                                                                                                  //                       |________________________|________________________|_________________________|
                }elseif(isset ($hero_def["real_eff"][$place - 4] ) 
                        && $hero_def["real_eff"][$place-4]["unit"]>$hero_def["real_eff"][$place-4]["dead_unit"] ){  //                     |                        |                        |                         |
                                                                                                                  //                       |           4            |            5           |            6            |
                    $this->cellFight($cell, $hero_def["real_eff"][$place - 4 ]);                                  //                       |                        |                        |                         |
                                                                                                                  //                       +------------------------+------------------------+-------------------------+
                } elseif (isset ($hero_def["real_eff"][$place - 5] ) 
                        && $hero_def["real_eff"][$place-5]["unit"]>$hero_def["real_eff"][$place-5]["dead_unit"] ) { //
                                                                                                                  //
                    $this->cellFight($cell, $hero_def["real_eff"][$place - 5 ]);           
                                                                                                                  //                                      
                                                                                                                  //                                        DEFENCE SIDE
                }
                
                /* this will be plus attribute if the player has only 3 cells in */
                
                /*
                 * this case will work only if  the attcker has less than 4 cells
                 *  current cell will fight the corosponding back  cell only 
                 * if the the crosponding cell has army and the back ony sure
                 */
                if(count($hero_attck["real_eff"]) <= 3){
                    
                    if(isset ($hero_def["real_eff"][$place  + 3] )  && isset ($hero_def["real_eff"][$place] ) 
                            && $hero_def["real_eff"][$place + 3]["unit"]>$hero_def["real_eff"][$place+3]["dead_unit"] ){                            //
                                                                                               
                        $this->cellFight($cell, $hero_def["real_eff"][$place + 3 ]); 
                        
                                                                                                    
                    }
                    
                }
                
            endforeach;
                    
        
    }
    
    
    /*
     * {muti d array}  $hero_arr
     * this function used  to scane hero 
     *   loop for each hero 
     *      loop for eachcell
     */
    
    
    public function scanHero(&$hero_arr)
    {
        
        foreach ($hero_arr as &$hero){ // loop  through heros
           $hero["standTillRound"] ++; 
            foreach ( $hero["real_eff"] as &$cell){ 
                
                $hero["honor"]          += $cell["honor"];      //  calculate the gained honor after round
                $hero["points"]         += $cell["points"];      //  calculate the gained honor after round
                $hero["troopsKilled"]   += $cell["troopsKilled"]; 
                $hero["troopsKills"]    += $cell["troopsKills"];
                
                $this->Players[$hero["id_player"]]["Kills"]  += $cell["troopsKills"];
                $this->Players[$hero["id_player"]]["Killed"] += $cell["troopsKilled"];
                $this->Players[$hero["id_player"]]["Honor"]  += $cell["honor"];
                $this->Players[$hero["id_player"]]["RemainTroops"][$cell["armyType"]] -= $cell["dead_unit"];
                        
                $this->TotalKill[$hero["side"]]["killed"]  +=  $cell["troopsKilled"];
                $this->TotalKill[$hero["side"]]["kills"]   +=  $cell["troopsKills"];
                
                $cell["troopsKilled"] = 0;
                $cell["troopsKills"]  = 0;
                
                $cell["unit"]      = max(0 , ($cell["unit"] - $cell["dead_unit"]) );
                $cell["dead_unit"] = 0;
                $cell["honor"]     = 0;                 
                $cell["points"]    = 0; 
                $cell["armyType"] = $cell["unit"] > 0? $cell["armyType"] : 0;
                
            }
            
            
        }
        
    }
    

    public function checkHeroSweped($hero)
    {
       
        
        foreach ($hero["real_eff"] as $one){
            
            if ($one["unit"] != 0){
                return FALSE;
            }
            
        }
        
        return TRUE;
        
    }
    
    
    public function endFight(&$all_heros)
    {
        $total_unit = [0,0];
        
        
        /*
         * looping through all heros to get win side
         */
        foreach ($all_heros as &$hero):
            
            /* FRIST cell in hero*/
            if(isset($hero["real_eff"][1])){
                $hero["post"]["f_1"] = $hero["pre"]["f_1"]  -  $hero["real_eff"][1]["unit"]; // set post value of cell
                
                $total_unit[$hero["side"]] += $hero["real_eff"][1]["unit"];// increment  value to get the winner hero and dead one
            }
            
            if(isset($hero["real_eff"][2])){
                $hero["post"]["f_2"] = $hero["pre"]["f_2"]  -  $hero["real_eff"][2]["unit"];
                
                $total_unit[$hero["side"]] += $hero["real_eff"][2]["unit"];
            }
            
            if(isset($hero["real_eff"][3])){
                $hero["post"]["f_3"] = $hero["pre"]["f_3"]  -  $hero["real_eff"][3]["unit"];
                
                $total_unit[$hero["side"]] += $hero["real_eff"][3]["unit"];
            }
            
            if(isset($hero["real_eff"][4])){
                $hero["post"]["b_1"] = $hero["pre"]["b_1"]  -  $hero["real_eff"][4]["unit"];
                $total_unit[$hero["side"]] += $hero["real_eff"][4]["unit"];
            }
            
            if(isset($hero["real_eff"][5])){
                $hero["post"]["b_2"] = $hero["pre"]["b_2"]  -  $hero["real_eff"][5]["unit"];
                $total_unit[$hero["side"]] += $hero["real_eff"][5]["unit"];
            }
            
            if(isset($hero["real_eff"][6])){
                $hero["post"]["b_3"] = $hero["pre"]["b_3"]  -  $hero["real_eff"][6]["unit"];
                $total_unit[$hero["side"]] += $hero["real_eff"][6]["unit"];
            }
            
        endforeach;
        
        
        /*    assign the winner   */
        if($total_unit[BATTEL_SIDE_ATT] > 0){
            $this->sideWin = BATTEL_SIDE_ATT;
        } else {
            $this->sideWin = BATTEL_SIDE_DEF;    
        }
        
    }
    
    function killHeroArmy()
    {
        
        foreach ($this->Heros as $oneHero)
        {
            LHeroArmy::killHeroArmy($oneHero, $this);
            if($oneHero["side"] != $this->sideWin)
                updateTable("loyal = GREATEST(loyal - 1, 0)", "hero", "id_hero = :idh", ["idh" => $oneHero['id_hero']]);
        }
        
    }
}

