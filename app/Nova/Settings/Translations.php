<?php

use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Textarea;
use App\Helpers\TeamSettingsHelper;

return array_map(function ($setting) {
    return TeamSettingsHelper::makeSetting(...$setting);
}, [
    ['about_us_page_name', 'About Us Page Name', Text::class],
    ['blog_page_name', 'Blog Page Name', Text::class],
    ['my_account_page_name', 'My Account Page Name', Text::class],
    ['results_page_name', 'Results Page Name', Text::class],
    ['rules_page_name', 'Rules Page Name', Text::class],
    ['register_page_name', 'Register Page Name', Text::class],
    ['login_page_name', 'Login Page Name', Text::class],
    ['forgot_password_page_name', 'Forgot Password Page Name', Text::class],
    ['terms_page_name', 'Terms and Conditions Page Name', Text::class],
    ['privacy_page_name', 'Privacy Policy Page Name', Text::class],
    ['contact_us_page_name', 'Contact Us Page Name', Text::class],
    ['usefull_links', 'Usefull Links', Text::class],
    ['company_links', 'Company Links', Text::class],
]);
