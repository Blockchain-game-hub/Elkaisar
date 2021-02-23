<?php

class LAfterFight {

    private $Unit;
    private $Battel;

    public function __construct($Unit, $Battel) {
        $this->Unit = $Unit;
        $this->Battel = $Battel;
    }

    private function updateLvl() {
        if (LWorldUnit::isMonawrat($this->Unit["ut"]) || LWorldUnit::isCamp($this->Unit["ut"]) || LWorldUnit::isAsianSquads($this->Unit["ut"]) || LWorldUnit::isGangStar($this->Unit["ut"]) || LWorldUnit::isCarthagianArmies($this->Unit["ut"]) || LWorldUnit::isStatueWalf($this->Unit["ut"]) || LWorldUnit::isStatueWar($this->Unit["ut"])) {
            updateTable("l = l + 1", "world", "x = :xc AND y = :yc AND l <= :lv ", ["xc" => $this->Unit["x"], "yc" => $this->Unit["y"], "lv" => CWorldUnit::$MAX_UNIT_LVL[$this->Unit["ut"]]]);
        }
    }

    private function setDominant() {
        $now = time();
        if (
                LWorldUnit::isArmyCapital($this->Unit["ut"]) || LWorldUnit::isArena($this->Unit["ut"])
        ) {

            

            if (LWorldUnit::isArenaGuild($this->Unit["ut"])) {
                $idGuild = selectFromTable("id_guild", "guild_member", "id_player = :idp", ["idp" => $this->Battel["id_player"]]);
                if (count($idGuild) == 0) {
                    return;
                }
                $idDominant = $idGuild[0]["id_guild"];
            } else {
                $idDominant = $this->Battel["id_player"];
            }

            updateTable("duration = :no - time_stamp", "world_unit_rank", "x = :xc AND y = :yc ORDER BY id_round DESC LIMIT 1", ["no" => $now, "xc" => $this->Unit["x"], "yc" => $this->Unit["y"]]);
            updateTable("time_end = time_end + 300", "battel", "x_coord = :xc AND y_coord = :yc", ["xc" => $this->Unit["x"], "yc" => $this->Unit["y"]]);
            insertIntoTable("x = :xc, y = :yc, id_dominant = :idd, time_stamp = :no", "world_unit_rank", ["xc" => $this->Unit["x"], "yc" => $this->Unit["y"], "idd" => $idDominant, "no" => $now]);
            
            
        }else if(LWorldUnit::isRepelCastle($this->Unit["ut"]) || LWorldUnit::isQueenCity($this->Unit["ut"])){
            
            $idGuild = selectFromTable("id_guild", "guild_member", "id_player = :idp", ["idp" => $this->Battel["id_player"]]);
            if (count($idGuild) == 0) {
                return;
            }
            $idDominant = $idGuild[0]["id_guild"];
            
            if(LWorldUnit::isRepelCastle($this->Unit["ut"]))
                deleteTable("world_unit_rank", "x = :xc AND y = :yc", ["xc" => $this->Unit["x"], "yc" => $this->Unit["y"]]);
            
            
            updateTable("duration = :no - time_stamp", "world_unit_rank", "x = :xc AND y = :yc ORDER BY id_round DESC LIMIT 1", ["no" => $now, "xc" => $this->Unit["x"], "yc" => $this->Unit["y"]]);
            updateTable("time_end = time_end + 300", "battel", "x_coord = :xc AND y_coord = :yc", ["xc" => $this->Unit["x"], "yc" => $this->Unit["y"]]);
            insertIntoTable("x = :xc, y = :yc, id_dominant = :idd, id_guild = :idg, time_stamp = :no", "world_unit_rank", ["xc" => $this->Unit["x"], "yc" => $this->Unit["y"], "idd" => $idDominant, "no" => $now, "idg" => $idDominant]);
            
        }
    }
    private function setCityColonizer()
    {
        
            $CityLoy       = selectFromTable("loy, id_city,  player.id_player, city.name AS CityName, player.name AS PlayerName, player.id_guild", "city JOIN player ON player.id_player = city.id_player", "x = :x AND y = :y", ["x" => $this->Battel["x_coord"], "y" => $this->Battel["y_coord"]]);
            $CityColonizer = selectFromTable("id_city, player.id_player, player.name AS PlayerName, player.id_guild, player.city_flag", "city JOIN player ON player.id_player = city.id_player", "x = :x AND y = :y", ["x" => $this->Battel["x_city"],   "y" => $this->Battel["y_city"]]);
            if(!count($CityLoy) || !count($CityColonizer))
                return catchError();
            if($CityLoy[0]["loy"] <= 0)
            {
                $Colonizer = selectFromTable("*", "city_colonize", "id_city_colonized = :idc", ["idc" => $CityLoy[0]["id_city"]]);
                if(count($Colonizer))
                    if($Colonizer[0]["time_stamp"] + 24*60*60 > time()){
                        LWorld::removeCityColonizer($Colonizer[0]["id_city_colonized"]);
                    }else {
                        return;
                    }
                        
                
                insertIntoTable (
                            "id_colonizer = :idp, id_colonized = :idp2, id_city_colonizer  = :idcr, id_city_colonized = :idcd, time_stamp = :ts", 
                            "city_colonize",
                            ["idp" => $CityColonizer[0]["id_player"], "idp2" => $CityLoy[0]["id_player"], "idcr" => $CityColonizer[0]["id_city"], "idcd" => $CityLoy[0]["id_city"], "ts" => time()]);
                
                
                LSaveState::afterCityColonized($CityLoy[0]["id_city"]);
                LSaveState::afterCityColonizer($CityColonizer[0]["id_city"]);
                
                (new LWebSocket())->send(json_encode([
                    "url" => "ServerAnnounce/CityColonized",
                    "data" => [
                        "ColonizerName"     => $CityColonizer[0]["PlayerName"],
                        "ColonizedName"     => $CityLoy[0]["PlayerName"],
                        "CityColonizedName" => $CityLoy[0]["CityName"],
                        "xCoord"            => $this->Battel["x_coord"],
                        "yCoord"            => $this->Battel["y_coord"],
                        "ColonizerIdGuild"  => $CityColonizer[0]["id_guild"],
                        "ColonizerIdPlayer" => $CityColonizer[0]["id_player"],
                        "CityColonizerFlag" => $CityColonizer[0]["city_flag"]
                        ]]));
            }else {
                
                updateTable("loy = :l", "city", "id_city = :idc", ["idc" => $CityLoy[0]["id_city"], "l" => max(0, $CityLoy[0]["loy"] - 5)]);
                
            }
                
        
    }
    private function setBarrayColonizer()
    {
        global $idPlayer;
        $idPlayer = $this->Battel["id_player"];
        $id_player        = selectFromTable("id_player", "city_bar", "x_coord = :xc AND y_coord = :yc", ["xc" => $this->Unit["x"], "yc" => $this->Unit["y"]]);
        $idCity           = selectFromTable("id_city", "hero", "id_hero = :idh", ["idh" => $this->Battel["id_hero"]])[0]["id_city"];
        $attackerBarCount = selectFromTable("COUNT(*)as coun", "city_bar", "id_city = :idc", ["idc" => $idCity])[0]["coun"];
        $palaceLvl        = LCityBuilding::buildingWithHeighestLvl( $idCity, CITY_BUILDING_PALACE);
        
        
        if ($attackerBarCount >= min($palaceLvl["Lvl"], 10))
            return;
        


        if (count($id_player) > 0)
            updateTable("id_player = :idp ,id_city = :idc ", "city_bar", "x_coord = :xc AND y_coord = :yc", ["idp" => $this->Battel["id_player"], "idc" => $idCity, "xc" => $this->Unit["x"], "yc" => $this->Unit["y"]]);
        else
            insertIntoTable("id_player = :idp , id_city = :idc ,  x_coord = :xc , y_coord = :yc ", "city_bar", ["idp" => $this->Battel["id_player"], "idc" => $idCity, "xc" => $this->Unit["x"], "yc" => $this->Unit["y"]]);
        
    }

