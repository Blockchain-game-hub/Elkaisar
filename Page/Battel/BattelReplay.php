<?php
$id_report = validateId(BATTEL_REPLAY_ID);
$BattelReplay = BRselectFromTable("*", "battel_replay", "id_battel_char = :idr", ["idr" => $id_report]);
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>

        <title>استعراض المعارك</title>
        <meta id="" name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=0.5, minimum-scale=0.5, width=device-width,height=device-height,target-densitydpi=device-dpi, user-scalable=yes">

        <link rel="stylesheet" href="<?= RESOURCE_BATH ?>/css/Base.css"/>
    </head>
    <body>
        <div id="BloodBar">
            <div class="bar"></div>
        </div>
        <div id="BattelReplayCanvas"></div>
        <div id="PlayControlBoard">
            <div class="SideContainer">
                <div class="PlayerImage" style="background-image: url(<?= RESOURCE_BATH ?>/images/ui/battleSituation_attack.png)"></div>
            </div>
            <div class="bordContainer">
                <div class="HeroImage">
                    <div class="image">
                        <div class="lable"></div>
                    </div>
                    <div class="name TroopsOverNum">Hero Name</div>
                </div>
                <div class="BattelDesc"></div>
                <div class="HeroImage">
                    <div class="image">
                        <div class="lable"></div>
                    </div>
                    <div class="name TroopsOverNum">Hero Name</div>
                </div>
            </div>
            <div class="SideContainer">
                <div class="PlayerImage" style="background-image: url(<?= RESOURCE_BATH ?>/images/ui/battleSituation_defense.png)"></div>
            </div>
        </div>
    </body>


    <script>
        const BattelID = 'BATTEL_REPLAY_ID';
        const BattelReplayData = `<?= $BattelReplay[0]["battel_replay"]; ?>`;
        const BASE_ASSET_BATH = `<?= RESOURCE_BATH ?>`;
    </script>
    <script src="<?= RESOURCE_BATH ?>/js/lib/jquery-3.2.1.min.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/lib/phaser.min.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/battelReplay/Base.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/battelReplay/PreLoad.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/battelReplay/Hero.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/battelReplay/Round.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/battelReplay/BattelReplay.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/battelReplay/Start.js"></script>

</html>