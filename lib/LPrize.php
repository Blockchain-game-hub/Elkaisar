<?php

class LPrize {

    public $Unit;
    public $heros;
    public $PlayerCities;
    public $totalResources = [
        "food" => 0,
        "wood" => 0,
        "stone" => 0,
        "metal" => 0,
        "coin" => 0,
    ];
    public $cityAvailResources;
    public $totalResCap = 0;
    public static $CITY_RES_TAKE_RATE = [
        "food" => 0.4,
        "wood" => 0.3,
        "stone" => 0.15,
        "metal" => 0.1,
        "coin" => 0.05
    ];

    public function __construct(LFight &$Fight) {

        $this->Unit = $Fight->Unit;
        if (LWorldUnit::isCity($Fight->Unit["ut"])) {
            $this->getCityAvailRes();
            $this->takeCityRes();
        }
        $this->PlayerCities = [
            BATTEL_SIDE_DEF => [],
            BATTEL_SIDE_ATT => []
        ];
    }

    static function giveImediatly($Unit) {
        if (LWorldUnit::isRepelCastle($Unit["ut"])) return false;
        if (LWorldUnit::isQueenCity($Unit["ut"])) return false;
        return true;
    }

    private function getCityAvailRes() {

        $this->cityAvailResources = selectFromTable(
                        "id_city,food,wood,stone,metal,coin, id_player,id_city,food_cap,wood_cap, stone_cap,metal_cap,coin_cap",
                        "city", "x = :x AND y = :y", ["x" => $this->Unit["x"], "y" => $this->Unit["y"]])[0];
    }

    private function takeCityRes() {
        LSaveState::saveCityState($this->cityAvailResources["id_city"]);
        $food = max($this->cityAvailResources["food"] - $this->totalResCap * static::$CITY_RES_TAKE_RATE["food"], 0);
        $wood = max($this->cityAvailResources["wood"] - $this->totalResCap * static::$CITY_RES_TAKE_RATE["wood"], 0);
        $stone = max($this->cityAvailResources["stone"] - $this->totalResCap * static::$CITY_RES_TAKE_RATE["stone"], 0);
        $metal = max($this->cityAvailResources["metal"] - $this->totalResCap * static::$CITY_RES_TAKE_RATE["metal"], 0);
        $coin = max($this->cityAvailResources["coin"] - $this->totalResCap * static::$CITY_RES_TAKE_RATE["coin"], 0);
        updateTable("food = :f, wood = :w, stone = :s, metal = :m, coin = :c", "city", "id_city = :idc", ["f" => $food, "w" => $wood, "s" => $stone, "m" => $metal, "c" => $coin, "idc" => $this->cityAvailResources["id_city"]]);
    }

    function heroShare(LFight &$Fight) {

        foreach ($Fight->Heros as $oneHero) {
            if (!isset($this->PlayerCities[$oneHero["side"]][$oneHero["id_player"]]))
                    $this->PlayerCities[$oneHero["side"]][$oneHero["id_player"]] = [];

            if (!isset($this->PlayerCities[$oneHero["side"]][$oneHero["id_player"]][$oneHero["id_city"]]))
                    $this->PlayerCities[$oneHero["side"]][$oneHero["id_player"]][$oneHero["id_city"]] = CPlayer::$PlayerCityPrizeEmpty;

            $this->PlayerCities[$oneHero["side"]][$oneHero["id_player"]][$oneHero["id_city"]]["Killed"] += $oneHero["troopsKilled"];
            $this->PlayerCities[$oneHero["side"]][$oneHero["id_player"]][$oneHero["id_city"]]["Kills"]  += $oneHero["troopsKills"];
            $this->PlayerCities[$oneHero["side"]][$oneHero["id_player"]][$oneHero["id_city"]]["idCity"]  = $oneHero["id_city"];


            foreach ($oneHero["type"] as $key => $val) {
                $this->PlayerCities[$oneHero["side"]][$oneHero["id_player"]][$oneHero["id_city"]]["armySize"] += CArmy::$ArmyCap[$val] * $oneHero["pre"][$key];
                $this->PlayerCities[$oneHero["side"]][$oneHero["id_player"]][$oneHero["id_city"]]["armyRemainSize"] += CArmy::$ArmyCap[$val] * max($oneHero["pre"][$key] - $oneHero["post"][$key], 0);
                $this->PlayerCities[$oneHero["side"]][$oneHero["id_player"]][$oneHero["id_city"]]["Troops"][$val] += $oneHero["pre"][$key];
                $this->PlayerCities[$oneHero["side"]][$oneHero["id_player"]][$oneHero["id_city"]]["RemainTroops"][$val] += max($oneHero["pre"][$key] - $oneHero["post"][$key], 0);
                $this->PlayerCities[$oneHero["side"]][$oneHero["id_player"]][$oneHero["id_city"]]["ResourceCap"] += CArmy::$ArmyPower[$val]["res_cap"] * max($oneHero["pre"][$key] - $oneHero["post"][$key], 0);
            }
        }

        foreach ($this->PlayerCities[BATTEL_SIDE_ATT] as $player) {
            foreach ($player as $City) {
                $this->totalResCap += $City["ResourceCap"];
            }
        }
    }