    private function setColonizer() {
        
        if ($this->Battel["task"] != BATTEL_TASK_DOMINATE) return;
        
        if (LWorldUnit::isBarrary($this->Unit["ut"]))
            $this->setBarrayColonizer();
        if (LWorldUnit::isCity($this->Unit["ut"]))
            $this->setCityColonizer();
    }
    
    private function setRank()
    {
        if($this->Battel["task"] == BATTEL_TASK_CHALLANGE)
        {
            $attackRank = selectFromTable("rank", "arena_player_challange", "id_player = :idp", ["idp" => $this->Battel["id_player"]]);
            $defRank    = selectFromTable("rank", "arena_player_challange", "id_player = :idp", ["idp" => $this->Battel["id_player_def"]]);
            
            updateTable("rank = :r, win = win + 1", "arena_player_challange", "id_player = :idp", ["idp" => $this->Battel["id_player"],     "r" => $defRank[0]["rank"]]);
            updateTable("rank = :r",                "arena_player_challange", "id_player = :idp", ["idp" => $this->Battel["id_player_def"], "r" => $attackRank[0]["rank"]]);
        }
        
    }

    public function afterWin(&$Fight) {

        $this->updateLvl();
        $this->setDominant();
        $this->setColonizer();
        $this->setRank();
        
        $this->giveWinnerPrize($Fight);
        
        LWorld::lvlChanged($this->Unit);
        
    }
    
