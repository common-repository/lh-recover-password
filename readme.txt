=== LH Recover Password ===
Contributors: shawfactor
Donate link: https://lhero.org/portfolio/lh-recover-password/
Tags: password, frontend, form, recover password, shortcode
Requires at least: 3.0.
Tested up to: 4.5.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Easily place a HTML5 password recovery form on the front end of your website 

== Description ==

This plugin provides a shortcode to include a HTML5 password recovery form on a page on your website and will natively link to this form throughout your site.

On activation a page with the shortcode [lh_recover_password_form] will be created o0n your site, and it will become your front end password reset form.

To change the recover password url to a page where you have the recover password form navigate to Wp Admin->Settings->LH Recover Password and paste the page id into the field. You can slo modify the email sent for password recovery and also the subject line in the same settinsg page.



Check out [our documentation][docs] for more information. 

All tickets for the project are being tracked on [GitHub][].


[docs]: http://lhero.org/plugins/lh-recover-password/
[GitHub]: https://github.com/shawfactor/lh-recover-password

Features:

* Front end password recovery form inserted via shortcode
* Multiple instances possible: Place form shortcode multiple pages or in sidebars and widgets
* If configured will override the WordPress recvover password url so that recover password links point to to your front end recover password page page (extra security)


== Installation ==

1. Upload the entire `lh-recover-password` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Upload the entire `lh-personalised-content` folder to the `/wp-content/plugins/` directory (this plugin must be activated for the lh-recover-password to work)
4. Activate the plugin through the 'Plugins' menu in WordPress.
5. Navigate to Settings->LH Recover Password if you wish to change or delete the page id, set the email subject line, or configure the email sent.


== Changelog ==

**1.0 November 13, 2015**  
Initial release.


**1.01 November 23, 2015**  
Added icon.

**1.10 December 07, 2015**  
Network activate

**1.12 April 15, 2016**  
Better multisite supprt

**1.13 June 26, 2016**  
Proper namespacing