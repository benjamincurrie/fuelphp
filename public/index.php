<?php
include dirname(__DIR__).'/apps/bootstrap.php';

Fuel\Foundation\Kernel::load_app('main');

$app = new Main\Application('dev');
echo $app->serve(Fuel\Http\Request::make())->send();
