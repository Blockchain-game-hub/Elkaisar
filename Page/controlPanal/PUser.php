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
                    
                    <?= LCPBase::getTabs("User")?>  
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
                            <input type="submit" id="searchByUserName" name="Submit" value="بحث بالاسم" />
                        </label>
                        <label>
                            <input type="submit" id="searchByUserEmail" name="Submit" value="بحث بالبريد" />
                        </label>
                        <label>
                            <input type="submit" id="searchForAdmins" name="Submit" value="عرض طقم الادارة" />
                        </label>
                    </div>
                    <div class="table"> 
                        <img src="<?=RESOURCE_BATH?>/img/bg-th-left.gif" width="8" height="7" alt="" class="left" /> 
                        <img src="<?=RESOURCE_BATH?>/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
                        <table id="user-table" class="listing" cellpadding="0" cellspacing="0">
                            <tr>
                                <th class="first" width="177"> الاعضاء</th>
                                <th>تغير كلمة المرور</th>
                                <th>تغير الصلاحيات</th>
                                <th>تغيرالاسم</th>
                                <th>تغير البريد</th>
                                <th class="last">حظر</th>
                            </tr>
                            
                            
                            <?php 
                            if(isset($_GET["page"])){
                                $page_num = htmlspecialchars($_GET["page"])*15;
                            }else{
                                $page_num = 0;
                            }
                            
                            $all_member = selectFromTableIndex("*", "game_user", "user_group > 0 LIMIT 15 OFFSET $page_num");
                            $pages = selectFromTableIndex("COUNT(*) AS m_count", "game_user", "1");
                            $mem_count = $pages[0]["m_count"];
                            
                            foreach ($all_member  as $one){
                               
                                echo '  <tr data-id-user="'.$one["id_user"].'">
                                            <td class="first">- '.$one['user_name'].' ('.$one['email'].') ('.$one['user_group'].')</td>
                                            <td class="user-change-pass" data-id-user="'.$one["id_user"].'">    <img src="../img/add-icon.gif" width="16" height="16" alt="" /></td>
                                            <td class="change-user-group" data-id-user="'.$one["id_user"].'">   <img src="../img/hr.gif" width="16" height="16" alt="" /></td>
                                            <td class="change-user-name" data-id-user="'.$one["id_user"].'">    <img src="../img/save-icon.gif" width="16" height="16" alt="" /></td>
                                            <td class="change-user-email" data-id-user="'.$one["id_user"].'">   <img src="../img/edit-icon.gif" width="16" height="16" alt="" /></td>
                                            <td class="pannUser" data-id-user="'.$one["id_user"].'">            <img src="../img/add-icon.gif" width="16" height="16" alt="add" /></td>
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
        var BASE_URL = "<?=BASE_URL?>";
        var SERVER_ID = <?=$_GET["server"]?> ;
        var OuthToken   = '<?=$_GET["AdminToken"]?>';
        var WS_HOST    = '<?=WEB_SOCKET_HOST?>';
        var WS_PORT    = '<?=$ServerList[$_GET["server"]]["Port"]?>';
    </script>
    <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/base.js"></script>
    <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/user.js"></script>
</html>
