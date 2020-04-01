<?php
/**
 * Driver file contains Math_Numerical_RootFinding_Bisection class to provide
 * Newton-Raphson 2 method root finding calculation.
 *
 * PHP versions 4 and 5
 *
 * LICENSE:
 * Copyright (c) 2008 Firman Wandayandi <firman@php.net>
 *
 * This source file is subject to the BSD License license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to pear-dev@list.php.net so we can send you a copy immediately.
 *
 * @category   Math
 * @package    Math_Numerical_RootFinding
 * @subpackage Methods
 * @author     Firman Wandayandi <firman@php.net>
 * @copyright  Copyright (c) 2004-2008 Firman Wandayandi
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://pear.php.net/package/Math_Numerical_RootFinding
 * @version    CVS: $Id$
 */

/**
 * Newton-Raphson 2 method class.
 *
 * @category   Math
 * @package    Math_Numerical_RootFinding
 * @subpackage Methods
 * @author     Firman Wandayandi <firman@php.net>
 * @copyright  2004-2008 Firman Wandayandi
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://pear.php.net/package/Math_Numerical_RootFinding
 * @version    Release: @package_version@
 */
class Math_Numerical_RootFinding_NewtonRaphson2
extends \Math_Numerical_RootFinding_Common
{
    // {{{ Constructor

    /**
     * Constructor.
     *
     * @param array $options (optional) Options.
     *
     * @access public
     * @see Math_Numerical_RootFinding_Common::Math_Numerical_RootFinding_Common()
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    // }}}
    // {{{ infoCompute()

    /**
     * Print out parameters description for compute() function.
     *
     * @access public
     * @return void
     */
    public function infoCompute()
    {
        print "<h2>False Position::compute()</h2>\n" .

              "<em>float</em> | <em>PEAR_Error</em> " .
              "<strong>compute</strong>(<u>\$fxFunction</u>, " .
              "<u>\$dfx1Function</u>, <u>\$dfx2Function</u>, " .
              "<u>\$xR</u>)<br />\n" .

              "<h3>Description</h3>\n" .
              "<em>callback</em> <u>\$fxFunction</u> Callback f(x) equation " .
              "function or object/method tuple.<br>\n" .

              "<em>callback</em> <u>\$dfx1Function</u> Callback f'(x) " .
              "equation function or object/method tuple.<br>\n" .

              "<em>callback</em> <u>\$dfx2Function</u> Callback f''(x) " .
              "equation function or object/method tuple.<br>\n" .

              "<em>float</em> <u>\$xR</u> Initial guess.<br>\n";
    }

    // }}}
    // {{{ compute()

    /**
     * Newton-Raphson method for calculate double root (twin root).
     *
     * @param callback $fxFunction   Callback f(x) equation function or object/method
     *                               tuple.
     * @param callback $dfx1Function Callback f'(x) equation function or
     *                               object/method tuple.
     * @param callback $dfx2Function Callback f''(x) equation function or
     *                               object/method tuple.
     * @param float    $xR           Initial guess.
     *
     * @return float|PEAR_Error Root value on success or PEAR_Error on failure.
     * @access public
     * @see Math_Numerical_RootFinding_Common::validateEqFunction()
     * @see Math_Numerical_RootFinding_Common::getEqResult()
     * @see Math_Numerical_RootFinding_Common::isDivergentRow()
     * @see Math_Numerical_RootFinding_NewtonRaphson::compute()
     */
    public function compute($fxFunction, $dfx1Function, $dfx2Function, $xR)
    {
        // Validate f(x) equation function.
        parent::validateEqFunction(
                 $fxFunction, $xR
               );

        // Validate f'(x) equation function.
        parent::validateEqFunction(
                 $dfx1Function, $xR
               );

        // Validate f''(x) equation function.
        parent::validateEqFunction(
                 $dfx2Function, $xR
               );

        // Sets maximum iteration and tolerance from options.
        $maxIteration = $this->options['max_iteration'];
        $errTolerance = $this->options['err_tolerance'];

        // Sets variable for saving errors during iteration, for divergent
        // detection.
        $epsErrors = array();

        for ($i = 1; $i < $maxIteration; $i++) {
            // Calculate f(x[i]), where: x[i] = $xR.
            $fxR = parent::getEqResult($fxFunction, $xR);

            // Calculate f'(x[i]), where: x[i] = $xR.
            $d1xR = parent::getEqResult(
                      $dfx1Function, $xR
                    );

            // Calculate f''(x[i]), where: x[i] = $xR.
            $d2xR = parent::getEqResult(
                      $dfx2Function, $xR
                    );

            // Avoid division by zero.
            if (pow($d1xR, 2) - ($fxR * $d2xR) == 0) {
                throw new \Exception('Iteration skipped, division by zero');
            }

            // Newton-Raphson's formula.
            $xN = $xR - (($fxR * $d1xR) / (pow($d1xR, 2) - ($fxR * $d2xR)));

            // xR is the root.
            if ($xN == 0) {
                $this->root = $xR;
                break;
            }

            // Compute error.
            $this->epsError = abs(($xN - $xR) / $xN);
            $epsErrors[]    = $this->epsError;

            // Detect for divergent rows.
            if ($this->isDivergentRows($epsErrors) &&
                $this->options['divergent_skip']) {
                throw new \Exception(
                         'Iteration skipped, divergent rows detected'
                       );
                break;
            }

            // Check for error tolerance, if lower than or equal with
            // $errTolerance it is the root.
            if ($this->epsError <= $errTolerance) {
                $this->root = $xR;
                break;
            }

            // Switch x[i+1] -> x[i], where: x[i] = $xR and x[i+1] = $xN.
            $xR = $xN;
        }
        $this->iterationCount = $i;
        return $this->root;
    }

    // }}}
}

/*
 * Local variables:
 * mode: php
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
?>
