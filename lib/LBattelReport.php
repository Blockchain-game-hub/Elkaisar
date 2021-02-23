<?php
class LBattelReport
{
    private $idReport;
    private $id_player;
    private $x_coord;
    private $y_coord;
    
    public function __construct(LFight $Fight) 
    {
        $this->idReport = insertIntoTable( "x = :xc , y = :yc , time_stamp = :ts , side_win = :ws , attacker = :at , round_num = :rn , task = :t, lvl =:l","report_battel", [
            "xc" => $Fight->Unit["x"], "yc" => $Fight->Unit["y"],
            "ts" => time(), "ws" => $Fight->sideWin, "at" => $Fight->Battel["id_player"], 
            "rn" => $Fight->roundNum,  "t"=> $Fight->Battel["task"], "l" => $Fight->Unit["l"]
        ]);
        
        if($this->idReport <= 0)
            TryToHack ();
    }
    
    public function addPlayer($Player , $side)
    {
        if($Player["idPlayer"] <=0 )
            return ;
        
        insertIntoTable(
                "id_player= :idp, id_report= :idr , side = :s, honor = :h, time_stamp = :t" ,
                'report_player', [
                    "idp" => $Player["idPlayer"], "idr" => $this->idReport ,"s" => $side, "h" => $Player["Honor"], "t" => time()
                    
                ]);
        $this->addPrize($Player);
    }
    
    public function addHero($Hero){
        
        $q  = "     id_hero   = :idh , id_player = :idp , id_report = :idr , "
                . " f_1_pre   = :f1p,  f_2_pre   = :f2p,  f_3_pre = :f3p, "
                . " b_1_pre   = :b1p,  b_2_pre   = :b2p,  b_3_pre = :b3p, "
                . " f_1_post  = :f1po, f_2_post  = :f2po, f_3_post = :f3po, "
                . " b_1_post  = :b1po, b_2_post  = :b2po, b_3_post = :b3po, "
                . " f_1_type  = :f1t,  f_2_type  = :f2t,  f_3_type = :f3t, "
                . " b_1_type  = :b1t,  b_2_type  = :b2t,  b_3_type = :b3t, "
                . " side      = :s ,   ord       = :ord , xp = :exp";
        return insertIntoTable(
                $q,"report_hero",
                [
                    "idh"  => $Hero["id_hero"],     "idp"  => $Hero["id_player"],   "idr" =>  $this->idReport,
                    "f1p"  => $Hero["pre"]["f_1"],  "f2p"  => $Hero["pre"]["f_2"],  "f3p" =>  $Hero["pre"]["f_3"], 
                    "b1p"  => $Hero["pre"]["b_1"],  "b2p"  => $Hero["pre"]["b_2"],  "b3p" =>  $Hero["pre"]["b_3"], 
                    "f1po" => $Hero["post"]["f_1"], "f2po" => $Hero["post"]["f_2"], "f3po" => $Hero["post"]["f_3"], 
                    "b1po" => $Hero["post"]["b_1"], "b2po" => $Hero["post"]["b_2"], "b3po" => $Hero["post"]["b_3"], 
                    "f1t"  => $Hero["type"]["f_1"], "f2t"  => $Hero["type"]["f_2"], "f3t" =>  $Hero["type"]["f_3"], 
                    "b1t"  => $Hero["type"]["b_1"], "b2t"  => $Hero["type"]["b_2"], "b3t" =>  $Hero["type"]["b_3"], 
                    "s"    => $Hero["side"],        "ord"  => $Hero["ord"],         "exp" => $Hero["gainXp"]
                ]);
       
    }
    
    public function addPrize($Player)
    {
        
        
      
            foreach ($Player["ItemPrize"] as $one){
                insertIntoTable(
                        "id_report = :idr, id_player = :idp, prize = :it, amount = :a",
                        "report_mat_prize",
                        [
                            "idr" => $this->idReport, "idp" => $Player["idPlayer"],
                            "it"  => $one["Item"], "a" => $one["amount"]
                        ]);
            }
            
       
        
        
        if(array_sum($Player["ResourcePrize"]) <= 0 )
            return ;
        
        insertIntoTable("id_report = :idr, id_player = :idp, "
                . "food  = {$Player["ResourcePrize"]["food"] }, "
                . "wood  = {$Player["ResourcePrize"]["wood"] }, "
                . "stone = {$Player["ResourcePrize"]["stone"]}, "
                . "metal = {$Player["ResourcePrize"]["metal"]}, "
                . "coin  = {$Player["ResourcePrize"]["coin"] } ", 
                "report_res_prize",
                        ["idr" => $this->idReport, "idp" => $Player["idPlayer"]]);
            
        
       
       
    }
    
