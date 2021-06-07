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

                    <?= LCPBase::getTabs("PlayerState") ?>  
                </ul>
            </div>

            <div id="middle">
                <div id="center-column" style="width: 90%; padding: 0px; background: none; margin: auto; float: none">
                    <br />
                    <div class="select-bar">
                        <label>
                            <input id="SearchOnePlayerVal" type="text" name="textfield">
                        </label>
                        <label>
                            <input id="SearchOnePlayer" type="submit" name="Submit" value="Search">
                        </label>
                        <div id="drop-down">
                            <ul>

                            </ul>
                        </div>
                    </div>

                    <div class="table" style="width: 100%"> 
                        <div class="listing" style="text-align: center; width: 100%">
                            <table id="playerCityResource" class="listing" cellspacing="0" cellpadding="0" style="width: 100%">
                                <tbody>
                                    <tr>
                                        <th class="first"> اللاعب</th>
                                        <th>غذاء</th>
                                        <th>اخشاب</th>
                                        <th>احجار</th>
                                        <th>حديد</th>
                                        <th>عملات</th>
                                        <th>ذهب</th>
                                        <th class="last">فحص</th>
                                    </tr>
                                </tbody>
                            </table>

                            <table id="playerCityArmy" class="listing" cellspacing="0" cellpadding="0"  style="width: 100%">
                                <tr>
                                    <th class="first"> اللاعب</th>
                                    <th>مشاة</th>
                                    <th>فرسان</th>
                                    <th>مدرعين</th>
                                    <th>رماة</th>
                                    <th>مقاليع</th>
                                    <th>منجنيق</th>
                                    <th>جواسيس</th>
                                    <th class="last">فحص</th>
                                </tr>
                            </table>
                            <div class="th" style="background: #9097A9 url(../img/bg-th-left.gif) no-repeat left top;">
                                <div class="tr" style=" ">قائمة المواد</div>
                            </div>
                            <div class="content" style="overflow: auto">
                                <div id="playerMatrialList">

                                </div>
                            </div>
                            <div class="th" style="background: #9097A9 url(../img/bg-th-left.gif) no-repeat left top;">
                                <div class="tr" style=" ">قائمة المعدات</div>
                            </div>
                            <div class="content" style="overflow: auto">
                                <div id="playerEquipList">

                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="table" style="margin-bottom: 0px; width: 100%"> 
                        <div class="listing" style="text-align: center">
                            <div class="th" style="background: #9097A9 url(../img/bg-th-left.gif) no-repeat left top;">
                                <div class="tr">الابطال</div>

                            </div>
                            <div id="playerHeros1" style="">
                                <ul></ul>
                            </div>
                            <hr style="display: block;width: 100%;float: none;clear: both;">
                            <div id="playerHeros2" style="">
                                <ul> </ul>
                            </div>
                            <hr style="display: block;width: 100%;float: none;clear: both;">
                            <div id="playerHeros3" style="">
                                <ul></ul>
                            </div>
                            <hr style="display: block;width: 100%;float: none;clear: both;">
                            <div id="playerHeros4" style="">
                                <ul> </ul>
                            </div>
                            <hr style="display: block;width: 100%;float: none;clear: both;">
                            <div id="playerHeros5" style=" display: block">
                                <ul>    </ul>
                            </div>

                        </div>

                    </div>

                </div>
            </div>

            <div id="footer"></div>
        </div>

        
    </body>
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
    <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/OnePlayer.js"></script>
</html>
