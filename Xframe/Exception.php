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
class Exception extends \Exception{

    public function __construct($message, $code = 0){
        parent::__construct($message, $code);
    }

    /**
     * Exception handler
     * @param  $e Exception object
     * @return
     */
    static public function exceptionHandler($e){
        if(C(false)->DEBUG === true){
            echo '<div style="clear:both; margin:5px; padding:8px; border:#E0E0E0 1px solid; background:#F3F3F3;">
<div style="padding-bottom:5px; font-size:18px; font-weight:bold; color:#666; border-bottom:#E0E0E0 1px solid;">Exception</div>
<div style="margin-top:8px; font-size:16px; color:#333;">', $e->getMessage(), '</div>
<div style="margin-top:8px; font-size:16px; color:#333;">', $e->getFile(), '(' , $e->getLine(), ')', '</div>
<div style="margin-top:8px; padding:5px; border:#E0E0E0 1px solid; background:#FDFBE3; color:#555; font-size:14px;">
    <span style="font-weight:bold">Trace:</span>
        <div style="padding:5px;">', str_replace("\n", "<br />\n", $e->getTraceAsString()), '</div>
    </div>
</div>';
        }else{
            E('system_error');
        }
    }
}
