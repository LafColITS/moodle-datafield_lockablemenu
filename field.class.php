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
 * Class definition of lockablemenu datafield.
 *
 * @package    datafield_lockablemenu
 * @copyright  2024 onwards Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../menu/field.class.php');

class data_field_lockablemenu extends data_field_menu {
    /** @var string The internal datafield type */
    public $type = 'lockablemenu';

    /**
     * Output control for editing content.
     *
     * @param int $recordid the id of the data record.
     * @param object $formdata the submitted form.
     *
     * @return string
     */
    public function display_add_field($recordid = 0, $formdata = null) {
        global $DB, $OUTPUT;

        $context = \context_module::instance($this->cm->id);
        if ($this->field->param2 === 'on' && !has_capability('datafield/lockablemenu:manage', $context)) {
            // Readonly mode.
            if ($formdata) {
                $fieldname = 'field_' . $this->field->id;
                $content = $formdata->$fieldname;
            } else if ($recordid) {
                $content = $DB->get_field('data_content', 'content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid));
                $content = trim($content);
            } else {
                $content = '';
            }
            $str = '<div title="' . s($this->field->description) . '">';
            $str .= '<label for="' . 'field_' . $this->field->id . '">';
            $str .= html_writer::span($this->field->name, 'accesshide');
            if ($this->field->required) {
                $image = $OUTPUT->pix_icon('req', get_string('requiredelement', 'form'));
                $str .= html_writer::div($image, 'inline-req');
            }
            $str .= '</label>';
            $str = '<input class="basefieldinput form-control d-inline mod-data-input" ' .
            'type="lockablemenu" name="field_' . $this->field->id . '" ' .
            'id="field_' . $this->field->id . '" value="' . s($content) . '"readonly/>';
            $str .= '</div>';

            return $str;
        } else {
            // Normal mode.
            return parent::display_add_field($recordid, $formdata);
        }
    }

    /**
     * Update the content.
     *
     * We do a set of permissions checks and then punt to the parent class.
     *
     * @param int $recordid the record id
     * @param string $value the content
     * @param string $name field name
     *
     * @return bool
     */
    public function update_content($recordid, $value, $name='') {
        global $DB;

        $context = \context_module::instance($this->cm->id);
        if ($this->field->param2 === 'on' && !has_capability('datafield/lockablemenu:manage', $context)) {
            return true;
        }

        return parent::update_content($recordid, $value, $name);
    }
}