<?php
require vendor/autoload.php;
 = require_once bootstrap/app.php;
->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

 = App\Models\Business::whereHas(translations, fn() => ->where(name, like, %Ab
