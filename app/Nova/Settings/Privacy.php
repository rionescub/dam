<?php

use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Textarea;
use App\Helpers\TeamSettingsHelper;

return array_map(function ($setting) {
    return TeamSettingsHelper::makeSetting(...$setting);
}, [
    ['privacy_banner_image', 'Privacy Policy Banner', Image::class],
    ['privacy_title', 'Privacy Policy Page Title', Text::class],
    ['privacy_content_title', 'Privacy Policy Content Title', Text::class],
    ['privacy_content', 'Privacy Policy Content', Trix::class],
    ['privacy_pdf_file', 'Privacy PDF File', File::class],
    ['privacy_pdf_button_text', 'Privacy PDF Button Text', Text::class],
]);
