<?php
/**
 * False Position method examples
 *
 * @author Firman Wandayandi <fwd@vfemail.net>
 * @package Math_Numerical_RootFinding
 * @category math
 * @license http://www.php.net/license/3_0.txt The PHP License, version 3.0
 */

/**
 * Math_Numerical_RootFinding class
 */
require_once 'Math/Numerical/RootFinding.php';

function fx($x) {
	return pow($x, 4) - 3 * pow($x, 2) - 7;
}

$mroot = new Math_Numerical_RootFinding();

$root = $mroot->falsePosition('fx', 3, 2);
if (PEAR::isError($root)) {
    print $root->toString();
    die;
}

print '<b>Root Finding::False Position</b><br /><br />';
print '<b>Case:</b><br />';
print 'f(x) = x<sup>4</sup> - 3x<sup>2</sup> - 7<br />';
print 'a = 3, b = 2<br /><br />';
print '<b>Iteration Count</b> = ' . $mroot->getIterationCount() . '<br />';
print '<b>Root</b> = ' . $root;
?>
