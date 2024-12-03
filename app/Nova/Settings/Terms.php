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
    ['terms_banner_image', 'Terms and Conditions Banner', Image::class],
    ['terms_title', 'Terms and Conditions Page Title', Text::class],
    ['terms_content_title', 'Terms and Conditions Content Title', Text::class],
    ['terms_content', 'Terms and Conditions Content', Trix::class],
    ['terms_pdf_file', 'Terms PDF File', File::class],
    ['terms_pdf_button_text', 'Terms PDF Button Text', Text::class],
]);
