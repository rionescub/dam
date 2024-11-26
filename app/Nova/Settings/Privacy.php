<?php

use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Textarea;
use App\Helpers\TeamSettingsHelper;

return array_map(function ($setting) {
    return TeamSettingsHelper::makeSetting(...$setting);
}, [
    ['privacy_banner_image', 'Privacy Policy Banner', Image::class],
    ['privacy_title', 'Privacy Policy Page Title', Text::class],
    ['privacy_content_title', 'Privacy Policy Content Title', Text::class],
    ['privacy_content', 'Privacy Policy Content', Trix::class],
]);
