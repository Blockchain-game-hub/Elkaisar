<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

class ARestartServers {

    function Restart() {

        print_r(shell_exec("killall node"));
        print_r(shell_exec("killall node"));
        print_r(shell_exec("node /home/elkaisar/WebSocket/run_1.js > /home/elkaisar/WebSocket/Log/output-1-$(date +%m).txt &"));
        print_r(shell_exec("node /home/elkaisar/WebSocket/run_2.js > /home/elkaisar/WebSocket/Log/output-2-$(date +%m).txt &"));
        print_r(shell_exec("node /home/elkaisar/WebSocket/run_3.js > /home/elkaisar/WebSocket/Log/output-3-$(date +%m).txt &"));

    }
    
   

}
