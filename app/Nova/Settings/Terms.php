<?php

use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Textarea;
use App\Helpers\TeamSettingsHelper;

return array_map(function ($setting) {
    return TeamSettingsHelper::makeSetting(...$setting);
}, [
    ['terms_title', 'Terms and Conditions Page Title', Text::class],
    ['terms_content_title', 'Terms and Conditions Content Title', Text::class],
    ['terms_content', 'Terms and Conditions Content', Trix::class],
]);
