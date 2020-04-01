<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

// {{{ Header

/**
 * Driver file contains Math_Numerical_RootFinding_Bisection class to provide Ralston
 * and Rabinowitz method root finding calculation.
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
 * @copyright  2004-2008 Firman Wandayandi
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://pear.php.net/package/Math_Numerical_RootFinding
 * @version    CVS: $Id$
 */

/**
 * Ralston and Rabinowitz method class.
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
class Math_Numerical_RootFinding_RalstonRabinowitz
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
              "<u>\$dfxFunction</u>, <u>\$xR0</u>, " .
              "<u>\$xR1</u>)<br />\n" .

              "<h3>Description</h3>\n" .
              "<em>callback</em> <u>\$fxFunction</u> Callback f(x) equation " .
              "function or object/method tuple.<br>\n" .

              "<em>callback</em> <u>\$dfxFunction</u> Callback f'(x) " .
              "equation function or object/method tuple.<br>\n" .

              "<em>float</em> <u>\$xR0</u> First initial guess.<br>\n" .

              "<em>float</em> <u>\$xR1</u> Second initial guess.<br>\n";
    }

    // }}}
    // {{{ compute()

    /**
     * Ralston and Rabinowitz method for calculate double root (twin root).
     *
     * @param callback $fxFunction Callback f(x) equation function or object/method
     *                             tuple.
     * @param callback $dxFunction Callback f'(x) equation function or object/method
     *                             tuple.
     * @param float    $xR0        First initial guess.
     * @param float    $xR1        Second initial guess.
     *
     * @return float|PEAR_Error Root value on success or PEAR_Error on failure.
     * @access public
     * @see Math_Numerical_RootFinding_Common::validateEqFunction()
     * @see Math_Numerical_RootFinding_Common::getEqResult()
     * @see Math_Numerical_RootFinding_Common::isDivergentRow()
     * @see Math_Numerical_RootFinding_Secant::compute()
     */
    public function compute($fxFunction, $dxFunction, $xR0, $xR1)
    {
        // Validate f(x) equation function.
        parent::validateEqFunction(
                 $fxFunction, $xR0
               );

        // Validate f'(x) equation function.
        parent::validateEqFunction(
                 $dxFunction, $xR0
               );

        // Sets maximum iteration and tolerance from options.
        $maxIteration = $this->options['max_iteration'];
        $errTolerance = $this->options['err_tolerance'];

        // Sets variable for saving errors during iteration, for divergent
        // detection.
        $epsErrors = array();

        for ($i = 1; $i <= $maxIteration; $i++) {
            // Calculate f(x[i-1]) and f'(x[1]), where: x[i-1] = $xR0.
            $fxR0 = parent::getEqResult(
                      $fxFunction, $xR0
                    );
            $dxR0 = parent::getEqResult(
                      $dxFunction, $xR0
                    );

            // Calculate f(x[i]) and f'(x[1]), where: x[i] = $xR1.
            $fxR1 = parent::getEqResult(
                      $fxFunction, $xR1
                    );
            $dxR1 = parent::getEqResult(
                      $dxFunction, $xR1
                    );

            // Calculate f(x[i-1]) / f'(x[i-1], where x[i-1] = $xR0.
            $uxR0 = $fxR0 / $dxR0;

            // Calculate f(x[i]) / f'(x[i]), where x[i] = $xR1;
            $uxR1 = $fxR1 / $dxR1;

            // Avoid division by zero.
            if ($uxR0 - $uxR1 == 0) {
                throw new \Exception('Iteration skipped division by zero');
            }

            // Ralston and Rabinowitz's formula.
            $xN = $xR1 - ($uxR1 * ($xR0 - $xR1) / ($uxR0 - $uxR1));

            // xR is the root.
            if ($xN == 0) {
                $this->root = $xR;
                break;
            }

            // Compute error.
            $this->epsError = abs(($xN - $xR1) / $xN);
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
                $this->root = $xR1;
                break;
            }

            // Swicth the values for next iteration x[i] -> x[i-1] and
            // x[i+1] -> x[i], where: x[i-1] = $xR0, x[i] = $xR1, and
            // x[i+1] = $xN.
            $xR0 = $xR1;
            $xR1 = $xN;
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
