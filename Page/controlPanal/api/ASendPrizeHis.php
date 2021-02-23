<?php

class ASendPrizeHis {

    function getPageCount() {
        return [
            "state" => "ok",
            "Count" => ceil(selectFromTable("COUNT(*) AS C", "cp_send_p_his", "1")[0]["C"]/10)
        ];
    }

    function getPage(){
        
        $Offset = validateID($_GET["offset"])*10;
        
        return selectFromTable("cp_send_p_his.*, player.name", "cp_send_p_his JOIN player ON player.id_player = cp_send_p_his.id_player" , "1 ORDER BY time_stamp DESC LIMIT 10 OFFSET $Offset");
        
    }
}
