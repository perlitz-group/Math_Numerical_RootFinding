<?php
/**
 * File contains Math_Numerical_RootFinding base class.
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
 * @category  Math
 * @package   Math_Numerical_RootFinding
 * @author    Firman Wandayandi <firman@php.net>
 * @copyright 2004-2008 Firman Wandayandi
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Math_Numerical_RootFinding
 */


/**
 * Math_Numerical_RootFinding base class.
 *
 * This class intended for build API structure and abstract class members.
 *
 * @category  Math
 * @package   Math_Numerical_RootFinding
 * @author    Firman Wandayandi <firman@php.net>
 * @copyright 2004-2008 Firman Wandayandi
 * @license   http://www.opensource.org/licenses/bsd-license.php
 *            BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Math_Numerical_RootFinding
 */
class Math_Numerical_RootFinding
{
    
    /**
     * Method driver aliases in order to create the prety file names,
     * also for insensitive-case of method name calls.
     *
     * @global array $GLOBALS['_Math_Numerical_RootFinding_drivers']
     * @_Math_Numerical_RootFinding_drivers
     * 
     * // OLD WAY
        $GLOBALS['_Math_Numerical_RootFinding_drivers'] = array(
            'bisection'         => 'Bisection',
            'falseposition'     => 'FalsePosition',
            'fixedpoint'        => 'FixedPoint',
            'newtonraphson'     => 'NewtonRaphson',
            'newtonraphson2'    => 'NewtonRaphson2',
            'ralstonrabinowitz' => 'RalstonRabinowitz',
            'secant'            => 'Secant'
        );
     */
    protected static $drivers = array(
        'bisection'         => 'Bisection',
        'falseposition'     => 'FalsePosition',
        'fixedpoint'        => 'FixedPoint',
        'newtonraphson'     => 'NewtonRaphson',
        'newtonraphson2'    => 'NewtonRaphson2',
        'ralstonrabinowitz' => 'RalstonRabinowitz',
        'secant'            => 'Secant'
    );
    
    // {{{ factory()
    
    /**
     * Create new instance of RootFinding method class.
     *
     * @param string $method             Method name.
     * @param array  $options (optional) Options (options is available inspecified
     *                                   method class).
     *
     * @return object New method's class on success or PEAR_Error on failure.
     * @access public
     * @static
     */
    public static function factory($method, $options = null)
    {
        $method = strtolower(trim($method));
        if (!isset(self::$drivers[$method])) {
            throw new \Exception('Driver file not found for ' .
                                    '\'' . $method . '\' method');
        }

        $method = self::$drivers[$method];

        $classname = '\Math_Numerical_RootFinding_' . $method;
        if (!class_exists($classname)) {
            throw new \Exception('Undefined class \'' . $classname . '\'');
        }

        $obj = new $classname;
        if (!is_object($obj) || !is_a($obj, $classname)) {
            throw new \Exception('Failed creating object from class '.
                                    '\'' . $classname . '\'');
        }

        return $obj;
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
