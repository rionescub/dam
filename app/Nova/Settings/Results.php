<?php

use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Textarea;
use App\Helpers\TeamSettingsHelper;

return array_map(function ($setting) {
    return TeamSettingsHelper::makeSetting(...$setting);
}, [
    ['results_title', 'Results Page Title', Text::class],
    ['results_content_title', 'Results Content Title', Text::class],
    ['results_content', 'results Content', Trix::class],
]);
