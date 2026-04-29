<?php

namespace App\Helpers;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\Image;

class TeamSettingsHelper
{
    public static function makeSetting($key, $name, $class): Field
    {
        $field = $class::make($name, $key);

        if ($class === Image::class) {
            $field->disk('public')
                ->path('settings')
                ->storeAs(fn ($request) => $request->file($key)?->hashName());
        }

        return $field;
    }
}