    public function afterLose(&$Fight)
    {
        if($this->Battel["task"] == BATTEL_TASK_CHALLANGE)
        {
            updateTable("lose = lose + 1", "arena_player_challange", "id_player = :idp", ["idp" => $this->Battel["id_player"]]);
        }
        $this->giveLoserPrize($Fight);
    }

    private function keepGarrisonHero($Hero) {
        if ($Hero["is_garrsion"])
            foreach ($Hero["real_eff"] as $cell) {
                if ($cell["unit"] > 0) return true;
            }
        return false;
    }

    public function heroBattelBack($Heros) {

        if($this->Battel["task"] == BATTEL_TASK_CHALLANGE)
            return ;
            
        foreach ($Heros as $oneHero):
            if ($oneHero["id_hero"] <= 0) continue;

            $ReturningTime = LWorldUnit::calReturningTime($oneHero["id_hero"], ["x" => $oneHero["x_coord"], "y" => $oneHero["y_coord"]], $this->Unit);
            
           
            if($this->keepGarrisonHero($oneHero))
                continue;
            
            
            insertIntoTable("id_hero = :idh,"
                    . " x_from  = :xf  , y_from = :yf ,"
                    . " task = :t , x_to = :xt , y_to = :yt ,"
                    . " time_back = :rt , id_player = :idp", "hero_back",
                    [
                        "idh" => $oneHero["id_hero"],
                        "xf" => $this->Unit["x"], "yf" => $this->Unit["y"], 
                        "t"  => $this->Battel["task"], "xt" => $oneHero["x_coord"], 
                        "yt" => $oneHero["y_coord"], "rt" => $ReturningTime,
                        "idp" => $oneHero["id_player"]
            ]);


        endforeach;
    }

