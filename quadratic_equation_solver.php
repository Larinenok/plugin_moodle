<?php
declare(strict_types=1);

require_once(__DIR__ . '/interfaces.php');

abstract class quadratic_equation_solver_base {
    public const string NAME = 'quadraticequationsolver';

    public static float $a = 0;
    public static float $b = 0;
    public static float $c = 0;
    public static ?float $d = null;
    public static ?float $x1 = null;
    public static ?float $x2 = null;
}

class quadratic_equation_solver_math extends quadratic_equation_solver_base implements solver {
    public function calculate(): array {
        if (self::$a == 0) {
            return [null, null, null];
        }

        self::$d = self::$b * self::$b - 4 * self::$a * self::$c;

        if (self::$d == 0) {
            self::$x1 = -self::$b / (2 * self::$a);
        } elseif (self::$d > 0) {
            self::$x1 = (-self::$b + sqrt(self::$d)) / (2 * self::$a);
            self::$x2 = (-self::$b - sqrt(self::$d)) / (2 * self::$a);
        }

        return [self::$d, self::$x1, self::$x2];
    }
}

class quadratic_equation_solver_db extends quadratic_equation_solver_base implements storage {
    public function write_db() {
        global $USER, $DB;

        try {
            $record = new stdClass();
            $record->userid = $USER->id;
            $record->a = self::$a;
            $record->b = self::$b;
            $record->c = self::$c;
            $record->d = self::$d;
            $record->x1 = self::$x1;
            $record->x2 = self::$x2;
            $record->timecreated = time();
            $DB->insert_record('calculator_history', $record);
        } catch (Exception $e) {
            throw new Exception("Error writing to the database. {$e}");
        }
    }

    public function read_db(): ?array {
        global $DB, $USER;

        $records = $DB->get_records('calculator_history', ['userid' => $USER->id]);

        if (!$records) {
            return null;
        }

        $history = [];

        foreach ($records as $record) {
            $history[] = [
                'a' => $record->a,
                'b' => $record->b,
                'c' => $record->c,
                'd' => $record->d,
                'x1' => $record->x1,
                'x2' => $record->x2,
                'timecreated' => date('Y-m-d H:i:s', (int)$record->timecreated)
            ];
        }

        return $history;
    }
}

class quadratic_equation_solver_view extends quadratic_equation_solver_base implements form_handler {
    // Переменные для главной формы
    private string $formaction;
    private string $namebutton;
    private string $urlhistory;
    private string $namehistory;
    private string $placeholder;

    // Переменные для формы вычисления
    private string $azero;
    private string $nosolution;
    private string $namecalculation;
    private string $nameequation;
    private string $namediscriminant;
    private string $nameresult;

    // Переменные для формы истории
    private string $time;
    private string $titlehistory;

    public function __construct() {
        $this->formaction = (string)(new moodle_url('/blocks/calculator/process.php')) . '?calculatormodule=' . self::NAME;
        $this->namebutton = get_string('submitbutton', 'block_calculator');
        $this->urlhistory = (string)(new moodle_url('/blocks/calculator/history.php')) . '?calculatormodule=' . self::NAME;
        $this->namehistory = get_string('namehistory', 'block_calculator');
        $this->placeholder = get_string('placeholder', 'block_calculator');

        $this->azero = get_string('azero', 'block_calculator');
        $this->nosolution = get_string('nosolution', 'block_calculator');
        $this->namecalculation = get_string('namecalculation', 'block_calculator');
        $this->nameequation = get_string('nameequation', 'block_calculator');
        $this->namediscriminant = get_string('namediscriminant', 'block_calculator');
        $this->nameresult = get_string('nameresult', 'block_calculator');

        $this->time = get_string('time', 'block_calculator');
        $this->titlehistory = get_string('titlehistory', 'block_calculator');
    }

    public function process_request(array $request) {
        self::$a = (float)$request['a'];
        self::$b = (float)$request['b'];
        self::$c = (float)$request['c'];
    }

    public function get_main_form(): string {
        global $OUTPUT;

        $data = [
            'formaction' => $this->formaction,
            'namebutton' => $this->namebutton,
            'urlhistory' => $this->urlhistory,
            'namehistory' => $this->namehistory,
            'placeholder' => $this->placeholder
        ];

        return $OUTPUT->render_from_template('block_calculator/quadratic_equation_solver/main', $data);
    }

    public function get_process_form(): string {
        global $OUTPUT;

        $equation = self::$a . 'x²';

        if (self::$b < 0) {
            $equation .= '-' . abs(self::$b) . 'x';
        } else {
            $equation .= '+' . abs(self::$b) . 'x';
        }

        if (self::$c < 0) {
            $equation .= '-' . abs(self::$c);
        } else {
            $equation .= '+' . abs(self::$c);
        }

        if (is_null(self::$d)) {
            $result = $this->azero;
        } elseif (self::$d < 0) {
            $result = $this->nosolution;
        } elseif (self::$d == 0) {
            $result = 'x = ' . round(self::$x1, 2);
        } else {
            $result = 'x1 = ' . round(self::$x1, 2) . ', x2 = ' . round(self::$x2, 2);
        }

        $data = [
            'namecalculation' => $this->namecalculation,
            'nameequation' => $this->nameequation,
            'equation' => $equation,
            'namediscriminant' => $this->namediscriminant,
            'discriminant' => self::$d,
            'nameresult' => $this->nameresult,
            'result' => $result,
        ];

        return $OUTPUT->render_from_template('block_calculator/quadratic_equation_solver/process', $data);
    }

    public function get_history_form(?array $history): string {
        global $OUTPUT;

        if (is_null($history)) {
            return '';
        }

        $data = [
            'history' => $history,
            'time' => $this->time,
            'titlehistory' => $this->titlehistory,
        ];

        return $OUTPUT->render_from_template('block_calculator/quadratic_equation_solver/history', $data);
    }
}

class quadratic_equation_solver implements calculator_module {
    private quadratic_equation_solver_math $qesmath;
    private quadratic_equation_solver_db $qesdb;
    private quadratic_equation_solver_view $qesview;

    public function __construct() {
        $this->qesmath = new quadratic_equation_solver_math();
        $this->qesdb = new quadratic_equation_solver_db();
        $this->qesview = new quadratic_equation_solver_view();
    }

    // quadratic_equation_solver_math
    public function calculate(): array {
        return $this->qesmath->calculate();
    }

    // quadratic_equation_solver_db
    public function write_db() {
        $this->qesdb->write_db();
    }

    public function read_db(): ?array {
        return $this->qesdb->read_db();
    }

    // quadratic_equation_solver_view
    public function process_request(array $request) {
        $this->qesview->process_request($request);
    }

    public function get_main_form(): string {
        return $this->qesview->get_main_form();
    }

    public function get_process_form(): string {
        return $this->qesview->get_process_form();
    }

    public function get_history_form(?array $history): string {
        return $this->qesview->get_history_form($history);
    }
}
