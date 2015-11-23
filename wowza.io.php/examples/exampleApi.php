<?php

//    site: www.wowza.io
//    author: Carlos Camacho
//    email: carloscamachoucv@gmail.com
//    created: 12/11/2015
require('../libs/wowza.php');

$wow = new Wowza("127.0.0.1:8087");

$wow->deleteAllApplications();
print ($wow->getApplications());
print ($wow->createNonSecuredApplication("sssss"));
//print ($wow->createSecuredApplication("rugabuga"));
//print ($wow->deleteApplication("wakawaka"));
//$wow->dumpApplicationConfig("wakawaka");






?>

