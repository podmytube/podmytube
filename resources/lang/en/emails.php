<?php

return [
    'common_hello' => 'Hello :name',
    'common_if_you_have_any_questions' =>
        'If you have any question, feel free to answer this email.<br>Cheers.<br>Fred',
    'common_upgrade_my_plan' => 'I want to upgrade.',

    'limitsReached_subject' =>
        'Episode ":media_title" will not be added to your podcast.',
    'limitsReached_body' => <<<EOT
I'm sorry to tell you that your episode <b>:media_title</b> will not be added to your podcast :'(.<br>
This is due to the fact that your channel <b>:channel_name</b> has reached its limits.<br>
Please consider upgrade your plan to get all your episodes included in your podcast without any time limits.
EOT
    ,
    'monthlyReport_subject' => 'Here is your :period report for :channel_name',
    'monthlyReport_channelShouldUpgrade_callToAction' =>
        "To take advantage of all of Podmytube's features and have all of your episodes included for an unlimited period of time, please consider upgrading your account.",
    'monthlyReport_no_media_published' => 'No media published this month',

    'newCategories_hello' => 'Hello :name',
    'newCategories_body' =>
        'Recently apple has made a big categories update on its podcast catalog.' .
        'These categories are now available on the dashboard for your podcast to update.' .
        'You should do this as soon as possible to avoid being misclassified.',
    'newCategories_explanations' =>
        'To do so, all you need is :' .
        '<ul>' .
        '<li>connecting on the dashboard</li>,' .
        '<li>edit your channels details</li>' .
        '<li>and set the category you want.</li>' .
        '</ul>',

    'welcome_aboard' => 'Welcome on Podmytube,',
    'welcome_p_first_line' => "I'm delighted by your interest in my service !",
    'welcome_p_register' =>
        'Now that you are registered, you should add the youtube channel you want to convert, in a magnificent podcast',
    'welcome_a_add_one_channel' => 'Convert my channel',

    'registered_h1_success' => 'Congratulations :name,',
    'registered_p_channel_is_now_registered' =>
        'Channel <b>:channel_id</b> is now registered.',
    'registered_p_in_a_few_minutes' =>
        'In a few minutes, you channel will be validated then your podcast will include your last episodes.',

    'registered_p_one_last_word' => "<b>One last word</b>. If you want to register your podcast on iTunes (You should !) you will have to :
        <ul>
        <li>Select your podcast category </li>
        <li>Add a podcast illustration (1400x1400 minimum 3000x3000 maximum)</li>
        </ul>",
    'registered_p_if_you_have_any_questions' =>
        "If you have any question, you only have to ask, I'm here to help :).<br>Cheers.<br>Fred",
    'registered_a_select_a_category' => 'Select your podcast category',
    'registered_a_add_an_illustration' => 'Add your illustration',

    'newsletter_subject' => ':period newsletter',
];
