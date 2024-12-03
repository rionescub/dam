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
    ['rules_banner_image', 'Rules Banner', Image::class],
    ['rules_title', 'Rules Page Title', Text::class],
    ['rules_content_title', 'Rules Content Title', Text::class],
    ['rules_content', 'Rules Content', Trix::class],
    ['rules_pdf_file', 'Rules PDF File', File::class],
    ['rules_pdf_button_text', 'Rules PDF Button Text', Text::class],
]);
