<?php

use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\URL;
use App\Helpers\TeamSettingsHelper;

return array_map(function ($setting) {
    return TeamSettingsHelper::makeSetting(...$setting);
}, [
    ['website_logo', 'Website Logo', Image::class],
    ['home_title', 'Home Page Title', Text::class],
    ['home_subtitle1', 'Home Page Subtitle 1', Text::class],
    ['home_subtitle2', 'Home Page Subtitle 2', Text::class],
    ['home_description', 'Home Page Description', Textarea::class],
    ['home_learn_more', 'Home Page Learn More Button', Text::class],
    ['hero_background_image', 'Hero Background Image', Image::class],
    ['overlay_image', 'Overlay Image', Image::class],
    ['contest_title', 'Contest Title', Text::class],
    ['contest_description', 'Contest Description', Textarea::class],
    ['contest_stage_1_title', 'Contest Stage 1 Title', Text::class],
    ['contest_stage_1_desc', 'Contest Stage 1 Description', Textarea::class],
    ['contest_stage_2_title', 'Contest Stage 2 Title', Text::class],
    ['contest_stage_2_desc', 'Contest Stage 2 Description', Textarea::class],
    ['contest_stage_3_title', 'Contest Stage 3 Title', Text::class],
    ['contest_stage_3_desc', 'Contest Stage 3 Description', Textarea::class],
    ['video_url', 'Video URL', URL::class],
    ['video_title', 'Video Title', Text::class],
    ['video_who', 'Who are we ', Text::class],
    ['video_description', 'Video Description', Textarea::class],
    ['video_logged_description', 'Video Logged in Text', Textarea::class],
    ['video_button_text', 'Video Button Text', Text::class],
    ['testimonial_title', 'Testimonial Title', Text::class],
    ['testimonial_description', 'Testimonial Description', Textarea::class],
    ['cookie_policy_link', 'Cookie Link', Text::class],
    ['cookies_text', 'Cookies Text', Textarea::class],
]);
