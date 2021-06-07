
<!DOCTYPE html>
<html>
    <head>
        <title>Elkaisar CP</title>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" href="<?= RESOURCE_BATH ?>/css/all.css"/>
    </head>
    <body>

        <div id="main">
            <div id="header"> <a href="#" class="logo"><img src="../images/Logo-wow.png" width="101" height="29" alt="" /></a>
                <ul id="top-navigation">
                    <?= LCPBase::getTabs("Ip") ?>  
                </ul>
            </div>
            <div id="middle">
                <div id="left-column">
                    <h3>Header</h3> </div>
                <div id="center-column">

                    <br />
                    <div class="select-bar">
                        <label>
                            <input id="SearchOnePlayerValIp" type="text" name="textfield" />
                        </label>
                        <label>
                            <input id="start-search" type="submit" name="Submit" value="Search" />
                        </label>
                        <div id="drop-down">
                            <ul>

                            </ul>
                        </div>
                    </div>
                    <div class="table"> <img src="img/bg-th-left.gif" width="8" height="7" alt="" class="left" /> <img src="img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
                        <table id="showTable" class="listing" cellpadding="0" cellspacing="0">
                            <tr>
                                <th class="first" width="177">اللاعب</th>
                                <th>الرتية</th>
                                <th>البلد</th>
                                <th>ip</th>
                                <th>تاريخ</th>
                                <th>عدد مرات</th>
                                <th class="last">فحص ip</th>
                            </tr>
                            <tr data-id-user="54">
                                <td class="how-player-gold">TEST1</td>
                                <td class="">مصر</td>
                                <td class="first ">قيصر</td>
                                <td class="change-pass"><a target="_blank" href="https://whatismyipaddress.com/ip/177.200.48.195">177.200.48.195</a></td>
                                <td class="show-box-mat">Invalid Date</td>
                                <td class="show-box-mat">1</td>
                                <td class="see-ip last"><img src="img/add-icon.gif" alt="add" class="checkThisIp" data-id-player="54" width="16" height="16"></td>
                            </tr>
                        </table>
                    </div>
                    <div id="search-result"></div>
                </div>
                <div id="right-column"><?= LCPBase::getMenu()?></div>
            </div>
            <div id="footer"></div>
        </div>
        <script>
            var BASE_URL = "<?= BASE_URL ?>";
            var SERVER_ID = <?= $_GET["server"] ?>;
            var SERVER_LIST = <?= json_encode(array_combine(array_keys($ServerList), array_column($ServerList, "name")), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?>;
            var OuthToken = '<?= $_GET["AdminToken"] ?>';
            var WS_HOST = '<?= WEB_SOCKET_HOST ?>';
            var WS_PORT = '<?= $ServerList[$_GET["server"]]["Port"] ?>';
        </script>
        <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/base.js"></script>
        <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/Ip.js"></script>

    </body>
</html>
