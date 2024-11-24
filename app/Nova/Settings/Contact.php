<?php

use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Textarea;
use App\Helpers\TeamSettingsHelper;

return array_map(function ($setting) {
    return TeamSettingsHelper::makeSetting(...$setting);
}, [
    ['contact_title', 'Contact Page Title', Text::class],
    ['email_1', 'First email', Text::class],
    ['email_2', 'Second email', Text::class],
    ['contact_form_title', 'Contact Form Title', Text::class],
    ['contact_form_name_field', 'Contact Form Name Field', Text::class],
    ['contact_form_email_field', 'Contact Form Email Field', Text::class],
    ['contact_form_message_field', 'Contact Form Message Field', Text::class],
    ['contact_form_submit_button', 'Contact Form Submit Button', Text::class],
    ['contact_form_success_message', 'Contact Form Success Message', Text::class],
    ['contact_form_error_message', 'Contact Form Error Message', Text::class],
]);
