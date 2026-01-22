<?php
# Open a connection to the system logger
openlog("Syslog", LOG_ODELAY | LOG_PERROR, LOG_LOCAL0);

# Execute log
syslog(LOG_INFO, "Test - Index.php");
error_log("ErrorLog: Test - Index.php");

echo "Env variable: ENV =".getenv('ENV')."</br>";

echo "INDEX.PHP! testing a11 1";
echo "2";
echo "3";
echo "4";
echo "5";
echo "6";
echo "7";
echo "8";
echo "9";
echo "10";
echo "11";
echo "12";