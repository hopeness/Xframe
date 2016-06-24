<?php
/**
 * Base of Model
 * @author     Hopeness <houpengg@gmail.com>
 * @category   Core
 * @package    Core base
 */

namespace Xframe;

use Xframe\BaseAbstract,
    Xframe\Api\ModelInterface;

abstract class ModelAbstract extends BaseAbstract implements ModelInterface
{

    final public function __construct(){}

    /**
     * Custom construct function
     */
    public function construct(){}

    /**
     * Custom destruct function
     */
    public function destruct(){}

    final public function __destruct(){}

}
