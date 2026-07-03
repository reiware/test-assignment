<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('files:cleanup')
    ->hourly()
    ->withoutOverlapping();
