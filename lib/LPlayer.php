<?php

class LPlayer {

    static function tekeGold($amount) {
        global $idPlayer;
        if ($amount <= 0) return false;

        if (!static::haveEnoughGold($amount)) return false;
        updateTable("gold = gold - :a", "player", "id_player = :idp", ["idp" => $idPlayer, "a" => $amount]);

        return true;
    }

    static function haveEnoughGold($amount) {
        if ($amount <= 0) return false;

        global $idPlayer;
        $gold = selectFromTable("gold", "player", "id_player = :idp", ["idp" => $idPlayer]);
        if (!count($gold)) return false;

        return ($gold[0]["gold"] >= $amount);
    }

    static function addPrestige($amount) {
        global $idPlayer;
        updateTable("prestige = prestige + :a", "player", "id_player = :idp", ["idp" => $idPlayer, "a" => $amount]);
    }

    static function getName($idPlayer) {
        return selectFromTable("name", "player", "id_player = :idp", ["idp" => $idPlayer])[0]["name"];
    }

    static function getData() {
        global $idPlayer;

        $player = selectFromTable(
                        "*",
                        "(SELECT player.*, @row:=@row+1 as 'rank' FROM player,(SELECT @row:=0) r ORDER BY player.prestige DESC ) AS col ",
                        "col.id_player = :idp", ["idp" => $idPlayer])[0];
        unset($player["p_token"]);
        return $player;
    }

    static function OnPlayerLogged() {
        global $idPlayer;
        $City = selectFromTable("*", "city", "id_player = :idp", ["idp" => $idPlayer]);
        foreach ($City as $one) {
            updateTable("pop_max = :pm", "city", "id_city = :idc", ["idc" => $one["id_city"], "pm" => LCity::maxPop($one)]);
        }

        updateTable("power_max = LEAST(150, lvl + 50)", "hero", "id_player = :idp", ["idp" => $idPlayer]);
    }

    static function PlayerGuildInvReq() {
        global $idPlayer;
        return[
            "GuildReq" => selectFromTable(
                    "guild.name, guild.slog_top, guild.slog_cnt, guild.slog_btm, guild_req.id_guild",
                    "guild_req JOIN guild ON guild_req.id_guild = guild.id_guild", "guild_req.id_player = :idp", ["idp" => $idPlayer]),
            "GuildInv" => selectFromTable(
                    "guild.name, guild.slog_top, guild.slog_cnt, guild.slog_btm, guild_inv.id_guild",
                    "guild_inv JOIN guild ON guild_inv.id_guild = guild.id_guild", "guild_inv.id_player = :idp", ["idp" => $idPlayer]),
        ];
    }

    static function giveNewCommerPrize($idPlayer) {
        $PrizeList = selectFromTable("*", "new_commer_prize", "1");
        foreach ($PrizeList as $onePrize) {
            if($onePrize["prize_type"] == "item"){
                LItem::addItem($onePrize["prize"], $onePrize["amount"], $idPlayer);
            }else if($onePrize["prize_type"] == "equip"){
                $Equip = explode(".", $onePrize["prize"]);
                LEquip::addEquip($Equip[0], $Equip[1], $Equip[2]);
            }
        }
    }

}
