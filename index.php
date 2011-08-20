<?php
require './shorty.php';
require './config.php';

$shorty = new Shorty($hostname, $connection);
$shorty->set_chars($chars);
$shorty->run();
?>
