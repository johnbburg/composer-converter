<?php 

// This would be the output of $ drush pml
$moules = array();



// The path to the resulting script to run composer commands.
// TODO: Pass these in as arguments.
$path = '/home/jbrandenburg/projects/composerify-test/composerize-test.sh';
$core_version = '8.4.8';
$core_version_minor =  substr($core_version, 0, 2);// '8.4';
$webroot = 'web';

// Remove any pre-existing version of the file.
unlink($path);

// Initialize the file.
$file = fopen($path, 'w');
$return = fwrite($file, "#!/bin/bash\n\nset -x\n\n");

// If we successfully initialized the file.
if ($return) {

  // Write the initial create project composer commands.
  fwrite($file, "composer create-project drupal-composer/drupal-project:~{$core_version_minor} {$webroot} --stability dev --no-interaction\n\n");

  // Without installer-paths present in composer.json, we need to be in the
  // Drupal root to run composer require commands for modules.
  fwrite($file, "cd {$webroot}\n\n");

  // Non-module components.
  fwrite($file, "composer require composer/installers --update-with-dependencies --ignore-platform-reqs \n\n");
  fwrite($file, "composer require wikimedia/composer-merge-plugin --update-with-dependencies --ignore-platform-reqs \n\n");
  fwrite($file, "composer require drush/drush --update-with-dependencies --ignore-platform-reqs \n\n");
  fwrite($file, "composer require drupal/console --update-with-dependencies --ignore-platform-reqs \n\n");


  // Loop over modules. This is the output of drush pml --format=var_dump
  foreach ( $modules as $mod_name => $info) {

    // If the module is enabled,
    // is not a theme (although re0think this since themes can be installed via composer)
    // Is not a core package
    // And doesn't have the same "version" as core, hard-coded here, and probable redundant to the above check.
    if($info['status'] == 'Enabled' && $info['type'] != 'Theme' && $info['package'] != 'Core' && $info['version'] != $core_version) {

      // The module release version
      $version = $info['version'];

      // Remove the core version component of the module version string.
      $version = str_replace('8.x-', '', $info['version']);
      if ($info['package'] == 'Core' || $version == $core_version) {
        // Do we really need this? Should we just not run composer require on core modules.
        //$version = '';
        continue;
      }

      // Prepend a colon so it ends up being module_name:version
      if ($version != '') {
        $version = ":".$version;
      }

      // Write the command to the script
      fwrite($file, "# {$info['name']}\n");
      fwrite($file, "composer require drupal/". $mod_name  . $version." --update-with-dependencies --ignore-platform-reqs \n\n");
    }
  }
}

// Close the stream.
fclose($file);

// Make it executable.
chmod($path, 0777);


