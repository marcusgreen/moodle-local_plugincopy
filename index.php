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
 * Run the code checker from the web.
 *
 * @package    local_plugincopy
 * @copyright  2022 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');

defined('MOODLE_INTERNAL') || die();
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/plugincopy');
$pluginman = \core_plugin_manager::instance();
$plugininfo = $pluginman->get_plugins();
echo $OUTPUT->header();
echo '<h1> Plugin copy</h1>';
echo '<p>Copy contrib/3rd party plugin files to new location. Must be run from command line</p> ';
//                echo  \core\dataformat::download_data('myfile', $export, array_keys($data[0]), $data);

$contribs = [];
foreach ($plugininfo as $plugintype => $pluginnames) {
    foreach ($pluginnames as $pluginname => $pluginfo) {
        if (!$pluginfo->is_standard()) {
           $info = $pluginfo->type. '_'.$pluginfo->name .', version, '.$pluginfo->release;
           echo $info;
           echo '<br/>';
        }
    }
}
echo $OUTPUT->footer();
