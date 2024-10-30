=== CheetahSender For WordPress ===
Contributors: Experian CheetahMail
Tags: SMTP relay, Experian, CheetahMail
Requires at least: 2.7
Tested up to: 3.2
Stable tag: 1.0.1


== Description ==

This plugin Override the wp_mail() function to use CheetahSender SMTP instead of wp_mail() function.

You should be a CheetahSender customer for using this plugin, you can't define your own SMTP, for this you might use another plugin
ie. wp-mail-smtp

You can set the following options:

* Specify the from name and email address for outgoing email.
* Choose to send mail by SMTP or PHP's mail() function.
* Specify an CheetahSender port (defaults to 25).
* Choose SSL / TLS encryption
* Specify CheetahSender username and password linked to your inbound point.


== Installation ==

1. Download
2. Upload to your `/wp-contents/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.


== Uninstallation and plugin deletion ==

1. Deactivate the plugin through the 'Plugins' menu in WordPress.
2. Delete the plugin through the 'Plugins' menu in WordPress. Notice that, it will reconfigure automatically the default SMTP WordPress Options.


== Frequently Asked Questions ==

= My plugin still sends mail via the mail() function =

If other plugins you're using are not coded to use the wp_mail() function but instead call PHP's mail() function directly, they will bypass the settings of this plugin. Normally, you can edit the other plugins and simply replace the `mail(` calls with `wp_mail(` (just adding wp_ in front) and this will work. I've tested this on a couple of plugins and it works, but it may not work on all plugins.

= Will this plugin work with WordPress versions less than 2.7? =

No. WordPress 2.7 changed the way options were updated, so the options page will only work on 2.7 or later.

= Can I add specific headers or attachments to all sent email from WordPress =
No you can't, WordPress do not actually propose those feature, hope it will in a next future ! Be sure, as if it would be available, it would be implemented ASAP :)

= Can I change the emails sent content =
No, the only solution to do this is to override the functions that are located in wp-includes/pluggable.php which contains all these features. It's up to you, by the way, it looks like hardcoding the WordPrfess Core.

== Screenshots ==

1. Screenshot of the Emails configuration.
2. Screen of the SMTP configuration.
3. Screen of the Test send feature.

== Changelog ==

V 1.0.1 : 
* bug fix
* translation in French and English
* change url and email contact

V 1.0.0 : initial release
