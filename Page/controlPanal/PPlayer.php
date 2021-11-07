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
                    <?= LCPBase::getTabs("Player")?>  
                </ul>
            </div>
            <div id="middle">
                <div id="left-column">
                    <h3>Header</h3> </div>
                <div id="center-column">
                    
                    <br />
                    <div class="select-bar">
                        <label>
                            <input id="UserSearchInput" type="text" name="textfield" />
                        </label>
                        <label>
                            <input type="submit" id="searchByPlayerName" name="Submit" value="بحث باسم الاعب" />
                        </label>
                        <label>
                            <input type="submit" id="searchByPlayerGuild" name="Submit" value="بحث باسم الحلف" />
                        </label>
                        <label>
                            <input type="submit" id="searchByPlayerAdmin" name="Submit" value="عرض الادارة" />
                        </label>
                    </div>
                    <div class="table"> 
                        <img src="<?=RESOURCE_BATH?>/img/bg-th-left.gif" width="8" height="7" alt="" class="left" /> 
                        <img src="<?=RESOURCE_BATH?>/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
                        <table id="user-table" class="listing" cellpadding="0" cellspacing="0">
                            <tr>
                                <th class="first" width="177"> الاعضاء</th>
                                <th> صلاحية</th>
                                <th>برستيج</th>
                                <th>شرف</th>
                                <th>ترقية</th>
                                <th>ذهب</th>
                                <th>حظر</th>
                                <th>القاب</th>
                                <th>فحص</th>
                                <th class="last">نقل</th>
                            </tr>
                            
                            
                            <?php 
                            DbConnect($_GET["server"]);
                            
                            if(isset($_GET["page"])){
                                $page_num = htmlspecialchars($_GET["page"])*15;
                            }else{
                                $page_num = 0;
                            }
                            
                            $all_member = selectFromTable("*", "player", "1 ORDER BY prestige DESC LIMIT 15 OFFSET $page_num");
                            $pages = selectFromTable("COUNT(*) AS m_count", "player", "1");
                            $mem_count = $pages[0]["m_count"];
                            
                            foreach ($all_member  as $one){
                               
                                echo '  <tr data-id-player="'.$one["id_player"].'">
                                            <td class="first change-player-name" data-id-player="'.$one["id_player"].'">- '.$one['name'].'</td>
                                            <td class="change-player-group"      data-id-player="'.$one["id_player"].'"> ('.$one["user_group"].')</td>
                                            <td class="change-player-prestige"   data-id-player="'.$one["id_player"].'">  '.$one["prestige"].' </td>
                                            <td class="change-player-honor"      data-id-player="'.$one["id_player"].'">  '.$one["honor"].' </td>
                                            <td class="change-player-porm"       data-id-player="'.$one["id_player"].'">  '.$one["porm"].' </td>
                                            <td class="change-player-gold"       data-id-player="'.$one["id_player"].'">  '.$one["gold"].' </td>
                                            <td class="pannPlayer"               data-id-player="'.$one["id_player"].'">   '. date("j M Y, H:i", $one["panned"]).'</td>
                                            <td class="changePlayerTitle"        data-player-name="'.$one['name'].'"     data-id-player="'.$one["id_player"].'"> <img src="../img/hr.gif" width="16" height="16" alt="" /></td>
                                            <td class="examinPlayer"             data-player-name="'.$one['name'].'"     data-id-player="'.$one["id_player"].'"> <img src="../img/hr.gif" width="16" height="16" alt="" /></td>
                                            <td class="transPlayer"              data-player-name="'.$one['name'].'"     data-id-player="'.$one["id_player"].'"> <img src="../img/save-icon.gif" width="16" height="16" alt="" /></td>
                                        </tr>';
                                
                            }
                            ?>
                        </table>
                        
                        <div class="select">
                            <ul>
                                <?php
                                    $start = max(0 , $page_num -5);
                                    $end   = min($start + 10 , (int)($mem_count/15));
                                    for($iii = $start ; $iii <= $end ; $iii++){
                                        
                                        
                                        echo ' <li><a href="?page='.$iii.'">'.$iii.'</a></li>' ;
                                        
                                        
                                    }
                                
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="right-column"><?= LCPBase::getMenu()?>  </div>
            </div>
            <div id="footer"></div>
        </div>
    </body>
    <script>
        var BASE_URL    = "<?=BASE_URL?>";
        var SERVER_ID   = <?=$_GET["server"]?> ;
        var SERVER_LIST = <?= json_encode(array_combine(array_keys($ServerList), array_column($ServerList, "name")), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)?>;
        var OuthToken   = '<?=$_GET["AdminToken"]?>';
        var WS_HOST    = '<?=WEB_SOCKET_HOST?>';
        var WS_PORT    = '<?=$ServerList[$_GET["server"]]["Port"]?>';
    </script>
    <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/base.js"></script>
    <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/Player.js"></script>
</html>
