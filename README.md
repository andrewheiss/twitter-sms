# Twitter SMS

This script rudimentarily allows you to receive notification of Twitter @replies and @mentions via SMS.

## Installation

1. Clone the repository.  
	(I keep a copy of the repository on my local machine and use rake to rsync it up to my server, since most shared hosts don't have git installed.)
2. Retrieve the Twitter PHP API submodule:
	1. From the top level of the cloned repository type:  
	`git submodule init && git submodule update`
3. Move and rename `sms.default.db` and `sms_config.default.php` to a folder that is not accessible from the webserver. (E.g. On a shared host with the webroot at `/home/username/www`, create a directory at `/home/username/protected`.)
4.  Add the appropriate data to `sms_config.php`. See below for SMS details.
5.  Ensure that `$config_dir` is set to the appropriate location in `index.php`.
6.  Visit `index.php` with your web browser to initialize the database.
7.  Every time you visit `index.php` it will check Twitter for new mentions. If it finds any, it'll e-mail the address you provided in `sms_config.php`. 
8.  Set up cron, either via the terminal or through CPanel, to run `php /path/to/index.php` as often as you want.

## SMS via e-mail

Most American cell phone providers have special e-mail addresses you can use to receive e-mail. Check this [comprehensive list](http://sms411.net/how-to-send-email-to-a-phone/) for specific information for your carrier.

Many carriers also have a web interface to give you more control over your e-mail-to-SMS address. For example, AT&T allows you to use an alias instead of your phone number, as well as giving you black and white lists for address filtering, at their [message center](http://mymessages.wireless.att.com/)

If, for some reason, your carrier's spam filter blocks your hosts e-mails, whitelist your sending address. If that still doesn't work, create a Gmail account and use it for `$email`. Then use Gmail's settings to forward all mail to your special SMS address. It's a super hacky roundabout pseudo fix, but it works.

## Other options

If you don't want to roll your own system for SMS notification, check out [@MentionNotifier](http://twitter.com/MentionNotifier) or [MyMentions.com](http://www.mymentions.com). MyMentions.com is in private alpha and can (supposedly) text your phone directly. MentionNotifier uses your special e-mail-to-SMS address and works great.