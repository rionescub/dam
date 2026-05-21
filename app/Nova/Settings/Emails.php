<?php

use Laravel\Nova\Fields\Trix;
use App\Helpers\TeamSettingsHelper;

return array_map(function ($setting) {
    return TeamSettingsHelper::makeSetting(...$setting);
}, [
    ['email_user_confirmation', 'User Confirmation Email', Trix::class],
    ['email_work_confirmation', 'Work Confirmation Email', Trix::class],
]);
