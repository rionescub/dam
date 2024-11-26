<?php

use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Textarea;
use App\Helpers\TeamSettingsHelper;

return array_map(function ($setting) {
    return TeamSettingsHelper::makeSetting(...$setting);
}, [
    ['sponsors_banner_image', 'Sponsors Banner', Image::class],
    ['sponsors_title', 'Sponsors Page Title', Text::class],
    ['sponsors_content', 'Sponsors Content', Textarea::class],
    ['sponsors_cta_title', 'Sponsors CTA Title', Text::class],
    ['sponsors_cta_text', 'Sponsors CTA Text', Text::class],
    ['sponsors_cta_button_text', 'Sponsors CTA Button Text', Text::class],

]);
