<?php

namespace App\Helpers;

use Laravel\Nova\Fields\Field;

class TeamSettingsHelper
{
    public static function makeSetting($key, $name, $class): Field
    {
        return $class::make($name, $key);
    }
}
