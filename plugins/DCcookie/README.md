# Integration Guide
- - -
## 0. Requirements
In order to compile the files and run gulp you need a installation of nodejs (that works for the project) 
## 1. Setup Files

- Copy all contents from the DCCookie folder into /plugins/DCcookie in the project
- Go into the terminal and navigate into this folder using ``cd plugins/DCcookie`` in your command line
- Run ``npm install``
- - -
## 2. One time code adjustments

TL;DR Search for all places where external scripts are called and integrate the consent logic

Generic method:
- Search for all occurrences where the external script/frame is called/used
- Adjust according to the provider:
    - Javascript:
      
        Add ``type="text/plain"`` and ``class="DCCOOKIE_SCRIPT_CLASS"`` to the script-tag (DCOOKIE_SCRIPT_CLASS from provider)
      
        If the script-tag contains javascript-code itself make sure that ``runscript = true`` is set in the provider settings
      
        example:
        `````html
        <script src="https://www.googletagmanager.com/gtag/js?id=XXX>"></script>
        `````
        becomes
        `````html
        <script type="text/plain" class="DCCookie_google_analytics" src="https://www.googletagmanager.com/gtag/js?id=XXX>"></script>
        `````
    - Dependent Scripts:
    
        It's possible, that a script is referencing imported scripts directly. The problem here is the load order and some asynchronize behavior leading to "not defined" errors.\
        In such cases it's possible to use a retry handler that constantly checks if the script and with it the variable it's depending on is loaded:
        ````javascript
        <script class='DCCOOKIE_SCRIPT_CLASS' type='text/plain'>
            __uniqueDynamicFunctionName__();
            
            function __uniqueDynamicFunctionName__() {
                if (typeof __variableYouAreDependingOn__ !=== 'undefined') {
                // original code
                } else {
                window.setTimeout(function() {
                        __uniqueDynamicFunctionName__();
                    }, 100);
                }  
            }
          </script>
        ````
      Replace everything with underscores according to the implementation and set the correct DCCOOKIE_SCRIPT_CLASS. An example for this is google maps in the standard.
      
    - IFrames:
      
        Change the tag to a div and add ``class="DCCOOKIE_IFRAME_CLASS"`` (DCOOKIE_IFRAME_CLASS from provider)\
        example:
        ````html
        <iframe id="<?= $youtube_id ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>"
                src="//www.youtube.com/embed/<?php echo $youtube_id; ?>?wmode=transparent&rel=0"
                allowfullscreen></iframe> 
        ````
        becomes
        ````html
        <div class="DCCookie_youtube" id="<?= $youtube_id ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>"
                src="//www.youtube.com/embed/<?php echo $youtube_id; ?>?wmode=transparent&rel=0"
                allowfullscreen></div> 
        ````
    
    - External Media:
    
        If you want the external media button to appear where content should be you need to add the ``externalMediaContainerClass`` to the container element\
        example:
        ````html
        <div id="map_canvas_<?= $sitepart_id ?>"></div> 
        ````
        becomes
        ````html
        <div class="googlemaps_content DCCookie_google_maps_container" id="map_canvas_<?= $sitepart_id ?>"></div> 
        ````
        This can vary vastly depending on the provider or how it's implemented. For IFrames this can usually be added the the iframe itself 
    
    
