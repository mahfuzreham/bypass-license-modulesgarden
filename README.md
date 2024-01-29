# Bypass License Modules Garden

![ModulesGarden](https://raw.githubusercontent.com/jesussuarz/bypass-license-modulesgarden/main/images/logo.svg)


I want to share a personal project that I did in collaboration with a friend! This is a modification of a .php file that allows you to "re-license" ModulesGarden.com modules.

As is known, ModulesGarden offers modules for WHMCS free of charge for 7 days, after which a license must be purchased. Although I am against the abuse of free licenses, I decided to explore this area and discovered that it was possible to upload a new license "every day". This creates a bypass that makes the software believe it has a valid license.

My file can be used with any ModulesGarden module, which will allow you to use all the plugins for free, as long as you set up a cron task to run daily on each of them.

## How to use?

### Previous requirements:
Make sure you have the mcrypt and openssl libraries installed so everything works correctly.

### Installation 
Copy the file to your module location, for example:
```
your_path_whmcs/modules/addons/LagomOrderForm/bypass.php
```

Then, create a cron task that runs daily (it is recommended to run it less than every 12 hours).

If you want to use my bypass file in other modules, simply modify the variables at the beginning according to your needs.
```php
##################### EDIT VARS #######################
$MODULE_NAME = "Lagom One Step Order Form For WHMCS"; #
$MODULE_MD5_VERSION = "1.2.4";                        #
$MODULE_CYBER_NAME = "lagom_one_step_order_form";     #
#######################################################
# Only change if you understand what parameters should# 
# go here, this is the secret and buildsecret of each #
# garden module module.                               #           
#######################################################
$SECRET = "*******************************";          # 
$BUILDSECRET = "**************";                      #
#######################################################
```


I hope this tool will be useful to you to get the most out of ModulesGarden modules in a responsible and ethical way! If you have any questions or comments, feel free to leave them here.
