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

        <link rel="stylesheet" href="<?= RESOURCE_BATH ?>/css/reset-min.css"/>
        <link rel="stylesheet" href="<?= RESOURCE_BATH ?>/css/fonts-min.css"/>
        <link rel="stylesheet" href="<?= RESOURCE_BATH ?>/css/Base.css"/>
        <link rel="stylesheet" href="<?= RESOURCE_BATH ?>/css/battelReview.css"/>
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
                <div id="ControlBtns">
                    <div class="con-btns-wrapper">
                        <div class="btn-list">
                            <ul>
                                <li class="prev-round">
                                    <button></button>
                                </li>
                                <li class="play-round">
                                    <button></button>
                                </li>
                                <li class="next-round">
                                    <button></button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="DescBox">
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
            </div>
            <div class="SideContainer">
                <div class="PlayerImage" style="background-image: url(<?= RESOURCE_BATH ?>/images/ui/battleSituation_defense.png)"></div>
            </div>
        </div>
        <div id="RightSideData">
            <div class="city-health">

            </div>
            <div class="show-battel-report">
                <div class="btn-wrapper">
                    <button id="ShowBattelReport">إظهار التقرير</button>
                </div>
            </div>
        </div>




    </body>


    <script>
        const BattelID = 'BATTEL_REPLAY_ID';
        const BattelReplayData = {
            Rounds:  <?= gzdecode(base64_decode($BattelReplay[0]["battel_replay"])); ?>,
            Players: <?= gzdecode(base64_decode($BattelReplay[0]["battel_report_players"])); ?>,
            Heros:   <?= gzdecode(base64_decode($BattelReplay[0]["battel_report_heros"])); ?>,
            Battel:  <?= gzdecode(base64_decode($BattelReplay[0]["battel_report_data"])); ?>
        };
        const BASE_ASSET_BATH = `<?= RESOURCE_BATH ?>`;
        const JSVersion = `<?= JS_VERSION ?>`;
    </script>
    <script src="<?= RESOURCE_BATH ?>/js/lib/jquery-3.2.1.min.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/lib/jquery.nicescroll.min.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/lib/phaser.min.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/battelReplay/Base.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/battelReplay/PreLoad.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/battelReplay/Hero.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/battelReplay/Round.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/battelReplay/BattelReplay.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/battelReplay/Start.js"></script>
    <script src="<?= RESOURCE_BATH ?>/js/battelReplay/BattelReport.js"></script>


</html>