<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * copy contrib plugins
 *
 * @package    local_plugincopy
 * @copyright  2022 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);
require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');

$pluginman = \core_plugin_manager::instance();
$plugininfo = $pluginman->get_plugins();
$params = cli_get_params([], []);

$destination = $params[1][0];

if (!is_dir($destination)) {
    cli_writeln($destination." is not a folder");
    return;
}
if (!is_writeable($destination)) {
    cli_writeln("cannot write to " .$destination);
    return;
}

$contribs = [];
foreach ($plugininfo as $plugintype => $pluginnames) {
    foreach ($pluginnames as $pluginname => $pluginfo) {
        if (!$pluginfo->is_standard()) {
            $contribs[$plugintype][$pluginname] = $pluginfo;
        }
    }
}
foreach ($contribs as $plugintype) {
    foreach ($plugintype as $plugin) {
        cli_writeln($plugin->name);
        recurse_copy($plugin->rootdir, $destination.DIRECTORY_SEPARATOR.$plugin->name);
        return;
    }
}
function recurse_copy($src, $dst) {

    $dir = opendir($src);
    $result = ($dir === false ? false : true);

    if ($result !== false) {
        $result = @mkdir($dst);

        if ($result === true) {
            while (false !== ( $file = readdir($dir)) ) {
                if (( $file != '.' ) && ( $file != '..' ) && $result) {
                    if ( is_dir($src . DIRECTORY_SEPARATOR . $file) ) {
                        $result = recurse_copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                    } else {
                        $result = copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                    }
                }
            }
            closedir($dir);
        }
    }

    return $result;
}
