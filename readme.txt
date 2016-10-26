=== Plugin Name ===
Contributors: bretterer, stormpath
Tags: authentication, authorization, auth, stormpath, user, users, login, registration, social, social login
Requires at least: 4.5.0
Tested up to: 4.6.1
Stable tag: 0.1.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Give your WordPress website the power of Stormpath Authentication.

== Description ==

With this plugin, you are replacing your local users with users inside of the Stormpath ecosystem. We have
designed this plugin to look and act the same as the built in user authentication, but give you the power
of Stormpath authentication.


== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/stormpath` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Stormpath screen to configure the plugin
4. Add your api key file (this is the recommended version)
    a. This should not be uploaded with the WordPress media library.  Please store the file in a secure location.
    b. If you can not upload a file outside of WordPress, you can use the api key id and secret directly, however these will be stored in plain text.


== Frequently Asked Questions ==

= Why? =

Our WordPress plugin allows you to share the same users across multiple applications inside or out of WordPress.

= What are the PHP requirements? =

Your sever must run PHP 5.5+

== Screenshots ==

1. Installing this plugin gives you a new option in the `settings` menu for Stormpath.  Here you can add your apiKey.properties file OR
the raw ID and Secret.

== Changelog ==

= 0.1.6 =
* Reporting for the plugin version to Stormpath updated.

= 0.1.5 =
* Another Auto Deployment Bug Fix

= 0.1.4 =
* Auto Deployment Bug Fix

= 0.1.3 =
* Auto Deployment to WordPress setup

= 0.1.2 =
* Patch Update for empty() usage in PHP versions 5.5 and below

= 0.1.1 =
* Current users will be created in Stormpath at next login
* Masked login errors to prevent easier attempts

= 0.1.0 =
* Initial Release

== Upgrade Notice ==

= 0.1.6 =
Update was made to the user agent so Stormpath knows the requests are coming from WordPress and which version of
the plugin.

= 0.1.1 =
More robust error handling on accounts not existing in Stormpath.

= 0.1.0 =
This version is the initial release and should be added to your site immediately.

== Stormpath ==

Stormpath is a complete and easy Identity management API for software teams building web, mobile, and API-driven applications. Powerful, pre-built authentication and user management eliminates the cost and security risks of developing and maintaining Identity in house. With Stormpath, developers can launch applications faster and focus on the core features that make their projects a success.

Stormpath is a cloud-based user data store with a private deployment option. Features include user registration, authentication, authorization, user profiles, single sign-on, multi-tenancy, token authentication, and API key management. Stormpath’s advanced security measures safeguard user data and promote compliance. The service includes robust open source SDKs for popular web and mobile frameworks, including Node.js, AngularJS, Java, PHP, Python, Ruby, .NET, iOS, and Android.

The Stormpath REST API lets developers quickly and easily build a wide variety of user management functions they would otherwise have to code themselves.
