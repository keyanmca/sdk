<?php

//    site: www.wowza.io
//    author: Carlos Camacho
//    email: carloscamachoucv@gmail.com
//    created: 12/11/2015
require('wowza.php');

$wow = new Wowza("127.0.0.1:8087");

//print ($wow->getApplications());
//print ($wow->createNonSecuredApplication("wakawaka33"));
//print ($wow->createSecuredApplication("rugabuga88"));
//print ($wow->deleteApplication("wakawaka"));
//$wow->dumpApplicationConfig("wakawaka");

$wow->deleteAllApplications();




?>

