<!DOCTYPE html>
<html>
    <head>
        <title>CMS Admin</title>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" href="<?=RESOURCE_BATH?>/css/all.css"/>
    </head>
    <body>
        <div id="main">
            <div id="header"> <a href="#" class="logo"><img src="../images/Logo-wow.png" width="101" height="29" alt="" /></a>
                <ul id="top-navigation">
                    <?= LCPBase::getTabs("World")?>
                </ul>
            </div>
            <div id="middle">
                <div id="left-column">
                    <h3>Header</h3> </div>
                <div id="center-column">

                    <br />
                    <div class="select-bar">
                        <select id="select-grou-to-send">
                            <option value="player">ارسال الى لاعب</option>
                            <option  value="guild">ارسال الى حلف</option>
                            <option value="server">رسال الى السيرفر </option>
                            <option value="group"> ارسال الى مجموعة معينة</option>
                            <option value="online"> ارسال الى المتصلين</option>
                        </select>

                        <select id="select-prize-type-to-send">
                            <option value="matrial">مواد</option>
                            <option value="equip">معدات</option>
                            <option value="resource">موارد</option>
                        </select>
                    </div>

                    <div class="table" style="margin-bottom: 0px;"> <img src="img/bg-th-left.gif" width="8" height="7" alt="" class="left" /> <img src="img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
                        <div class="listing" style="text-align: center">
                            <div class="th" style="background: #9097A9 url(../img/bg-th-left.gif) no-repeat left top;">
                                <div class="tr" style="width: 30%; display: inline-block; ">قائمة اللاعبين</div>
                                <div class="tr" style="width: 68%; display: inline-block">المواد</div>
                            </div>
                            <div class="content" style="overflow: auto">
                                <div id="matrial-list">

                                </div>

                                <div id="player-list">
                                    <label style="margin-top:15px; display: block ">
                                        <input id="search-val" type="text" name="textfield" placeholder="أدخل اسم اللاعب " />
                                    </label>
                                    <label>
                                        <input id="start-search-player-prize" type="submit" name="Submit" value="Search" />
                                    </label>

                                    <div id="search-result">
                                        <ul>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="table" style="margin-bottom: 0px; height: 180px"> <img src="img/bg-th-left.gif" width="8" height="7" alt="" class="left" /> <img src="img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
                        <div class="listing" style="text-align: center">
                            <div class="th" style="background: #9097A9 url(../img/bg-th-left.gif) no-repeat left top;">
                                <div class="tr" style="width: 30%; display: inline-block; ">قائمة اللاعبين</div>
                                <div class="tr" style="width: 68%; display: inline-block">المواد</div>

                            </div>
                            <div id="matrial-to-send" style="width: 70%;  float: right;height: 162px;overflow: auto;">

                            </div>
                            <div id="player-to-send" style="width: 30%;  float: left;height: 162px;overflow: auto;">
                                <ul>

                                </ul>
                            </div>
                        </div>

                    </div>
                    <hr style="display: block;width: 100%;float: none;clear: both;">

                    <div id="SEND_MATRIAL">
                        <button data-send-to="player" style="display: block; margin: auto; width: 120px; height: 36px; margin-bottom: 15px;"> ارسال المواد</button>
                    </div>


                </div>
                <div id="right-column"></div>
            </div>
            <div id="footer"></div>
        </div>
        <script>
            var BASE_URL = "<?=BASE_URL?>";
            var SERVER_ID = <?=$_GET["server"]?> ;
            var SERVER_LIST = <?= json_encode(array_combine(array_keys($ServerList), array_column($ServerList, "name")), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)?>;
            var OuthToken   = '<?=$_GET["AdminToken"]?>';
            var WS_HOST    = '<?=WEB_SOCKET_HOST?>';
            var WS_PORT    = '<?=$ServerList[$_GET["server"]]["Port"]?>';
        </script>
        <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/base.js"></script>
        <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/Player.js"></script>
        <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/SendPrize.js"></script>
        <script>
            var PRIZE_TO_SEND = [];
            var PLYAERS_TO_SEND = [];

        </script>

    </body>
</html>
