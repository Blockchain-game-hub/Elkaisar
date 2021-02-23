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
                    <?= LCPBase::getTabs("Exchange") ?>  
                </ul>
            </div>
            <div id="middle">
                <div id="left-column">
                    <h3>التبادل الحالى</h3> 
                    <div id="current-exchange">
                        <ul style="padding: 0px;">

                        </ul>
                    </div>

                </div>
                <div id="center-column">

                    <br />
                    <div class="select-bar">
                        <select id="select-prize-type">
                            <option value="matrial">مواد</option>
                            <option  value="equip"> معدات</option>
                        </select>
                        <label>
                            نوع الجائزة
                        </label>

                    </div>
                    <div class="table" style="margin-bottom: 0px;"> <img src="img/bg-th-left.gif" width="8" height="7" alt="" class="left" /> <img src="img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
                        <div class="listing" style="text-align: center">
                            <div class="th" style="background: #9097A9 url(../img/bg-th-left.gif) no-repeat left top;">
                                <div class="tr" style="width: 30%; display: inline-block; ">اخرى</div>
                                <div class="tr" style="width: 68%; display: inline-block">المواد</div>
                            </div>
                            <div class="content" style="overflow: auto">
                                <div id="matrial-list">

                                </div>

                                <div id="other-list" style="width: 30%">

                                    <div id="resource-req">
                                        <input id="coin" type="text" name="textfield" placeholder="سسترسس"  style="margin-top: 3px"/>
                                        <input id="food" type="text" name="textfield" placeholder="غذاء"  style="margin-top:3px"/>
                                        <input id="wood" type="text" name="textfield" placeholder="اخشاب"  style="margin-top: 3px"/>
                                        <input id="stone" type="text" name="textfield" placeholder="احجار"  style="margin-top: 3px"/>
                                        <input id="metal" type="text" name="textfield" placeholder="حديد"  style="margin-top: 3px"/>
                                        <input id="gold" type="text" name="textfield" placeholder="ذهب"  style="margin-top: 3px"/>
                                    </div>

                                    <hr/>

                                    <div id="pormotion">
                                        <label style="margin-top:5px; display: block ">الترقية المطلوبة</label>
                                        <select id="required-prom" style="width: 50%; display: block; margin: auto">
                                            <option value="0">مواطن</option>
                                            <option value="1">عريف</option>
                                            <option value="2">رقيب</option>
                                            <option value="3">قسطور</option>
                                            <option value="4">قسطور اعلى</option>
                                            <option value="5">نائب</option>
                                            <option value="6">قاضى</option>
                                            <option value="7">موفد</option>
                                            <option value="8">ديكتاتور</option>
                                            <option value="9">قائد الفيلق الخامس</option>
                                            <option value="10">اقئد الفيلق الرابع</option>
                                            <option value="11">قائد الفيلق الثالث</option>
                                            <option value="12">قائد الفيلق الثانى</option>
                                            <option value="13">قائد الفيلق الاول</option>
                                            <option value="14">لواء</option>
                                            <option value="15">فريق</option>
                                            <option value="16">فريق درجة 1</option>
                                            <option value="17">فريق درجة 2</option>
                                            <option value="18">فريق درجة 3</option>
                                            <option value="19">مارشال</option>
                                            <option value="20">رقيب درجة 9</option>
                                            <option value="21">رقيب درجة 8</option>
                                            <option value="22">رقيب درجة 7</option>
                                            <option value="23">رقيب درجة 6</option>
                                            <option value="24">رقيب درجة 5</option>
                                            <option value="25">رقيب درجة 4</option>
                                            <option value="26">رقيب درجة 3</option>
                                            <option value="27">رقيب درجة 2</option>
                                            <option value="28">رقيب درجة 1</option>
                                            <option value="29">قيصر</option>
                                        </select>
                                    </div>

                                    <hr/>
                                    <div id="player-max">
                                        <label style="margin-top:5px; display: block ">اقصى كمية يمتلكها للاعب</label>
                                        <input type="text" name="textfield" placeholder="اقصى كمية  يمكن امتلاكها "  style="margin-top: 5px"/>
                                    </div>

                                    <hr/>
                                    <div id="player-max-trade">
                                        <label style="margin-top:5px; display: block ">اقصى كمية للتبادل اليومى للاعب </label>
                                        <input type="text" name="textfield" placeholder="أقصى كمية للتبادل اليومى للاعب"  style="margin-top: 5px"/>
                                    </div>

                                    <hr/>
                                    <div id="server-max">
                                        <input type="text" name="textfield" placeholder="اقصى كمية للتبادل للسيرفر"  style="margin-top: 5px"/>
                                    </div>


                                    <hr/>
                                    <div>
                                        <select id="exchange-cat" style="width: 60%">
                                            <option value="trade-daily">الموارد اليومية</option>
                                            <option  value="trade-milli"> خط الانتاج من المجلس الاعلى</option>
                                            <option value="trade-event">هدايا الفاعلياب</option>
                                        </select>
                                    </div>


                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="table" style="margin-bottom: 0px; height: 180px"> <img src="img/bg-th-left.gif" width="8" height="7" alt="" class="left" /> <img src="img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
                        <div class="listing" style="text-align: center">
                            <div class="th" style="background: #9097A9 url(../img/bg-th-left.gif) no-repeat left top;">
                                <div class="tr" style="width: 30%; display: inline-block; ">الجائزة</div>
                                <div class="tr" style="width: 68%; display: inline-block">المتطلبات</div>

                            </div>
                            <div id="exchange-req"  style="width: 70%;  float: right;height: 162px;overflow: auto;">
                                <ul>

                                </ul>
                            </div>
                            <div id="exchange-reword" style="width: 30%;  float: left;height: 162px;overflow: auto;">

                            </div>
                        </div>

                    </div>
                    <hr style="display: block;width: 100%;float: none;clear: both;">

                    <div id="ADD_EXHANGE_UNIT">
                        <button style="display: block; margin: auto; width: 120px; height: 36px; margin-bottom: 15px;">اضف المادة</button>
                    </div>
                    <!--
                    <div class="table"> <img src="img/bg-th-left.gif" width="8" height="7" alt="" class="left" /> <img src="img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
                        <table class="listing form" cellpadding="0" cellspacing="0">
                            <tr>
                                <th class="full" colspan="2">Header Here</th>
                            </tr>
                            <tr>
                                <td class="first" width="172"><strong>Lorem Ipsum</strong></td>
                                <td class="last"><input type="text" class="text" /></td>
                            </tr>
                            <tr class="bg">
                                <td class="first"><strong>Lorem Ipsum</strong></td>
                                <td class="last"><input type="text" class="text" /></td>
                            </tr>
                            <tr>
                                <td class="first"><strong>Lorem Ipsum</strong></td>
                                <td class="last"><input type="text" class="text" /></td>
                            </tr>
                            <tr class="bg">
                                <td class="first"><strong>Lorem Ipsum</strong></td>
                                <td class="last"><input type="text" class="text" /></td>
                            </tr>
                        </table>
                        <p>&nbsp;</p>
                    </div>
                    -->


                </div>
                <div id="right-column"></div>
            </div>
            <div id="footer"></div>
        </div>
       <script>
            var BASE_URL    = "<?= BASE_URL ?>";
            var SERVER_ID   = <?= $_GET["server"] ?>;
            var SERVER_LIST = <?= json_encode(array_combine(array_keys($ServerList), array_column($ServerList, "name")), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?>;
            var OuthToken   = '<?= $_GET["AdminToken"] ?>';
            var WS_HOST     = '<?= WEB_SOCKET_HOST ?>';
            var WS_PORT     = '<?= $ServerList[$_GET["server"]]["Port"] ?>';
        </script>
        <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/base.js"></script>
        <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/Item.js"></script>
        <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/Exchange.js"></script>
    </body>
</html>
