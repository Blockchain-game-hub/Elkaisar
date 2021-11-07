<!DOCTYPE html>
<html>
    <head>
        <title>CMS Admin</title>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" href="<?= RESOURCE_BATH ?>/css/all.css"/>
    </head>
    <body>
        <div id="main">
            <div id="header"> <a href="#" class="logo"><img src="../images/Logo-wow.png" width="101" height="29" alt="" /></a>
                <ul id="top-navigation">
                    <?= LCPBase::getTabs("SendPrizeHis") ?>
                </ul>
            </div>
            <div id="middle">
                <div id="left-column">
                    <h3>Header</h3> </div>
                <div id="center-column">

                    <table id="ShowSendPrizeHis" class="listing" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <th class="first" width="50"> اللاعب</th>
                                <th>جوئز</th>
                                <th class="last">تاريخ</th>
                            </tr>
                            <tr data-id-user="21">
                                <td class="first">- troymasry </td>
                                <td class="change-pass">
                                    <div class="matrial-unit update-player-matrial" data-matrial="black_ore" data-matrial-table="matrial_main" data-amount="8" prize-type="matrial" style="width: 33%; height:35px;">
                                        <img src="../images/items/Ore.black.jpg">
                                        <div class="amount" style="margin-top: -10px; font-size:10px">8</div>
                                    </div>
                                </td>
                                <td>2020-10-03 09:51:01</td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="SendPrizeHisLis"></div>
                </div>
                <div id="right-column"></div>
            </div>
            <div id="footer">

            </div>
        </div>
        <script>
            var BASE_URL = "<?= BASE_URL ?>";
            var SERVER_ID = <?= $_GET["server"] ?>;
            var SERVER_LIST = <?= json_encode(array_combine(array_keys($ServerList), array_column($ServerList, "name")), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?>;
            var OuthToken = '<?= $_GET["AdminToken"] ?>';
            var WS_HOST    = '<?=WEB_SOCKET_HOST?>';
            var WS_PORT    = '<?=$ServerList[$_GET["server"]]["Port"]?>';
        </script>
        <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/base.js"></script>
        <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/Player.js"></script>
        <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/SendPrizeHis.js"></script>
        <script>
            var PRIZE_TO_SEND = [];
            var PLYAERS_TO_SEND = [];

        </script>

    </body>
</html>
