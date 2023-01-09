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

require_login();

defined('MOODLE_INTERNAL') || die();
use \core\dataformat;

// $PAGE->requires->js_call_amd('local_plugincopy/copy', 'init');

$download = optional_param("download", '', PARAM_BOOL);

$pluginman = \core_plugin_manager::instance();
$plugininfo = $pluginman->get_plugins();

global $DB, $CFG;
$data = [];
$data['serverinfo']['os'] = php_uname('s');
$data['serverinfo']['phpversion'] = phpversion();
$data['serverinfo']['database'] = $DB->get_dbfamily();
$data['serverinfo']['webserver'] = $_SERVER['SERVER_SOFTWARE'];
$data['serverinfo']['moodleversion'] = $CFG->release;
$data['serverinfo']['post_max_size'] = ini_get('post_max_size');
$data['serverinfo']['upload_max_filesize'] = ini_get('post_max_size');
$data['serverinfo']['max_execution_time'] = ini_get('max_execution_time');
$data['serverinfo']['dbname'] = $CFG->dbname;
$data['serverinfo']['xdebug_client_port'] = ini_get('xdebug.client_port');

$records = [];
foreach ($plugininfo as $plugintype => $pluginnames) {
    foreach ($pluginnames as $pluginname => $pluginfo) {
        if (!$pluginfo->is_standard()) {
            $data['plugins'][]['name'] = $pluginfo->type. '_'.$pluginfo->name .', version: '.$pluginfo->versiondb;
            $records[] = [$pluginfo->name, 'version' => $pluginfo->versiondb ];
        }
    }
}
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/plugincopy');


$html = $OUTPUT->render_from_template('local_plugincopy/pluginlist', $data);

send_output($html, $download, $records);

/**
 * Send output either to the browser or
 * to a file download
 *
 * @param string $html
 * @param string $dload
 * @param array $data
 * @return void
 */
function send_output(string $html, string $download, array $data) : void {
    global $OUTPUT, $PAGE;
    if ($download) {
        download('plugins', 'excel', ['name', 'version'], $data);
        echo $OUTPUT->header();

        echo $html;
    } else {
        $PAGE->set_pagelayout('standard');
        echo $OUTPUT->header();

        echo $html;
    }
    echo $OUTPUT->footer();
}

function download($filename, $format, $columns, $records) {
    dataformat::download_data($filename, $format, $columns, $records);
    exit();
}
