=== LNH Flux ===
Contributors: Weysan, CouleurCitron
Tags: LNH, handball, import
Requires at least: 4.2
Tested up to: 4.3
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Can import data from LNH (http://www.lnh.fr) webservices about players, calendar and ranking.
Plugin only in french for the moment.

= Dependency =
You can import datas without any others plugin, but the plugin is working with a framework plugin to add custom fields :
https://github.com/Weysan/wp-framework-backend-form

= Install =
- Download the wordpress plugin.
- Unzip the file in your plugin directory
- Activate the plugin in your backoffice

= How to use =

= Settings =
After activate your plugin, you have to configure it :
- Go to Settings > LNH Settings
- put the team name (i.e. : toulouse)
- Set the stream you want to import (Players, Ranking, Calendar)

= Import Data Mannualy =
You can import manually datas from the LNH stream by clicking on Tools > Import Flux LNH and submit the form.

Players will be imported in "LNH joueurs" content type
Ranking will be imported in "LNH classement" content type
Calendar will be imported in "LNH Matchs" content type

= Display =
The plugin create automatically some widgets to display the previous match and the next match.

You have 2 shortcodes to display the calendar:
<pre>[lnh-calendrier]</pre>

And the ranking :
<pre>[classement saison="20152016"]</pre>
