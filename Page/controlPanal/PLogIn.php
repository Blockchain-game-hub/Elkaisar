<html class="" lang="ar">
    <head>
        <link rel="stylesheet" href="<?=RESOURCE_BATH?>/css/login.css"/>

    </head>
    <body>
   
        <div class="login-page">
            <div class="form">
                
                <input id="user-name" type="text" placeholder="username">
                <input id="password" type="password" placeholder="password">
                <select id="server-id">
                    <?php
                    
                    foreach ($ServerList as $id => $oneServer)
                    {
                        
                        echo '<option value="'.$id.'">'.$oneServer["name"].'</option>'."\n";
                    }
                    ?>
                </select>
                <button id="log-in-btn">تسجيل الدخول</button>
                
            </div>
        </div>
        <script>
            var BASE_URL = "<?=BASE_URL?>";
        </script>
        <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/base.js"></script>
        <script type="text/javascript" src="<?=RESOURCE_BATH?>/js/login.js"></script>
       
    </body>
</html>
