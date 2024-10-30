=== MailChimp Archives ===
Contributors: Mark Parolisi
Donate link: http://markparolisi.com
Tags: email, mail chimp, mailchimp
Requires at least: 2.7
Tested up to: 3
Stable tag: 3.2

Display your Mail Chimp Email Archives on your WordPress site.
 
== Description ==

2 Options to display your Archives:

-Add this short code : [mc_archives] to any page to show a list of your MailChimp email campaigns. The user can click the campaign title to see an archive of the full HTML mail message. This will fetch the campaign data from MailChimp live

Custom options for shortcode display:

1. Specify an interval to group your campaigns (by year or by month).
2. Fully customizable archive list with CSS rich classes and unique ID's
3. Specify whether to open archive in the same window or a new one (target='_blank').
4. Add a jQuery show/hide effect to your archives list. Helpful for long lists.

-Set a daily scheduled event for the plugin to fetch the campaigns and save them as posts in a new category called 'MailChimp Archives'. You may view them as you would any other post in WordPress.

Custom options for saving to WordPress:

1. Attribute archive to specified user.
2. Select category for archives
3. Select type (post/page)
4. Default post status (draft, pending, published)


-none yet

== Installation ==

1. Upload the `mail-chimp-archives` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Some defaults are set, but you can customize options and add your API key in the newly created admin sub-menu under the 'Settings' menu.
4. Add the short code [mc_archives] to any page to display your MailChimp archive list or set the plugin to save the campaigns as posts
-for more info on short codes visit the WP Codex :: http://codex.wordpress.org/Shortcode_API

== Frequently Asked Questions ==

= How many email campaigns will this show? = 

The MailChimp API restricts the list of campaigns to 1000. So that's where it is set.


= Should I save the archives to WordPress or just fetch them live with the shortcode? =

Saving them as posts is more reliable, due to MailChimp flux in delivery speed, hence fetching the archives with the shortcode may result in a long loading time on those pages. However, fetching them will not clutter your WordPress database with email archive posts that you will never want to edit.  


== Screenshots ==

-none 

== Changelog ==

= 3.0 =
Updated styling to the settigs page.
More refactoring for easier readablity.
Added the option to select which list(s) to use.

= 2.3 = 
refactoring to avoid naming conflict errors

= 2.2 =

Bug fixes

= 2.1 = 

Fixed incompatibility with other Mailchimp plugin(s)


= 2.0 = 
* Major refactoring and functionality. I've not set a scheduled task to store the Campaign ID, subject, and creation time in the wp_options table. This makes for much faster responses. I've also rewritten the save_fetch function to be less repetitive. Added button on the admin panel to update all records.

= 1.4 =
* Bug fixes

= 1.3 =
* Bug fixes on live fetch
* New options for saving (category, author, post type)

= 1.2 =
* Adds the option to save campaigns as posts.
* Fixed shortcode option to show single email archives in a window by themselves.

= 1.1 =
* Prevent multiple connection attempts.
* Displays only sent campaigns

= 1.0 =
* Initial stable version.

== Upgrade Notice ==
= 2.2 =
Received some notices of errors in server logs so I've padding some of my operations to help curb some of this. If you are receiving memory errors, then please send me the log and also check the API log in your Mailchimp admin panel (it's under account settings). If we try to connect to Mailchimp to fetch our emails and the connection is refused(or throttled for too long) for some reason, then that could be causing our error(s).

= 2.1 = 
Fixes incompatibility with other Mailchimp plugins.

= 2.0 =
We have major changes here. I know the plugin has been flaky lately, and it's part due to the new features I've been adding, and part due to MailChimp's API rules. I've hopefully corrected all of those and it should be a much better experience now. As for some people who are getting a fatal error on activation, please send me more info that just that. I want to get this working for EVERYONE.

= 1.4 =
This fixes a lot of bugs hopefully, but I'm still having slow loading (to where the script will timeout) sometimes. I've put a ticket in with MailChimp and hopefully we can get things a little faster.

= 1.2 =
This updates adds an important feature to the plugin.

= 1.1 =
This update will dramatically reduce load times for the plugin, and fixes the error of all your campaigns showing in the list instead of just the 'sent' ones.