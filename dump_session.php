<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$s = DB::table('sessions')->orderBy('last_activity', 'desc')->first();
echo base64_decode($s->payload);
