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
                    <?= LCPBase::getTabs("GodGate") ?>  
                </ul>
            </div>
            <div id="middle">
                <div id="left-column">
                    <h3>محتوبات الصندوق</h3> 
                    <div id="item-prize-list">
                        <ul id="player-list" style="padding: 0px;">

                        </ul>
                    </div>

                </div>
                <div id="center-column">
                    <br />
                    <div class="select-bar">
                        <select id="select-god-gate">
                            <option value="god_gate_point_plus_1">البوبة 1</option>
                            <option value="god_gate_point_plus_2">البوبة 2</option>
                            <option value="god_gate_point_plus_3">البوبة 3</option>
                            <option value="god_gate_point_plus_4">البوبة 4</option>
                        </select>
                        <label>البويات</label>

                    </div>
                    <div class="table" style="margin-bottom: 0px;"> <img src="img/bg-th-left.gif" width="8" height="7" alt="" class="left" /> <img src="img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
                        <div class="listing" style="text-align: center">
                            <div class="th" style="background: #9097A9 url(../img/bg-th-left.gif) no-repeat left top;">
                                <div class="tr" style="width: 30%; display: inline-block; ">تفاصيل المادة</div>
                                <div class="tr" style="width: 68%; display: inline-block">المواد</div>
                            </div>
                            <div class="content" style="overflow: auto">
                                <div id="rank-plus-point" style="width: 70%; float:  right; ">
                                    <ul style="font-size: 16px"></ul>
                                </div>
                                <div id="other-list" style="width: 30%">
                                    <div id="GodGateGeneralData" >
                                        
                                    </div>
                                    <br> <br> <br>
                                    <div>
                                        <button id="SaveGodGateGenralData">حفظ المتطلبات</button>
                                    </div>
                                    <br> <br> <br>
                                    <div id="GodGateMaxPoint" >
                                        
                                    </div>
                                    <br> <br> <br>
                                    <div>
                                        <button id="SaveGodGateMaxPoint">حفظ أقصى قوة</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="table" style="margin-bottom: 0px; height: 320px">
                        <img src="img/bg-th-left.gif" width="8" height="7" alt="" class="left" /> <img src="img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
                        <div class="listing" style="text-align: center">
                            <div class="th" style="background: #9097A9 url(../img/bg-th-left.gif) no-repeat left top;">
                                <div class="tr" style="width: 100%; display: inline-block; ">الجائزة</div>

                            </div>
                            <div id="item-box-reword" style="width: 100%;  height: 280px;overflow: auto;"> </div>
                        </div>

                    </div>
                    <hr style="display: block;width: 100%;float: none;clear: both;">
                    <div id="SaveItemProp">
                        <button style="display: block; margin: auto; width: 120px; height: 36px; margin-bottom: 15px;">حفظ المادة</button>
                    </div>
                </div>
                <div id="right-column"> 
                    <h3>ترتيب</h3> 
                    <div style="width: 142px; margin-top: 15px;"> 
                        <table id="user-table" class="listing" style="width: 100%;" cellspacing="0" cellpadding="0">
                            <tbody>
                                <tr>
                                    <th class="first" style="text-align: center">ترتيب</th>
                                </tr>                
                            </tbody>
                        </table>

                    </div>
                </div>
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
        <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/GodGate.js"></script>
    </body>
</html>
