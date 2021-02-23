
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

                    <?= LCPBase::getTabs("ServerAnnounce") ?>  
                </ul>
            </div>
            <div id="middle">
                <div id="left-column">
                    <h3>Header</h3> </div>
                <div id="center-column">
                    
                </div>
                <div id="right-column"> 
                    <ul class="Right-Column-Ul">
                        <li id="showSendMail"> ارسال بريد</li>
                        <li id="showSendAnnounce">ارسال  للشات</li>
                        <li id="showSendSchadAnn">جدولة الرسائل</li>
                    </ul>
                </div>
            </div>
            <div id="footer"></div>
        </div>
        <script>
            var BASE_URL = "<?= BASE_URL ?>";
            var SERVER_ID = <?= $_GET["server"] ?>;
            var OuthToken = '<?= $_GET["AdminToken"] ?>';
            var WS_HOST = '<?= WEB_SOCKET_HOST ?>';
            var WS_PORT = '<?= $ServerList[$_GET["server"]]["Port"] ?>';
        </script>
        <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/base.js"></script>
        <script type="text/javascript" src="<?= RESOURCE_BATH ?>/js/ServerAnnounce.js"></script>
    </body>
</html>
