=== Nutrition Facts Vitamins ===
Contributors:  dandelionweb
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JEMTB4U8SYFL6
Tags: nutrition facts label, food, nutrition, nutrition facts, nutrition label, nutrition labelling, vitamins, nutrition facts table, FDA
Requires at least: 3.0
Tested up to: 4.6
Stable tag: trunk
Version: 3.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use this free WordPress plugin to create Nutrition Facts Labels with vitamins.

== Description ==

This plugin creates a 'Label' custom post type which can be assigned to any page or post.

* Includes Vitamins D, Calcium, Iron and Potassium
* "Not a Significant source of _____" line will be generated for blank fields
* Add user generated additional vitamins
* Use the shortcode [nutrition-label id=XXX] to display a nutrition label.
* When creating the label you can also specify the page/post and use shortcode [nutrition-label] to display the nutrition label that has been attached to the page/post.
* Developers can add do_shortcode('[nutrition-label]') to their templates.
* For a Bilingual Label see my Canadian Nutrition Facts Label plugin - https://wordpress.org/plugins/canadian-nutrition-facts-label/

= Donations: =
I appreciate all donations, no matter the size. Further development of this plugin is not contingent on donations, but they are a nice incentive. To donate click on the "donate to this plugin" link in the sidebar below the Authors.

== Installation ==

1. Install via the WordPress.org plugin directory or by uploading the nutrition-facts-vitamins folder to the /wp-content/plugins directory

2. Activate the plugin through the Plugins menu in WordPress

3. Create a label in the new 'Labels' custom post type section of the admin. See units instructions for assistance.

4. Include the shortcode [nutrition-label id=XXX] where you want a specific label to be displayed. Change id=XXX to the specific id number for your label.

5. DEVELOPERS: include echo do_shortcode("[nutrition-label]"); in your page template. Then specify the Page or Post when creating the label.

== Frequently Asked Questions ==

= What's the shortcode for this plugin? =
[nutrition-label] or [nutrition-label id=XXX]  Change id=XXX to the specific id number for your label.

= How do I include the nutrition label in a page template. =
Include echo do_shortcode("[nutrition-label]"); in your page template. Then specify the Page or Post when creating the label.

= How do I add additional vitamins? =
See screenshot #7 - you click Add New Vitamin then edit the field label to give the vitamin a name

= How do I get the line "Not a significant source of ____" to work? =
Leave a vitamin blank and it will be added to the line.  If you put a 0 in the field, it will not be added. You can also add new vitamins, label them in the order you want them to appear, and leave the field blank. Such as "Not a significant source of vitamin C, or thiamine."

== Screenshots ==

1. Install and activate the plugin.  Then navigate to the new "Labels" custom post type in the admin.

2. Add/Edit Label Form - select a page/post to attach the label.  Then simply fill in all the fields

3. Include 'do_shortcode' in your template.  It will search for the first label attached to that page.

4. Add a label with the shortcode and label id [nutrition-label id=XXX].

5. Example generated label

6. Generated label with Vit C left blank showing as "Not a significant source of vitamin C" and with Thiamine added

7. Example how to add additional vitamins

== Upgrade Notice ==
= 4 =
Added polyunsaturatedfat and monounsaturatedfat declarations to the nutritional label.
= 3 =
CAUTION: version 3.0 of this plugin changes the Nutrition Label to the NEW FDA format. Only update the plugin if you want this new format. Includes nutrients D, Calcium, Iron and Potassium. See http://www.fda.gov/downloads/Food/NewsEvents/WorkshopsMeetingsConferences/UCM508389.pdf for a detailed description of what's changed.

* Servings: larger, bolder type
* Serving sizes updated
* Calories: larger type
* Updated daily values
* New: added sugars
* Change in nutrients required, Vitamins A & C removed, Vitamin D & Potassium added, actual amounts declared
* New footnote

= 2.1.1 =
* minor fix of Not a significant source of line and add missing space after Calories from fat
= 2.1 =
* localization
= 2 =
* Add ability for user generated additional vitamins
* If a field is left blank add it to text summary line "Not a significant source of"
* Separated CSS from the main plugin file. Also directories are added for css, js and images.
= 1 =
* Initial Release, forked and updated from easy-nutrition-facts-label plugin

== Changelog ==
= 3 =
CAUTION version 3.0 of this plugin changes the Nutrition Label to the NEW FDA format. Only update the plugin if you want this new format. Includes nutrients D, Calcium, Iron and Potassium. See http://www.fda.gov/downloads/Food/NewsEvents/WorkshopsMeetingsConferences/UCM508389.pdf for a detailed description of what's changed.

* Servings: larger, bolder type
* Serving sizes updated
* Calories: larger type
* Updated daily values
* New: added sugars
* Change in nutrients required, Vitamins A & C removed, Vitamin D & Potassium added, actual amounts declared
* New footnote

= 2.1.1 =
* minor fix of Not a significant source of line
= 2.1 =
* localization
= 2 =
* Add ability for user generated additional vitamins
* If a field is left blank add it to text summary line "Not a significant source of"
* Separated CSS from the main plugin file. Also directories are added for css, js and images.

= 1 =
* Initial release
