=== Admin Notice Control ===
Contributors: jjmontalban
Donate link:  https://github.com/jjmontalban
Tags: admin, notices, disable, hide, cleanup, dashboard
Requires at least: 5.8
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Tired of a cluttered WordPress dashboard full of nags and promo banners?  
**Admin Notice Control** lets you list every notice added by plugins or themes and hide the ones you don’t want—without touching code and without permanently deleting anything.

* Quickly see **which plugin or theme** registers each admin notice.  
* Hide or re-enable entire groups of notices with a single click.  
* Safe: hidden notices can be restored at any time.  
* Lightweight: no third-party libraries, no front-end impact.  
* Fully i18n-ready (text-domain `admin-notice-control`).

== Features ==

* Detects callbacks attached to `admin_notices` and `all_admin_notices`.
* Groups notices by **source** (`plugin: my-plugin`, `theme: my-theme`).
* One-page UI under **Settings → Admin Notice Control**.
* Stores user choices in a single option (`adminnc_hidden_sources`).
* Cleans up after itself on uninstall.

== Screenshots ==

1. *Settings page listing all sources and their notices.*  
2. *Details of callbacks registered by a plugin.*  
3. *Dashboard after hiding unwanted notices.*

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install it from **Plugins → Add New**.
2. Activate **Admin Notice Control**.
3. Go to **Settings → Admin Notice Control**, review the list and choose *Hide* or *Show* for each source.
4. Click **Save Changes**. Hidden notices disappear instantly.

== Frequently Asked Questions ==

= Does it delete notices permanently? =  
No. It only removes the callback from the hooks at runtime. You can unhide any source later.

= Will it hide WordPress core notices? =  
Core notices are ignored on purpose; the plugin targets notices added by other plugins and themes.

= Multisite compatible? =  
Yes, but settings are **per site** (each sub-site manages its own notices).

== Changelog ==

= 1.0.0 =
* First public release.
* List / hide / restore notices by plugin or theme.
* Snapshot of callbacks for safe rollback.
* Spanish and English translations included.

== Upgrade Notice ==

= 1.0.0 =
Initial release — no special upgrade steps required.