    public function getAllPlayers(LFight &$Fight)
    {
        
        
        
        if(LWorldUnit::isBarrary($this->Unit["ut"])){
            
            $ownerBarray = selectFromTable("id_player", "city_bar", "x_coord = :x AND y_coord = :y", ["x" => $this->Unit["x"], "y" => $this->Unit["y"]]);
            if(count($ownerBarray))
                if(!isset ($Fight->Players[$ownerBarray[0]["id_player"]])){
                    $Fight->Players[$ownerBarray[0]["id_player"]] = CPlayer::$BattelPlayerEmpty;
                    $Fight->Players[$ownerBarray[0]["id_player"]]["idPlayer"] = $ownerBarray[0]["id_player"];
                    $Fight->Players[$ownerBarray[0]["id_player"]]["side"] = BATTEL_SIDE_DEF;
                }
                    
            
        } else if(LWorldUnit::isCity($this->Unit["ut"])){
            
            $cityData = selectFromTable('id_city, id_player', "city", "x = :x AND y = :y", ["x" => $this->Unit["x"], "y" => $this->Unit["y"]]);
            
            if(count($cityData)){
                LSaveState::saveCityState($cityData[0]["id_city"]);
                if(!isset ($Fight->Players[$cityData[0]["id_player"]])){
                    $Fight->Players[$cityData[0]["id_player"]] = CPlayer::$BattelPlayerEmpty;
                    $Fight->Players[$cityData[0]["id_player"]]["idPlayer"] = $cityData[0]["id_player"];
                    $Fight->Players[$cityData[0]["id_player"]]["side"] = BATTEL_SIDE_DEF;
                }
            }
        }
    }

    public function giveWinnerPrize(LFight &$Fight)
    {
        if(!LPrize::giveImediatly($this->Unit) || $Fight->Battel["task"] == BATTEL_TASK_DOMINATE)
            return;
        
        $this->getAllPlayers($Fight);
        
        
        $Prize = new LPrize($Fight);
        
        if(LWorldUnit::isSharablePrize($this->Unit["ut"]))
            $Prize->heroShare($Fight);
        
        foreach ($Fight->Players  as &$onePlayer):
            
            if($onePlayer["idPlayer"] <= 0 || $onePlayer["side"] == BATTEL_SIDE_DEF)
                continue;
            
            
            if($onePlayer["idPlayer"] == $Fight->Battel["id_player"]){
                $Prize->givePrize($onePlayer);  
            }
            else if(LWorldUnit::isSharablePrize($this->Unit["ut"]))
                $Prize->givePrize($onePlayer);
            
            
            
        endforeach;
        
    }
    public function giveLoserPrize(LFight &$Fight)
    {
        
        if($Fight->Battel["task"] != BATTEL_TASK_CHALLANGE)
            return ;
        
        if(!LPrize::giveImediatly($this->Unit) || $Fight->Battel["task"] == BATTEL_TASK_DOMINATE)
            return ;
        
        $Prize = new LPrize($Fight);
        
        foreach ($Fight->Players as &$onePlayer):
            
            if($onePlayer["idPlayer"] <= 0 ||  $onePlayer["side"] == BATTEL_SIDE_DEF)
                continue;
            
            if($onePlayer["idPlayer"] == $Fight->Battel["id_player_def"])
                $Prize->givePrize($onePlayer);
            else if(LWorldUnit::isSharablePrize($this->Unit["ut"]))
                $Prize->givePrize($onePlayer);
                
            
            
        endforeach;
        
    }

