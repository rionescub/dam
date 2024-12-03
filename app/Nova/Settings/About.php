<?php

use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Textarea;
use App\Helpers\TeamSettingsHelper;
use Laravel\Nova\Fields\Date;

return array_map(function ($setting) {
    return TeamSettingsHelper::makeSetting(...$setting);
}, [
    ['about_banner_image', 'About Banner', Image::class],
    ['about_title', 'About Page Title', Text::class],
    ['about_description', 'About Description', Textarea::class],
    ['about_metric_number', 'About Metric Number', Number::class],
    ['about_metric_number_text', 'About Metric Number Text', Text::class],
    ['about_metric_title', 'About Metric Title', Text::class],
    ['about_metric_description', 'About Metric Description', Textarea::class],
    ['about_metric_image_1', 'First Metric Image', Image::class],
    ['about_metric_image_2', 'Second Metric Image', Image::class],
    ['about_metric_image_3', 'Third Metric Image', Image::class],
    ['about_metric_button_text', 'About Metric Button Text', Text::class],
    ['about_open_date', 'Contest Open Date', Date::class],
    ['about_open_title', 'Contest Open Title', Text::class],
    ['about_open_text', 'Contest Open Text', Text::class],
    ['about_close_date', 'Contest Close Date', Date::class],
    ['about_close_title', 'Contest Close Title', Text::class],
    ['about_close_text', 'Contest Close Text', Text::class],
    ['about_jury_date', 'Contest Jury Date', Date::class],
    ['about_jury_title', 'Contest Jury Title', Text::class],
    ['about_jury_text', 'Contest Jury Text', Text::class],
    ['about_ceremony_date', 'Contest Ceremony Date', Date::class],
    ['about_ceremony_title', 'Contest Ceremony Title', Text::class],
    ['about_ceremony_text', 'Contest Ceremony Text', Text::class],
]);
