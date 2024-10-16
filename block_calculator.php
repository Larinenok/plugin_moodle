<?php
declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

class block_calculator extends block_base {
    public string $formaction;
    public string $name_button;
    public string $url_history;
    public string $name_history;
    public string $placeholder;

    public function init() {
        $this->title = get_string('pluginname', 'block_calculator');
        $this->formaction = (string)(new moodle_url('/blocks/calculator/process.php'));
        $this->name_button = get_string('submit_button', 'block_calculator');
        $this->url_history = (string)(new moodle_url('/blocks/calculator/history.php'));
        $this->name_history = get_string('name_history', 'block_calculator');
        $this->placeholder = get_string('placeholder', 'block_calculator');
    }

    public function get_content() {
        global $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $data = [
            'formaction' => $this->formaction,
            'name_button' => $this->name_button,
            'url_history' => $this->url_history,
            'name_history' => $this->name_history,
            'placeholder' => $this->placeholder
        ];

        $form = $OUTPUT->render_from_template('block_calculator/block_calculator', $data);

        $this->content = new stdClass;
        $this->content->text = $form;

        return $this->content;
    }

    public static function calculate(float $a, float $b, float $c) {
        if ($a == 0) {
            return [null, null, null];
        }

        $discriminant = $b * $b - 4 * $a * $c;
        $x1 = null;
        $x2 = null;

        if ($discriminant == 0) {
            $x1 = -$b / (2 * $a);
        } elseif ($discriminant > 0) {
            $x1 = (-$b + sqrt($discriminant)) / (2 * $a);
            $x2 = (-$b - sqrt($discriminant)) / (2 * $a);
        }

        return [$discriminant, $x1, $x2];
    }

    public static function write_db(float $a, float $b, float $c,
            float|null $discriminant, float|null $x1, float|null $x2) {
        global $USER, $DB;

        $record = new stdClass();
        $record->userid = $USER->id;
        $record->a = $a;
        $record->b = $b;
        $record->c = $c;
        $record->d = $discriminant;
        $record->x1 = $x1;
        $record->x2 = $x2;
        $record->timecreated = time();
        $DB->insert_record('calculator_history', $record);
    }
}
