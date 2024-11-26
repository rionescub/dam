<?php

use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Textarea;
use App\Helpers\TeamSettingsHelper;

return array_map(function ($setting) {
    return TeamSettingsHelper::makeSetting(...$setting);
}, [
    ['results_banner_image', 'Results Banner', Image::class],
    ['results_title', 'Results Page Title', Text::class],
    ['results_content_title', 'Results Content Title', Text::class],
    ['results_content', 'Results Content', Trix::class],
]);