Standard known places to adjust:
- Google Analytics
    - initialization in ``google_analytics.inc.php``
    - search for all occurrences of ``ga(`` (only adjust the ones from Google Analytics (there might be hits in ckfinder, do NOT adjust them!))
- Youtube
    - ``show_youtube.inc.php``
- Google Tag Manager
    - initialization ``google_analytics.inc.php``
- Google Maps\
    ``show_googlemaps.inc.php``
    The code for this is rather complicated since a custom JS function depending on external scripts. There is a patch for it in the meta folder.\
    If the patch doesn't work for the given project refer to the standard version and adjust accordingly (see above "dependent script")
- Facebook Pixel
    - initialization in ``facebook_pixel.inc.php``
    - search for all occurrences of ``fbq(``
- Facebook Connect
    - ``show_facebook.inc.php``

It is possible that the standard providers are used in different places from prior adjustments. All occurrences must be covered.
    
Add this to ``frontend_functions.inc.php``:
```php
    /**
     * @param $file
     * @return string
     */
    function getFileWithModifiedTime($file) {
        $absolute_file_path = dirname(__DIR__, 2) . $file;
        $filetime = filemtime($absolute_file_path);
        if((int)$filetime > 0) {
            $file .= '?t='.$filetime;
        }

        return $file;
    }
```
- - -
## 3. Styles

The styles can be found in ``/meta/dc-cookie.less``

Depending on the project there are two ways to include the styles:
1. For newer projects with less files and gulp:
    - Copy the ``dc-cookie.less`` file into all necessary /layout/frontend/**CODE**/src/less/app folders
    - If present delete ``cookiebar.less`` there
    - Edit ``style.less``:
        - Add this under ``//core``: 
            ````less 
            @import "dc-cookie";
            `````
        - Remove this if present: 
            ````less 
            @import "cookiebar";
            `````
   - Run Gulp Public
   - If you have problems getting this to work ask the frontend developer responsible for the project for help (this can be very customized depending on the age of the project)
2. For older projects:
    - Adjust the ``dc-cookie.less`` file:
      - Comment out lines 1-5 and comment in lines 7-11
      - Change the colors in lines 7-11 to be the same as the project\
        (Usually you can find them in some core css files, but this step can vary greatly depending on the project, ask frontend development for help if neccessary)
    - Compile the less file yourself (This can be easily done by an online tool, e.g. https://beautifytools.com/less-compiler.php)
    - Reference the css file in the ``<head>`` section of the frontend files where needed 
        ```php
            <?
            // get last modified file time to prevent cache for changes
            $cssFile = "/plugins/DCcookie/dc-cookie.css";
            ?>
            <link type="text/css" rel="stylesheet" href="<?=getFileWithModifiedTime($cssFile);?>">
        ```
    - If multiple sites with different colors are present you need to make copies of the less file accordingly and adjust every copy individually
- - -
## 4. Frontend Files

The cookie banner must be linked separately in every frontend file. 

For the configuration to be loaded correctly, the ``<body>`` tag needs the data for the site code and language code. You can either add it manually:\
```data-lang_code="<?=$GLOBALS["language"]["code"]?>"```\
```data-site_code="<?=$GLOBALS["site"]["code"]?>"```\
or use this search and replace regex:
````
<body (.*)>
<body $1 data-site_code="\<\?\=\$GLOBALS\["site"\]\["code"]\?\>" data-lang_code="\<\?\=\$GLOBALS\["language"\]\["code"\]\?\>"\>
````
Make sure to check the result when using the regex!\

Afterwards you need to add the cookie banner itself to the same frontend file. In order to be compatible with IE it's necessary that this occurs __after__ the closing ``</body>`` tag.
````php
   <?
    // get last modified file time to prevent cache for changes
    $jsFile = "/plugins/DCcookie/dist/main.min.js";
    $ieFile = "/plugins/DCcookie/dist/ie.min.js";
    ?>
    <script type="text/javascript">
        setTimeout(function() {
            var element = document.createElement('script');
            var src = "";
            if (window.navigator.userAgent.indexOf("MSIE ") > 0 || (!!window.MSInputMethodContext && !!document.documentMode)) {
                src = "<?=getFileWithModifiedTime($ieFile);?>";
            } else {
                src = "<?=getFileWithModifiedTime($jsFile);?>";
            }
            element.setAttribute('src', src);
            document.getElementsByTagName('html')[0].appendChild(element);
        },250);
    </script>
````
- - -
## 5. Statistics

All necessary files can be found in /meta/statistics.
1. Import ``tracking_table.sql`` into the database
2. Select either ``DC statistics.php`` or ``MYSYDE_PHP7 statistics.php`` depending on your project (if you run mysyde with php 5 you need additional libraries for bin2hex and random_bytes functions)
    - Copy the selected file into the root folder of DCCookie in the project (/plugins/DCCookie/)
    - Rename it into ``statistics.php``
3. Apply either ``DC_dcCookie_statistics.patch`` or ``MYSYDE_dcCookie_statistics.patch`` depending on your project
   (If this fails, the changes shouldn't be too hard to apply manually (New entry in admin menu and a new file to display the statistics). See existing implementations if necessary: Jako, CutMetall, Reusch)

- - -
## 6. (Data) maintenance
This is the main part that might need to be adjusted later down the line. It's still possible that you need to do other code adjustments according to (2.) if you have a new provider. If a new frontend file gets added you might need to do (4.) again.
### Configuration
The main configuration is done in the ``index.js`` file. All variables need to be adjusted according to the given project.\
``lastReset``, ``showRecurrent``, ``templateNo``,``forceActive``,``enableStatistics``, ``availableLanguages`` and ``defaultLanguage`` should be self explanatory (see comments)

- ``siteCode`` and ``languageCode``\
    These values get initialized from the give body-element of the current site. Usually the`y don't need to be adjusted. The only thing that might happen for multi-site and multi-language projects is a different 
    language code used for the same real language. E.g. ``deu`` and ``de`` are used at the same time for different sites. If that's the case you need to map the different codes to one. According to the final ``languageCode`` the 
    localization file will be loaded. 
- ``identifierCode``\
    This code identifies consent groups. Every consent group maintains its own settings from the users.
    If you for example want a b2b and login to only ask the user once they need to be put into the same consent group, since the consent information from the user is saved for a given group.
    You want to have the same providers for all sites/languages within the same group (=same identifierCode)! It is possible to ignore that if you want to. Be aware that the user won't be asked for consent 
    again within the same group even if the providers differ! It can be empty so all site/languages are in the same group.\
    Different groups als enable different localizations. If you want to use an alternative translation you can provide new translation files with the identifierCode appended to the end after an underscore:
    e.g. ``de_CODE.json``. If not provided the default language will be loaded instead.
- ``altImprintLink`` and ``altPrivatePolicyLink``\
    If multiple sites/langauges are mapped to the same identifierCode you need to provide differing linkes for the imprint and private policy for each site. The alternative links need to be provieded per language:\
    e.g. 
    ````javascript
    altImprintLink.de = 'https://www.my-site.com/de/imprint_link';
    altImprintLink.en = 'https://www.my-site.com/en/imprint_link';
    ````
    If none given the default link from the localization file will be used. 
- ``providers``\
    This is the main provider array. Here all used providers must be listed by their respective id from ``CookieProvider.js``.\
    e.g.
    ````javascript
    providers = [
        "cookie_consent",
        "google_analytics",
        "google_maps",
        "youtube"  
    ];
    ````
 
__For complex multi-site multi-language projects it's usually the best idea to do a switch over ``siteCode`` and adjust all settings individually for each site__\
Good examples for that are Jako, Reusch, CutMetall

After every adjustment to the ``index.js`` file you need to compile everything:\
Navigate into ``plugins/DCcookie`` and make sure you have run ``npm install`` before.
Then execute:
````shell 
npm run compile
````

### Localization

In order to let the customer handle the localization into his custom languages there is an CSV exporter and importer in the /meta/ folder.\
All that needs to be done is adjust the first few lines of the ``exporter.js`` for the given languages (if you need a new language just copy the english version and rename the copy)
You can now adjust all necessary translations within the ``translate.csv`` file.\
Once you recieved the translated CSV file you can simply place it in the /meta/ folder. Adjust the first few lines of the ``importer.js`` to match the paths/filename if necessary.
Afterwards just go into the json Files and hit auto-format (default hotkey Ctrl+Alt+L). Check the file afterwards for errors!\
You can run the exporter/importer easly with nodejs. If you're using PHPStorm simply go into the file and hit Ctrl+Shift+F10 (default hotkey). This will generate the default run environment for you.\
__Very important:__ The translated CSV file needs to be a semicolon separated UTF-8 file! Otherwise you will get weird symbols. If that's not the case you can open the file with excel, hit "save as" and select "CSV UTF-8 (separated by semicolon)" as the file type. (German: "CSV UTF-8 (durch Trennzeichen getrennt)")

### Google Tag Manager

Request access to __all__ used containers if they are not already managed by us (Online Marketing). The adjustments need to be done within all utilized containers!\

For each used provider you firstly need to create a new trigger:
- Name it "&lt;gtmVariable&gt; Trigger" with the &lt;gtmVariable&gt; used from the given provider in ``CookieProviders.js``
- As a type select "userdefined event"
- The eventname has to be __exactly__ "&lt;gtmVariable&gt;Event" (no Spaces! Case sensitive!) with the &lt;gtmVariable&gt; used from the given provider
- Leave every thing else as default

Example for Google Analytics:

    Trigger name: DCCookieGoogleAnalytics Trigger
    Trigger type: userdefined event
    Event name: DCCookieGoogleAnalyticsEvent

Now you need to create Trigger Groups for all used triggers with their respective consent trigger (the one from above). This is easier explained with an example:\
Given the Tag "GA - Click Kontaktanfragen". It's from the type "Google Analytics: Universal Analytics" and has a trigger called "Click - Kontaktanfragen Trigger".\
You now need to create a new trigger group with this trigger and the DCCookieGoogleAnalytics Trigger combined. Just put both the triggers in a group and call it "DCCookieGoogleAnalytics + Click - Kontaktanfragen Trigger".
Now replace the original trigger from the tag with the new trigger group.\
If multiple triggers are present on a tag you have to create a trigger group for each __individually__ (DON'T combine them in one group!) and replace all of them.


The only exception to the trigger group is a trigger called "page visit". This can just be replaced by the initial consent trigger itself (e.g. "DCCookieGoogleAnalytics Trigger") and doesn't need to be combined within a trigger group.

It's highly recommended to use existing implementations as an example: CutMetall, Jako, Reusch, Minilu...\
Be aware that some tags/triggers are very complex and need special custom replacements that cannot be covered by simple trigger groups. A good example for that is the Jako "Scrolltiefe" or the minilu "Enhanced E-Commerce"-Tracking.\
It's always very important to test all tags/triggers and think about every tag/trigger individually before replacing it! (This can be really hard and annoying sometimes)

### Private policy 
For every language that the consent management is active there needs to be a button within the private polacy page. Here are some buttons for the provided languages:
````html
DE: <a class="dcCookieBar__button dcCookieBar__Navigation_Button" href="javascript:void(0);">Hier k&ouml;nnen Sie Ihre Cookie-Einstellungen bearbeiten</a>
EN: <a class="dcCookieBar__button dcCookieBar__Navigation_Button" href="javascript:void(0);">Here you can change your cookie settings</a>
FR: <a class="dcCookieBar__button dcCookieBar__Navigation_Button" href="javascript:void(0);">Ici, vous pouvez modifier vos paramètres de cookies</a>
PL: <a class="dcCookieBar__button dcCookieBar__Navigation_Button" href="javascript:void(0);">Tutaj możesz edytować swoje ustawienia dotyczące plików cookie</a>
````