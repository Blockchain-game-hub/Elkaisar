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
                    <?= LCPBase::getTabs("World")?>  
                </ul>
            </div>
            <div id="middle">
                <div id="left-column">
                    <h3>Header</h3> </div>
                <div id="center-column">
                    
                    <br />
                    <div class="select-bar">
                        <label>
                            X: <input style="width: 35px" id="xCoordInput" type="text" name="textfield" />
                        </label>
                        <label>
                            Y: <input style="width: 35px" id="xCoordInput" type="text" name="textfield" />
                        </label>
                        <label>
                            Lv: <input style="width: 35px" id="xCoordInput" type="text" name="textfield" />
                        </label>
                        <label>
                            <input type="submit" id="searchByCoord" name="Submit" value="بحث" />
                        </label>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <label>
                            Type: <select class="allWorldUnitSelectFrom" style="width: 120px"></select>
                        </label>
                        <label>
                            Lv: <input style="width: 35px" id="xCoordInput" type="text" name="textfield" />
                        </label>
                        <label>
                            <input type="submit" id="searchByPlayerType" name="Submit" value="بحث" />
                        </label>
                    </div>
                    <div class="select-bar">
                        <label>
                            X: <input style="width: 35px" id="xCoordInput" type="text" name="textfield" />
                        </label>
                        <label>
                            Y: <input style="width: 35px" id="xCoordInput" type="text" name="textfield" />
                        </label>
                        <label>
                            <input type="submit" id="changeLvlByCoord" name="Submit" value="تعديل المستوى" />
                        </label>
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <label>
                            X: <input style="width: 35px" id="xCoordInput" type="text" name="textfield" />
                        </label>
                        <label>
                            Y: <input style="width: 35px" id="xCoordInput" type="text" name="textfield" />
                        </label>
                        <label>
                            Type: <select class="allWorldUnitSelectFrom" style="width: 120px"></select>
                        </label>
                        <label>
                            <input type="submit" id="changeTypeByCoord" name="Submit" value="تعديل النوع" />
                        </label>
                      
                    </div>
                    <div class="select-bar">
                        <label>
                            Type: <select class="allWorldUnitSelectFrom" id="unitTypeToChangeLvl" style="width: 120px"></select>
                        </label>
                        <label>
                            <input type="submit" id="changeLvlByType" name="Submit" value="تعديل مستوى" />
                        </label>
                    </div>
                    <div class="table"> 
                        
                    </div>
                </div>
                <div id="right-column"><?= LCPBase::getMenu()?>  </div>
            </div>
            <div id="footer"></div>
        </div>
    </body>
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
    <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/World.js"></script>
</html>
