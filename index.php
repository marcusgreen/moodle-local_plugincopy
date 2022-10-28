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

// $PAGE->requires->js_call_amd('local_plugincopy/copy', 'init');


$pluginman = \core_plugin_manager::instance();
$plugininfo = $pluginman->get_plugins();
echo $OUTPUT->header();
// admin_externalpage_setup('local_plugincopy', '', []);


$contribs = [];
foreach ($plugininfo as $plugintype => $pluginnames) {
    foreach ($pluginnames as $pluginname => $pluginfo) {
        if (!$pluginfo->is_standard()) {
            $contribs['plugins'][]['name'] = $pluginfo->type. '_'.$pluginfo->name .', version, '.$pluginfo->release;
        }
    }
}

echo $OUTPUT->render_from_template('local_plugincopy/pluginlist', $contribs);

echo $OUTPUT->footer();