    public static function getPlayerReport($id_player , $offset)
    {
        global $dbh;
        $sql = $dbh->prepare("SELECT DISTINCT id_report , seen FROM report_player WHERE id_player  = '$id_player' ORDER BY id_report DESC LIMIT 10 OFFSET $offset");
        $sql->execute();
        return$sql->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getReportHeader($id_report , $type="")
    {

        global $dbh;
        $sql = $dbh->prepare("SELECT * FROM report_battel WHERE id_report  = '$id_report'");
        $sql->execute();
        $report =  $sql->fetch(PDO::FETCH_ASSOC);
        if(is_array($report)){
            $row = WorldMap::getUnitData($report["x"], $report["y"]);
            $row["x"] = $report["x"];
            $row["y"] = $report["y"];
            $row["time_stamp"] = date("j M y", ($report["time_stamp"]));
            $row["id_report"] = $report["id_report"];
            $row["type"] = "battel";
            return $row;
        }else{
            return FALSE;
        }
        
        
        
    }
   
    public static function getHeros($id_report)
    {
        global $dbh;
        $sql = $dbh->prepare("SELECT report_hero.* , player.name AS p_name , hero.name AS h_name , hero.avatar FROM "
                . " report_hero  LEFT JOIN player ON report_hero.id_player = player.id_player "
                . " LEFT JOIN hero ON hero.id_hero = report_hero.id_hero "
                . " WHERE report_hero.id_report = :idr ORDER BY ord ASC");
        $sql->execute(["idr" => $id_report]);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getPrize($id_report , $id_player)
    {
        
        $prize["matrial"] = selectFromTable("prize, amount", "report_mat_prize", "id_player = $id_player AND id_report = $id_report");
        $res = selectFromTable("*", "report_res_prize", "id_player = $id_player AND id_report = $id_report");
        if(count($res) > 0){
            
            $prize["resource"] = $res[0];
            
        }else{
            
           $prize["resource"]  = [
               
               "food"=>0,
               "wood"=>0,
               "stone"=>0,
               "metal"=>0,
               "coin"=>0
               
           ]; 
            
        }
        $prize["honor"] = selectFromTable("honor", "report_player", "id_player = $id_player AND id_report = $id_report")[0]["honor"];
        
        return $prize;
    }
    
    public function getHeroXp($Unit)
    {
        
        
        
        if(LWorldUnit::isAsianSquads($Unit["ut"])){
            
            $arr_xp = [ WUT_FRONT_SQUAD      =>rand(416500   ,426500),  WUT_FRONT_BAND       =>rand(610256   ,620256),  WUT_FRONT_SQUADRON      =>rand(810256   ,820256),  WUT_FRONT_DIVISION      =>rand(1010256  ,1020256),
                        WUT_ARMY_LIGHT_SQUAD =>rand(1320256  ,1420256), WUT_ARMY_LIGHT_BAND  =>rand(1510256  ,1610256), WUT_ARMY_LIGHT_SQUADRON =>rand(2150256  ,2250256), WUT_ARMY_LIGHT_DIVISION =>rand(2250256  ,2350256),
                        WUT_ARMY_HEAVY_SQUAD =>rand(2250256  ,2450256), WUT_ARMY_HEAVY_BAND  =>rand(2350256  ,2650256), WUT_ARMY_HEAVY_SQUADRON =>rand(2400265  ,2800265), WUT_ARMY_HEAVY_DIVISION =>rand(2950265  ,3050265),
                        WUT_GUARD_SQUAD      =>rand(3050265  ,3450265), WUT_GUARD_BAND       =>rand(3550265  ,3750265), WUT_GUARD_SQUADRON      =>rand(4000128  ,4100128), WUT_GUARD_DIVISION      =>rand(4200128  ,4400128),
                        WUT_BRAVE_THUNDER    =>rand(10400128  ,11000128)];
            
            return $arr_xp[$Unit["ut"]];
            
        }else if(LWorldUnit::isBarrary($Unit["ut"])){
            $arr_xp = [
                1 => rand(50, 100), 2=> rand(80, 150), 3=> rand(300, 400),
                4 => rand(400, 500), 5=> rand(1300, 1400), 6=> rand(3200, 3800),
                7 => rand(7000, 8500), 8=> rand(32000, 48000), 9=> rand(63522, 80000),
                10=> rand(120000, 180000),
            ];
            return $arr_xp[$Unit["l"]];
            
        }else if(LWorldUnit::isCamp($Unit["ut"]) || LWorldUnit::isMonawrat($Unit["ut"])){
            
            $factor = 1;
            if($Unit["l"]%10){
                $factor = 3;
            }
            
            return rand((pow($Unit["l"], 2)*100), (pow($Unit["l"], 2)*110));
            
        }else if(LWorldUnit::isGangStar($Unit["ut"])){
            return rand(500, 650) * $Unit["l"];
        }else if (LWorldUnit::isCarthagianArmies($Unit["ut"])) {
            
           $arr_xp = [
                WUT_CARTHAGE_GANG    =>[rand(9000, 10000)     , rand(65000, 70000)    , rand(12000, 14000)    , rand(85000, 90000)],
                WUT_CARTHAGE_TEAMS   =>[rand(28000, 30000)    , rand(180000, 190000)  , rand(42000, 44000)    , rand(350000, 360000)],
                WUT_CARTHAGE_REBELS  =>[rand(650000, 660000)  , rand(1900000, 2000000), rand(950000, 1000000) , rand(2800000, 2900000)],
                WUT_CARTHAGE_FORCES  =>[rand(9500000, 1000000), rand(2800000, 3000000), rand(9500000, 1000000), rand(3000000, 3200000)],
                WUT_CARTHAGE_CAPITAL =>[rand(2800000, 3000000), rand(8200000, 8500000), rand(4100000, 4300000), rand(16200000, 16500000)]
               ]; 
           
           
               return $arr_xp[$Unit["ut"]][$Unit["l"] < 5 ? 0 : ($Unit["l"] == 5 ? 1 : ($Unit["l"] == 10 ? 3 : 2))];
           
        }
        return 10000;
        
    }
    public static function getGeneralData($id_report)
    {
        global $dbh;
        $sql = $dbh->prepare("SELECT round_num , side_win , name AS p_name , x, y, time_stamp, task FROM report_battel JOIN player ON "
                . " report_battel.id_report = :idr AND player.id_player = report_battel.attacker ");
        $sql->execute(["idr" => $id_report]);
        
        return $fetch = $sql->fetch(PDO::FETCH_ASSOC);
        
    }
    public static function updateReportSeen($id_report, $id_player)
    {
        
        global $dbh;
        $sql = $dbh->prepare("UPDATE report_player SET seen = 1 WHERE id_report = $id_report AND id_player = $id_player");
        $sql->execute();
        
        
    }
    
}

