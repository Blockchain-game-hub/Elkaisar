<?php

class AMarket
{
    
    function marketTrans(){
        
        $now = time() + 1;
        $allTransmit = selectFromTable("*", "market_transport", "time_arrive <= $now");
        deleteTable("market_transport_back", "time_arrive <= $now");
        $Players = [];
        
        foreach ($allTransmit as $oneTransit)
        {
            
            $cityFrom = selectFromTable("city.x, city.y , city.name , city.id_player, player.porm", "city JOIN player ON player.id_player = city.id_player", "city.id_city = {$oneTransit["id_city_from"]}")[0];
            $cityTo   = selectFromTable("city.x, city.y, city.id_player, player.porm", "city JOIN player ON player.id_player = city.id_player", "city.id_city = {$oneTransit["id_city_to"]}")[0];
            $this->backTrans($oneTransit, $cityFrom, $cityTo);
            $loseRatio = 0;
            
            if($cityFrom["porm"] < $cityTo["porm"])
                $loseRatio = ($cityTo["porm"] - $cityFrom["porm"])*0.03;
            
            $loseRatio = 0;
            
            $fl = ($oneTransit["food"] *$loseRatio); $wl = ($oneTransit["wood"] *$loseRatio); $sl = ($oneTransit["stone"]*$loseRatio); $ml = ($oneTransit["metal"]*$loseRatio); $cl = ($oneTransit["coin"] *$loseRatio);

            $fs = $oneTransit["food"] - $fl; $ws = $oneTransit["wood"] - $wl; $ss = $oneTransit["stone"]- $sl; $ms = $oneTransit["metal"]- $ml; $cs = $oneTransit["coin"] - $cl;
            
            
            $body  = '<div class="req_table_msg"><table class="req_table x-2" border="0"><tbody><tr><td><img src="images/style/food.png"> 
                    <div class="amount sol-food">'.$oneTransit["food"].'</div></td><td><img src="images/style/stone.png"><div class="amount sol-stone">'.$oneTransit["stone"].' 
                    </div></td></tr><tr><td><img src="images/style/wood.png"><div class="amount sol-wood">'.$oneTransit["wood"].'</div></td><td><img src="images/style/iron.png"> 
                    <div class="amount sol-metal">'.$oneTransit["metal"].'</div></td></tr><tr><td><img src="images/style/coin.png"><div class="amount sol-coin">'.$oneTransit["coin"].' 
                    </div></td><td></td></tr></tbody></table></div><div class="desc-in-msg">تم نقل الموارد من المدينة '.$cityFrom["name"].' الى المدينة ['.$cityFrom["y"].' ,'.$cityFrom["x"].'] وتم فقد خلال هذة العملية
                    </div><div id="city-resource"><ul>
                    <li><img src="images/style/food.png"><label>'.$fl.'</label></li>
                    <li><img src="images/style/wood.png"><label>'.$wl.'</label></li>
                    <li><img src="images/style/stone.png"><label>'.$sl.'</label></li>
                    <li><img src="images/style/iron.png"><label>'.$ml.'</label></li>
                    <li><img src="images/style/coin.png"><label>'.$cl.'</label></li>
                    </ul></div>';
            
            LSaveState::saveCityState($oneTransit["id_city_to"]);

            $quray = "food = food + {$fs} , wood = wood + {$ws} , metal = metal + {$ms} , coin = coin + {$cs} , stone = stone + {$ss}";
            
            updateTable($quray, "city", "id_city = {$oneTransit['id_city_to']}");
            deleteTable("market_transport", "id_trans = {$oneTransit["id_trans"]}");
            
            insertIntoTable("id_to = {$cityFrom["id_player"]} , head = 'تقرير وصول الموارد'  , body = '$body' , time_stamp = $now ", "msg_diff");
            insertIntoTable("id_to = {$cityTo["id_player"]}  , head = 'تقرير وصول الموارد'  , body = '$body' , time_stamp = $now ", "msg_diff");
            
            $Players[] = ["idPlayer" =>$cityFrom["id_player"], "idCity" => $oneTransit["id_city_from"]];
            $Players[] = ["idPlayer" =>$cityTo["id_player"], "idCity" => $oneTransit["id_city_to"]];
        }
        return $Players;
    }
    
    
    private function backTrans($oneTransit, $cityFrom, $cityTo)
    {
        
        $distance = LWorldUnit::calDist($cityFrom["x"], $cityTo["x"], $cityFrom["y"], $cityTo["y"]);
        $time_arrive  = time() + $distance/300;
        insertIntoTable("id_city_to = {$oneTransit["id_city_from"]} , id_city_from  ={$oneTransit["id_city_to"]}, time_arrive = $time_arrive", "market_transport_back");
    }
    
    
    function marketBuyTrans()
    {
        $Players = [];
        $now = time() - 1;
        $all_transmit = selectFromTable("*", "market_buy_transmit", "time_arrive <= $now");

        foreach ($all_transmit as $one){

            LSaveState::saveCityState($one["id_city_to"]);
            
            deleteTable("market_buy_transmit", "id_deal = {$one["id_deal"]}");
            updateTable("{$one["resource"]} = {$one["resource"]} + {$one["amount"]}", "city", "id_city = {$one["id_city_to"]}");

            $Players[] = [
                "idPlayer" => $one["id_player_to"],
                "idCity"   => $one["id_city_to"]
            ];
        }

        return $Players;
    }
}