    public function lastLvlDone() {
        
        global $ServerList;
        $idServer = validateID($_POST["server"]);
        
        $Battels = selectFromTable("*", "battel", "x_coord = :x AND y_coord = :y", ["x" => $this->Unit["x"], "y" => $this->Unit["y"]]);
        $allPlayer = array();

        foreach ($Battels as $one) {

            $players   = selectFromTable("id_hero ,  id_player", "battel_member", " id_battel = {$one["id_battel"]}");
            $time_back = 2 * time() - $one["time_start"];

            foreach ($players as $player) {

                if (!array_key_exists($player["id_player"], $allPlayer)) 
                    $allPlayer[$player['id_player']]["id_player"] = $player['id_player'];
                

                insertIntoTable("x_from = :x , id_hero = {$player["id_hero"]} ,"
                        . " y_from = :y , task = {$one["task"]}, "
                        . "time_back = $time_back , id_player = {$player["id_player"]}", 
                        "hero_back", ["x" => $this->Unit["x"], "y" => $this->Unit["y"]]);
            }
            LBattelFinish::removeBattel($one["id_battel"]);
        }
        
        $CancelAnnounce = [
            "url" => "WS_Battel.BattelCanceled",
            "data" => [
                "Players" => $allPlayer,
            ]
        ];
        
        (new LWebSocket())->send(json_encode($CancelAnnounce));
       
    }

    
    public function afterWinAnnounce(&$Players) {
        
        if(!LWorld::afterWinAnnounceable($this->Unit))
            return;
        
        if(LWorldUnit::isRepelCastle($this->Unit["ut"]) || LWorldUnit::isQueenCity($this->Unit["ut"])){
           
            $this->announceGuildWin();
            return;
        }
        
        $TotalHonor = 0;
        $ItemPrize = [];
        $idPlayers = [
            "Def" => [],
            "Att" => []
        ];
        foreach ($Players as $player){
         
            
            if($player["side"] == BATTEL_SIDE_DEF)
            {
                $idPlayers["Def"][] =  $player["idPlayer"];
                continue;
            }
                
            $TotalHonor += $player["Honor"];
            $ItemPrize   = array_merge($ItemPrize, $player["ItemPrize"]);
            
            if($this->Battel["id_player"]  != $player["idPlayer"])
                $idPlayers["Att"][]        = $player["idPlayer"];
            if(count($idPlayers["Att"]) +  count($idPlayers["Def"])> 4)
                break;
        }
        $Joiner = [];
        $Defender = [];
        
        if(count($idPlayers["Att"]))
           $Joiner =  selectFromTable("id_player, name", "player", "id_player IN (". implode(",", array_unique($idPlayers["Att"])).")");
        if(count($idPlayers["Def"]))
           $Defender =  selectFromTable("id_player, name", "player", "id_player IN (". implode(",", array_unique($idPlayers["Def"])).")");
        
        $this->Unit["t"] = $this->Unit["ut"];
        $WinAnnounce = [
            "url" => "ServerAnnounce/BattelWin",
            "data" => [
                "Attacker"  => selectFromTable("id_player, name", "player", "id_player = :idp", ["idp" => $this->Battel["id_player"]])[0],
                "Joiners"   => $Joiner,
                "Defender"  => $Defender,
                "EnemyName" =>"بطل النظام",
                "WinPrize"  => $ItemPrize,
                "honor"     => $TotalHonor,
                "WorldUnit" => $this->Unit
            ]
        ];
        
        (new LWebSocket())->send(json_encode($WinAnnounce));
        
    }

    private function announceGuildWin()
    {
        $GuildPlayer = selectFromTable(
                "guild_member.id_guild, guild_member.id_player, guild.slog_top, guild.slog_cnt, guild.slog_btm, guild.name AS GuildName, player.name AS PlayerName", 
                "guild_member JOIN guild ON guild.id_guild = guild_member.id_guild JOIN player ON player.id_player = guild_member.id_player", 
                "guild_member.id_player = :idp", ["idp" => $this->Battel["id_player"]]);
        
        
        if(!count($GuildPlayer))
            return ;
        
        
        $WinAnnounce = [
            "url" => "ServerAnnounce/BattelGuildWin",
            "data" => [
                "Guild"     => $GuildPlayer[0],
                "Battel" => $this->Battel
            ]
        ];
        
        
        (new LWebSocket())->send(json_encode($WinAnnounce));
        
    }
    
    private function announcePlayerWin()
    {
        $Player = selectFromTable("name AS PlayerName, guild AS GuildName,id_guild, id_player, porm", "player",  "id_player = :idp", ["idp" => $this->Battel["id_player"]]);
        
        if(!count($Player))
            return ;
        
        
        $WinAnnounce = [
            "url" => "ServerAnnounce/BattelPlayerWin",
            "data" => [
                "Player"     => $Player[0],
                "Battel" => $this->Battel
            ]
        ];
        
        (new LWebSocket())->send(json_encode($WinAnnounce));
        
    }
}
