<?php
/**
 * Xframe is a opensource php framework
 * @author     Hopeness <houpengg@gmail.com>
 * @link       https://github.com/hopeness/Xframe
 * @license    https://github.com/hopeness/Xframe/blob/master/LICENSE GNU License
 */

namespace Xframe;

/**
 * Base of Core
 * @author     Hopeness <houpengg@gmail.com>
 * @category   Core
 * @package    Core base
 */
final class ErrorException extends \ErrorException{

    public function __construct($errstr = '', $code = 0, $errno = 1, $errfile = __FILE__, $errline = __LINE__, $previous = null){
        parent::ErrorException($errstr, $code, $errno, $errfile, $errline, $previous);
    }

    /**
     * Error handling
     * @param  int    $errno   Error number
     * @param  string $errstr  Error message
     * @param  string $errfile Error file tracback
     * @param  int    $errline Error line number
     * @return
     */
    static public function errorExceptionHandler($errno, $errstr, $errfile, $errline){
        if(C()->DEBUG === true){
            echo '<div style="clear:both; margin:5px; padding:8px; border:#E0E0E0 1px solid; background:#F3F3F3;">
<div style="padding-bottom:5px; font-size:18px; font-weight:bold; color:#666; border-bottom:#E0E0E0 1px solid;">Error</div>
<div style="margin-top:8px; font-size:16px; color:#333;">[', $errno, '] ', $errstr,  '</div>
<div style="margin-top:8px; padding:5px; border:#E0E0E0 1px solid; background:#FDFBE3; color:#555; font-size:14px;">
    <span style="font-weight:bold">Trace:</span>
        <div style="padding:5px;">', $errfile, '(', $errline, ')', '</div>
    </div>
</div>';
        }else{
            E('system_error');
        }
    }

    /**
     * Fatal error handling
     * @return
     */
    static public function fatalErrorExceptionHandler(){
        echo 'fatalError();';
    }
}
