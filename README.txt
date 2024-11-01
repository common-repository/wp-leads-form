=== WP Leads Form ===
Contributors: itpixelz
Donate link: https://profiles.wordpress.org/itpixelz/
Tags: leads, crm
Requires at least: 3.0.1
Tested up to: 5.2.2
Stable tag: trunk
Requires PHP: 5.2.4+
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Mini CRM which collects leads from form and saves into the database as post type.
== Description ==

Collect leads from leads form and save data into the database, manage them by tags and categories for later use or record.

== Installation ==

1. Upload `wp-leads` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place the shortcode `[wp-leads-form]` in your posts/pages or place `<?php echo do_shortcode('[wp-leads-form]'); ?>` in your template files
4. The lead form can be customized with available attributes as below
 `[wp-leads-form label-name="My Name" label-email="My Email" label-message="My Message" label-budget="My Budget" label-phone="My Phone" rows-message="2" cols-message="3" max-name="30"]`
5. Available attributes with their default values as below
   `label-name`
       (string) label name for the field "name"
       Default value: Name (required)

   `label-phone`
       (string) label name for the field "phone"
       Default value: Phone Number

   `label-email`
      (string) label name for the field "email"
      Default value: Email Address (required)

   `label-budget`
      (string) label name for the field "budget"
      Default value: Desired Budget

   `label-message`
      (string) label name for the field "message"
      Default value: Message

   `max-name`
      (int) maximum length for the field "name"
      Default value: 70

   `max-phone`
      (int) maximum length for the field "phone"
      Default value: 30

   `max-email`
      (int) maximum length for the field "email"
      Default value: 50

   `rows-message`
      (int) rows attribute for textarea field for "message"
      Default value: 5

   `cols-message`
      (int) cols attribute for textarea field for "message"
      Default value: 50


== Screenshots ==

1. Lead form
2. Leads in WordPress admin area as post_type
3. Single lead in WordPress admin area