    public function cityPrize($idPlayer) {    /// دى جوايز المناورات

        $p_prize = ["food" => 0, "wood" => 0, "stone" => 0, "metal" => 0, "coin" => 0];

        $share = min($this->totalResCap / ($this->cityAvailResources["food"] + $this->cityAvailResources["wood"] + $this->cityAvailResources["stone"] + $this->cityAvailResources["metal"] + $this->cityAvailResources["coin"]), 1);
        if (isset($this->PlayerCities[BATTEL_SIDE_ATT][$idPlayer]))
                foreach ($this->PlayerCities[BATTEL_SIDE_ATT][$idPlayer] as $city) {

                $idCity = $city["idCity"];
                $cityShareRate = $city["ResourceCap"] / $this->totalResCap;

                LSaveState::saveCityState($idCity);

                $food = $this->cityAvailResources["food"] * static::$CITY_RES_TAKE_RATE["food"] * $cityShareRate * $share;
                $wood = $this->cityAvailResources["wood"] * static::$CITY_RES_TAKE_RATE["wood"] * $cityShareRate * $share;
                $stone = $this->cityAvailResources["stone"] * static::$CITY_RES_TAKE_RATE["stone"] * $cityShareRate * $share;
                $metal = $this->cityAvailResources["metal"] * static::$CITY_RES_TAKE_RATE["metal"] * $cityShareRate * $share;
                $coin = $this->cityAvailResources["coin"] * static::$CITY_RES_TAKE_RATE["coin"] * $cityShareRate * $share;

                updateTable("food = food + $food, wood = wood + $wood, stone = stone + $stone, metal = metal + $metal , coin = coin + $coin", "city", "id_city =  $idCity");

                $p_prize["food"] += $food;
                $p_prize["wood"] += $wood;
                $p_prize["stone"] += $stone;
                $p_prize["metal"] += $metal;
                $p_prize["coin"] += $coin;
                
            } 

        return ["food" => $p_prize["food"], "wood" => $p_prize["wood"], "stone" => $p_prize["stone"], "metal" => $p_prize["metal"], "coin" => $p_prize["coin"]];
    }

    private function cityItemsPrize($idPlayer) {
        if ($this->totalResCap < 5e6) {
            return [];
        }

        if (!isset($this->PlayerCities[BATTEL_SIDE_ATT][$idPlayer])) return [];
        $playerTotal = 0;

        foreach ($this->PlayerCities[BATTEL_SIDE_ATT][$idPlayer] as $oneCity) {
            $playerTotal += $oneCity["ResourceCap"];
        }

        $amountToTake = floor(min($playerTotal / 50e6, ITEM_MAX_TAKE));
        $items = selectFromTable("*", "world_unit_prize", "unitType = :t ORDER BY RAND() LIMIT $amountToTake", ["t" => $this->Unit["ut"]]);


        $playerShare = $playerTotal / $this->totalResCap;
        $ItemPrize = [];
        foreach ($items as $key => $oneItem) {

            $canTakeAmount = floor(rand($oneItem["amount_min"], $oneItem["amount_max"]) * $playerShare);
            $playerAmount = LItem::getAmount($oneItem["prize"], $this->cityAvailResources["id_player"]);

            $amount = min($playerAmount, $canTakeAmount);
            if ($amount < 1) {
                continue;
            }
            
            LItem::useItem($oneItem["prize"], $amount, $this->cityAvailResources["id_player"]);

            $items[$key]["amount_min"] = $amount;
            $items[$key]["amount_max"] = $amount;
            $ItemPrize[] = [
                "prize"       => $oneItem["prize"],
                "amount_min" => $amount,
                "amount_max" => $amount,
                "win_rate"   => 1000
            ];
        }

        return $ItemPrize;
    }

    /* this fuction arrange call inside */

    public function givePrize(&$Player) {
        $prizes = CPlayer::$PrizeEmpty;
        
        if (LWorldUnit::isArmyCapital($this->Unit["ut"])) return;


        if (LWorldUnit::isCity($this->Unit["ut"])) {


            $res = $this->cityPrize($Player["idPlayer"]);
            $prizes["ItemPrize"] = $this->cityItemsPrize($Player["idPlayer"]);
            $prizes["ResourcePrize"] = [
                "food" => $res["food"], "wood" => $res["wood"],
                "stone" => $res["stone"], "metal" => $res["metal"],
                "coin" => $res["coin"]
            ];
            $this->addPrizeToDB($prizes, $Player);
            
            return;
        }

        $prizes["ItemPrize"] = selectFromTable("prize, amount_max, amount_min, win_rate, mat_tab", "world_unit_prize", "unitType = {$this->Unit["ut"]} AND lvl = {$this->Unit["l"]} ORDER BY RAND()");



        $this->addPrizeToDB($prizes, $Player);
    }

    public function addPrizeToDB($prizes, &$Player) {

        if (!isset($Player["ItemPrize"])) $Player["ItemPrize"] = [];

        foreach ($prizes["ItemPrize"] as $one) {

            $luck = rand(0, 1000);
            if ($luck <= $one["win_rate"]) {
                $amount = rand($one["amount_min"], $one["amount_max"]);
                LItem::addItem($one["prize"], $amount, $Player["idPlayer"]);
                $Player["ItemPrize"][] = [
                    "Item" => $one["prize"],
                    "amount" => $amount
                ];
            }
        }
        
        $Player["ResourcePrize"] = $prizes["ResourcePrize"];
        

        updateTable("honor = honor + :h", "player", "id_player = :idp", ["idp" => $Player["idPlayer"], "h" => $Player["Honor"]]);
    }

}
