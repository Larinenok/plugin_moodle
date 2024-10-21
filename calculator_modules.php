<?php
declare(strict_types=1);

require_once(__DIR__ . '/calculator_wrapper.php');
require_once(__DIR__ . '/quadratic_equation_solver.php');

/**
 * @return calculator_wrapper[] $calculatormodules
 */
function get_calculator_modules(): array {
    /**
     * @var calculator_wrapper[] $calculatormodules
     */
    $calculatormodules = [
        'quadraticequationsolver' => new calculator_wrapper(
            new quadratic_equation_solver_data(),
            new quadratic_equation_solver_math(),
            new quadratic_equation_solver_db(),
            new quadratic_equation_solver_view(),
        ),
        // другие модули
    ];

    return $calculatormodules;
}
