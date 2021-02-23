<!DOCTYPE html>
<html>
    <head>
        <title>Elkaisar CP</title>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" href="<?=RESOURCE_BATH?>/css/all.css"/>
    </head>
    <body>
        <div id="main">
            <div id="header"> <a href="#" class="logo"><img src="../images/Logo-wow.png" width="101" height="29" alt="" /></a>
                <ul id="top-navigation">
                    <?= LCPBase::getTabs("WorldUnitPrize")?>  
                </ul>
            </div>
            <div id="middle">
                <div id="left-column">
                    <h3>Header</h3> </div>
                <div id="center-column">

                    <br />
                    <div class="select-bar">
                        <select id="WORLD_UNIT_TYPE">
                            <option value="0">انهار</option>
                            <option value="1">جبال</option>
                            <option value="2">صحراء</option>
                            <option value="3">غابات</option>
                            <option value="4">مناورات</option>
                            <option value="5">معسكر بريطانى</option>
                            <option value="6">معسكر المانى</option>
                            <option value="7">معسكر اسيوى</option>
                            <option value="8">معسكر فرنسى</option>
                            <option value="9">معسكر مقدونى</option>
                            <option value="10">معسكر اسبانى</option>
                            <option value="11">معسكر ايطالى</option>
                            <option value="12">معسكر فارسى</option>
                            <option value="13">معسكر قرطاجى</option>
                            <option value="14">معسكر مصرى</option>
                            <option value="15">المجموعة الامامية</option>
                            <option value="16">السرية الامامية</option>
                            <option value="17">الجماعة الامامية</option>
                            <option value="18">الكتيبة الامامية</option>
                            <option value="19">فرقة التسليح الخفيف</option>
                            <option value="20">سرية التسليح الخفيف</option>
                            <option value="21">جماعة التسليح الخفيف</option>
                            <option value="22">كتيبة التسليح الخفيف</option>
                            <option value="23">فرقة التسليح الثقيل</option>
                            <option value="24">سرية التسليح الثقيل</option>
                            <option value="25">جماعة التسليح الثقيل</option>
                            <option value="26">كتيبة التسليح الثقيل</option>
                            <option value="27">فرقة  الحراسة</option>
                            <option value="28">سرية  الحراسة</option>
                            <option value="29">جماعة  الحراسة</option>
                            <option value="30">كتيبة  الحراسة</option>
                            <option value="31">الساندر</option>
                            <option value="32">العصابات</option>
                            <option value="33">قطاع الطرق</option>
                            <option value="34">اللصوص</option>

                            <option value="35">عصابات قرطاجية</option>
                            <option value="36"> فرق العصيان القرطاجية</option>
                            <option value="37"> متمردى قرطاجة</option>
                            <option value="38">القوات الخاصة القرطاجية</option>
                            <option value="39">عاصمة التمرد</option>

                            <option value="100">عاصمة المشاه</option>
                            <option value="101">عاصمة الفرسان</option>
                            <option value="102">عاصمة المدرعين</option>
                            <option value="103">عاصمة الرماة</option>
                            <option value="104">عاصمة المقاليع</option>
                            <option value="105">عاصمة المنجنيق</option>
                            <option value="125">حلبة التحدى</option>
                            <option value="126">حلبة الموت</option>
                            <option value="127">حلبة الاحلاف</option>
                            
                            <option value="150">تمثال صغير</option>
                            <option value="151">تمثال وسط</option>
                            <option value="152">تمثال كبير</option>
                            
                            <option value="153">ذئب صغير</option>
                            <option value="154">ذئب وسط</option>
                            <option value="155">ذئب كبير</option>
                            
                            
                            <option value="180">مدينة الملكة</option>
                            
                            <option value="184">قلاع التمرد</option>
                            <option value="185">ميدان التحدى</option>
                            
                            <option value="1000">مدينة</option>
                            
                        </select>
                        <select id="WORLD_UNIT_WITH_LVL">
                        </select>
                        <input placeholder="انواع"   id="MoreUnitTyps"/>
                        <input placeholder="مستويات" id="MoreUnitLvls"/>
                    </div>
                    <div class="table" style="margin-bottom: 0px;"> <img src="img/bg-th-left.gif" width="8" height="7" alt="" class="left" /> <img src="img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
                        <div class="listing" style="text-align: center">
                            <div class="th" style="background: #9097A9 url(../img/bg-th-left.gif) no-repeat left top;">
                                <div class="tr" style="width: 30%; display: inline-block; ">قائمة الجوائز</div>
                                <div class="tr" style="width: 68%; display: inline-block">المواد</div>
                            </div>
                            <div class="content" style="overflow: auto">
                                <div id="matrial-list">

                                </div>

                                <div id="player-list">

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <div id="footer"></div>
        </div>
    </body>
    <script>
        var BASE_URL   = "<?=BASE_URL?>";
        var SERVER_ID  = <?=$_GET["server"]?> ;
        var OuthToken  = '<?=$_GET["AdminToken"]?>';
        var WS_HOST    = '<?=WEB_SOCKET_HOST?>';
        var WS_PORT    = '<?=$ServerList[$_GET["server"]]["Port"]?>';
    </script>
    <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/base.js"></script>
    <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/WorldUnit.js"></script>
    <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/worldUnitPrize.js"></script>
</html>
