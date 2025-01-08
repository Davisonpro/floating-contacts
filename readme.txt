=== Floating Contacts ===
Contributors: davisonprodev
Tags: contact, floating button, whatsapp, email
Requires at least: 5.2
Tested up to: 6.7
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A customizable floating contact button for your WordPress site, allowing visitors to easily reach you through various communication channels.

== Description ==

Floating Contacts adds a customizable floating contact button to your WordPress website. This plugin allows your visitors to easily reach you through various communication channels such as Live Chat, Phone, Email, and WhatsApp.

Key features:

* Customizable floating contact button
* Support for multiple contact methods: Live Chat, Phone, Email, and WhatsApp
* Responsive design for both desktop and mobile devices
* Easy configuration through the WordPress admin panel
* Font Awesome icon integration for visual appeal

== Installation ==

1. Upload the `floating-contacts` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Floating Contacts to configure the plugin

== Frequently Asked Questions ==

= Can I customize the button colors? =

Yes, you can customize the background color and icon colors through the settings page.

= Is the plugin responsive? =

Yes, the Floating Contacts widget is designed to work well on both desktop and mobile devices.

== Screenshots ==

1. Floating contact button on website
2. Admin settings page

== External services ==

This plugin connects to the WhatsApp API to generate a contact link for the floating contact button. It is needed to allow users to quickly contact via WhatsApp when they click the button.

It sends the phone number configured in the plugin settings to the WhatsApp API each time the floating contact button is displayed. The API is called when the user interacts with the button.

Service: WhatsApp API
What data is sent: The phone number configured in the plugin settings.
When the data is sent: Every time the floating contact button is loaded or clicked.

Service provider: WhatsApp Inc.
- Terms of Service: https://www.whatsapp.com/legal/business-terms/
- Privacy Policy: https://www.whatsapp.com/legal/privacy-policy/

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release of Floating Contacts.