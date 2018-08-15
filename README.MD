# Composer Converter

This is an initial attempt to create a script that will convert an existing Drupal installation that does not use 
composer to use composer.

## Usage

Right now it's pretty wonky. This is a sandbox project with many aspirations. 

Generate a list of installed modules on your site using $ drush pml --format=json. Set the output of that to the 
$modules array in this script. Fill in the rest of the options as necessary. Once the options are set, run the script 
with php in the command line, and it should set up a composer project based in the directory you defined. 