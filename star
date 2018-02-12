#!/usr/bin/env php
<?php

require "init.php";

use Symfony\Component\Console\Application;
use Star\Commands\PimpleCommand;

set_time_limit(0);

$application = new Application();
$application->add(new PimpleCommand());
$application->run();

