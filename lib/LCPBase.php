<?php

class LCPBase
{
    
    static function TryToHack()
    {
        
    }
    
    static function getTabs($CurrentTab)
    {
        $serverID = $_GET["server"] ;
        $Ot = $_GET["AdminToken"];

        $ret =  '   <li'.($CurrentTab == 'home'           ? ' class="active"' : "").'><span><span><a href="">الرئيسية</a></span></span></li>
                    <li'.($CurrentTab == 'User'           ? ' class="active"' : "").' ><span><span><a href="User?server='.$serverID.'&AdminToken='.$Ot.'">عضو</a></span></span></li>
                    <li'.($CurrentTab == 'Player'         ? ' class="active"' : "").'><span><span><a href="Player?server='.$serverID.'&AdminToken='.$Ot.'">لاعب</a></span></span></li>
                    <li'.($CurrentTab == 'PlayerDeleted'  ? ' class="active"' : "").'><span><span><a href="PlayerDelted?server='.$serverID.'&AdminToken='.$Ot.'">محذوف</a></span></span></li>
                    <li'.($CurrentTab == 'World'          ? ' class="active"' : "").'><span><span><a href="World?server='.$serverID.'&AdminToken='.$Ot.'">العالم</a></span></span></li>
                    <li'.($CurrentTab == 'WorldUnitPrize' ? ' class="active"' : "").'><span><span><a href="WorldUnitPrize?server='.$serverID.'&AdminToken='.$Ot.'">نسب</a></span></span></li>
                    <li'.($CurrentTab == 'SendPrize'      ? ' class="active"' : "").'><span><span><a href="SendPrize?server='.$serverID.'&AdminToken='.$Ot.'">جوائز</a></span></span></li>
                    <li'.($CurrentTab == 'SendPrizeHis'   ? ' class="active"' : "").'><span><span><a href="SendPrizeHis?server='.$serverID.'&AdminToken='.$Ot.'&page=0">تاريخ</a></span></span></li>
                    <li'.($CurrentTab == 'ServerAnnounce' ? ' class="active"' : "").'><span><span><a href="ServerAnnounce?server='.$serverID.'&AdminToken='.$Ot.'&page=0">An</a></span></span></li>
                    <li'.($CurrentTab == 'PlayerState'    ? ' class="active"' : "").'><span><span><a href="PlayerState?server='.$serverID.'&AdminToken='.$Ot.'">احصا</a></span></span></li>
                    <li'.($CurrentTab == 'Exchange'       ? ' class="active"' : "").'><span><span><a href="Exchange?server='.$serverID.'&AdminToken='.$Ot.'">التبادل</a></span></span></li>
                    <li'.($CurrentTab == 'Item'           ? ' class="active"' : "").'><span><span><a href="Item?server='.$serverID.'&AdminToken='.$Ot.'">المواد</a></span></span></li>
                    <li'.($CurrentTab == 'onePlayer'      ? ' class="active"' : "").'><span><span><a href="onePlayer.php?server='.$serverID.'">احصاء لاعب</a></span></span></li>
                    <li'.($CurrentTab == 'Online'         ? ' class="active"' : "").'><span><span><a href="Online?server='.$serverID.'&AdminToken='.$Ot.'">online</a></span></span></li>
                    <li'.($CurrentTab == 'rate'           ? ' class="active"' : "").'><span><span><a href="rate.php?server='.$serverID.'&AdminToken='.$Ot.'">نسب الجوائز</a></span></span></li>
                    <li'.($CurrentTab == 'spPrize'        ? ' class="active"' : "").'><span><span><a href="spPrize.php?server='.$serverID.'&AdminToken='.$Ot.'">ج خاصة</a></span></span></li>
                    <li'.($CurrentTab == 'Equip'          ? ' class="active"' : "").'><span><span><a href="Equip?server='.$serverID.'&AdminToken='.$Ot.'">معدات</a></span></span></li>
                    <li'.($CurrentTab == 'reportprize'    ? ' class="active"' : "").'><span><span><a href="reportPrize.php?server='.$serverID.'&AdminToken='.$Ot.'">تقارير</a></span></span></li>
                    <li'.($CurrentTab == 'Ip    '         ? ' class="active"' : "").'><span><span><a href="Ip?server='.$serverID.'&AdminToken='.$Ot.'">ip</a></span></span></li>
                  '
                ;
        return $ret;
    }
    



    static function getMenu(){

        return '<div class="right-menu">
                    <ul>
                        <li> <a href="menu/pan.php">حظر</a></li>
                        <li> <a href="menu/maint.php">تفعيل الصيانة</a></li>
                    </ul>
                </div>';

    }
}