<?php
include dirname(__DIR__).'/apps/bootstrap.php';

use Fuel\Foundation\Kernel;
use Fuel\Http\Request;

$app = new Main\Application();
echo $app->serve(Request::make())->send();
