<?php

/**
 * Messages is the legacy part of this app.
 * It will keep common translations.
 */

return [
    //pageTitle
    'page_title_user_show' => 'User informations',
    'page_title_user_edit' => 'Updating user informations',
    'page_title_channel_show' => 'Channel informations ',
    'page_title_channel_edit' => 'Updating podcast ',
    'page_title_channel_index' => 'Your podcasts',
    'page_title_channel_create' => 'Add a podcast',
    'page_title_user_register' => 'Register',
    'page_title_user_login' => 'Login',
    'page_title_lost_password' => 'Lost password',
    'page_title_user_logout' => 'Logout',
    'page_title_home_index' => 'Welcome',
    'page_title_podcasts_index' => 'Podcasts',
    'page_title_medias_stats_index' => 'Episodes downloads',
    'page_title_channel_stats_index' => 'Episodes downloads',
    'page_title_channel_app_stats_index' => 'Applications',

    // channel global stats
    'channel_stats_link_graph_legend' => 'Audience',
    'channel_stats_link_last7_period' => 'last 7 days',
    'channel_stats_link_last15_period' => 'last 15 days',
    'channel_stats_link_last30_period' => 'last 30 days',
    'channel_stats_link_last90_period' => 'last 90 days',

    // breadcrumb
    'page_title_home_breadcrumb' => 'Welcome',
    'page_title_podcasts_breadcrumb' => 'Your podcasts',

    // flash
    'flash_channel_has_been_created' => "Podcast :channel has been successfully registered :)",
    'flash_channel_id_is_invalid' => "The youtube url you entered was invalid !",

    // commons
    'yes' => "yes",
    'no' => "no",
    'language_label' => "Podcast Language",
    'channel_show_stats_alt' => "Show my my stats",

    'button_update_label' => 'Update',
    'button_cancel_label' => 'Cancel',

    'button_show_channel_label' => "View",
    'button_edit_channel_label' => "Edit",
    'button_edit_thumb_label' => "Update thumb",
    'button_view_episodes_label' => "Episodes",
    'button_update_channel_label' => "Update my channel's information",
    'button_create_channel_label' => "Convert one new channel",
    'button_need_assistance_label' => "Contact me",
    'button_modify_label' => 'Update',
    'button_submit_label' => 'Submit',
    'button_upgrade_my_plan' => 'Upgrade my plan',

    // home part
    'no_channels_at_this_moment' => "You have no channel at this time.",
    'title_application_label' => 'Dashboard',
    'title_channel_index_label' => 'Your channels',
    'danger_podcast_is_no_more_updated' => 'Your podcast is no more updated, please consider upgrading your plan.',
    'button_i_want_to_upgrade_now' => 'Upgrade',
    'one_of_your_podcast_is_no_more_updated' => "One of your podcast is no more updated, and your new episodes will not be added to your podcast. Please consider upgrading your plan.",

    // user part
    'button_update_user_label' => 'update',


    // thumbs part 
    'page_title_channel_thumbs_index' => 'Podcast image',
    'button_update_thumbs_label' => 'Upload a new podcast image',
    'page_title_channel_thumbs_edit' => 'Upload a new one',
    'thumbs_edit_new_thumb_help_message' => '<b>Image must meet iTunes requirements !</b> 
    <ul>
        <li>Minimum dimensions : 1400x1400</li>
        <li>Maximum dimensions : 3000x3000</li>
        <li>Squared : width = height</li>
        <li>File size must be less than 5Mb (using jpg format will help)</li>
    </ul>',
    'thumbs_edit_new_thumb_form_label' => 'Select your new podcast image.',
    'thumbs_edit_error_image_required' => 'You should select a new image before clicking on the update button.',
    'thumbs_edit_error_image_dimensions' => 'The new image must be a square one between 1400x1400 and 3000x3000.',


    //channel index part


    // adding a channel part
    'channel_owner_warning_checkbox_label' => "By checking this case, I certify to be the owner of this channel",
    'channel_to_be_validated' => '--- VALIDATION WAITING ---',
    'youtube_channel_url_label' => "Your youtube channel url (by ex : https://www.youtube.com/channel/UCVeMw72tepFl1Zt5fvf9QKQ)",
    'create_youtube_channel_url_help' => "Your youtube channel url should look like this one https://www.youtube.com/channel/UCVeMw72tepFl1Zt5fvf9QKQ",
    'create_youtube_channel_url_error' => "Your youtube channel url seems to be invalid. It should be something like that https://www.youtube.com/channel/UCVeMw72tepFl1Zt5fvf9QKQ",

    // register/login/reset password part
    'title_register_label' => 'Sign up',
    'button_register_label' => 'Sign up',

    'title_login_label' => 'Sign in',
    'title_logout_label' => 'Logout',
    'button_login_label' => 'Sign in',
    'button_lost_password' => 'Send Password Reset Link',
    'button_reset_password' => 'Reset password',

    'name_label' => 'Name',
    'email_label' => 'Email address',
    'password_label' => 'Password',
    'confirm_password_label' => 'Password confirmation',
    'change_password_label' => 'Update your password',
    'remember_me_label' => 'Remember me',
    'password_forgotten_label' => 'Password forgotten',

    // channel show/edit part
    'channel_youtube_url_label' => "Youtube channel url",
    'channel_podcast_url_label' => "Podcast url",
    'channel_podcast_created_label' => "Podcast created on",
    'channel_podcast_updated_label' => "Podcast updated on",
    'podcast_link_label' => "Your podcast",

    'channel_name_label' => "Channel name",
    'channel_name_help' => "This is the name of your channel on Youtube.",

    'channel_podcast_name_label' => "Podcast name",
    'channel_podcast_name_help' => "If you want your podcast to have a different name than your channel.",

    'channel_authors_label' => "Author(s)",
    'channel_authors_help' => "Who is / are the author (s) / author (s).",

    'channel_email_label' => "Email address",
    'channel_email_help' => "The email address that appears in your podcast.",

    'channel_description_label' => "Description",
    'channel_description_help' => "The description is obtained from Youtube.",

    'channel_link_label' => "Website url",
    'channel_link_help' => "The website that appears in your podcast.",

    'channel_category_label' => "Category",
    'channel_category_help' => "In which category is your podcast ?",

    'no_category_defined_label' => "This podcast has no category.",
    'no_filter_defined_label' => "No filter has been entered. All episodes (according to your plan) are included in your podcast.",

    'channel_lang_label' => "Podcast Language",
    'channel_lang_help' => "Your podcast language.",

    'channel_explicit_label' => "This podcast uses an explicit language.",
    'channel_explicit_help' => "If your podcast is using explicit content, you should check this check box.",

    'channel_filter_by_tag_label' => "Tag filter",
    'channel_filter_by_tag_help' => "Only videos that have been tagged (under Youtube) will be included in your podcast. For example \"podcast\"",

    'channel_filters_label' => 'Filters',
    'filters_warning' => '<i class="fas fa-exclamation-triangle"></i> Applying incorrect filters may empty your podcast. Change this only if you know what you are doing <i class="fas fa-exclamation-triangle"></i>.',
    'accept_video_by_tag' => "Only youtube videos tagged with tag <b>{:tag}</b> will be included into your podcast. Don't forget to apply tag !",
    'reject_video_by_keyword' => "Videos with this keyword <b>{:keyword}</b> in their title will not be included in your podcast. However, all the others will be included.",
    'reject_video_too_old' => "Only videos published after this date <b>{:date}</b> will be included.",

    'channel_filter_by_date_label' => "Date filter",
    'channel_filter_by_date_help' => "Only videos published after this date will be included in your podcast. For example: \"18/09/2016\"",

    'channel_filter_by_keyword_label' => "Keyword filter",
    'channel_filter_by_keyword_help' => "Only videos whose title includes this keyword will be included in your podcast. For example \"Podcast\"",

    'channel_ftp_feature_description' => 'If you wish, you can host the xml podcast file on one subdomain. For example: http://podcast.example.com. If you want to use this feature, you will need to fill in the following fields.',

    'channel_ftp_host_label' => "FTP host",
    'channel_ftp_host_help' => "Address of your FTP server",
    'no_ftp_host_defined_label' => "No FTP server defined",

    'channel_ftp_user_label' => "Login (FTP)",
    'channel_ftp_user_help' => "Login to connect to your FTP server",
    'no_ftp_user_defined_label' => "No FTP login defined",

    'channel_ftp_pass_label' => "Password (FTP)",
    'channel_ftp_pass_help' => "Password to connect to your FTP server",
    'no_ftp_pass_defined_label' => "No Password defined",

    'channel_ftp_dir_label' => "Folder (FTP)",
    'channel_ftp_dir_help' => "Directory to upload the podcast file on your FTP server",
    'no_ftp_dir_defined_label' => "No destination directory specified",

    'channel_ftp_podcast_label' => "File name (FTP)",
    'channel_ftp_podcast_help' => "The name of the XML file uploaded on your FTP server. By default it will be podcast.xml",
    'no_ftp_podcast_defined_label' => "No password defined",

    'channel_ftp_pasv_label' => "Passive mode ? (FTP)",
    'channel_ftp_pasv_help' => "Transfer mode used for ftp transfer. By default in \"active\". By checking this check box the transfer will be done in \"passive\"",
    'no_ftp_pasv_defined_label' => "Active mode",
    'ftp_pasv_defined_label' => "Passive mode",

    // footer
    'layout_footer_slogan' => '- Convert your youtube channel in a wonderful podcast in a snap !',
    'layout_footer_legal_notice' => 'Legal Notice',
    'layout_footer_legal_notice_url' => 'http://fr.podmytube.com/mentions-legales',

];
