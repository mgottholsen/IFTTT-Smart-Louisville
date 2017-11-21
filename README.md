# IFTTT Shim API

A Slim 3 PHP middleware API to translates calls between IFTTT and various Louisville Metro Government APIs.

## Installation Instructions
After cloning this repo, there are a couple of preparation steps that must be taken in order to get everything set up.
1. Run the ``composer install`` at the root of the repo, to pull in all the required dependencies
2. A config file is required in order to specify your special environment variables such as API keys and special URLs.
We have provided an ```example.config.php``` file for you to start with. We recommend that you place this file somewhere outside of the project
folder. Usually, you will have three of these files, ```dev.config.php```,```tst.config.php```,```prod.config.php```
which contains the correct variables for each environment.
    
    With that being said, in ```src/dependencies.php```, you will find an ```$env``` variable that is used to load the
    proper environment configuration file, as well as the location of those files. In this example, the configuration
    files are located one level up from the root folder, but you can place it anywhere you'd like and point it to the
    proper location in the ```src/dependencies.php``` file.
3.  You will need to create the tables described in the schema file that we've provided at ```schema/schema.sql```.