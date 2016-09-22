<?php declare(strict_types=1);
/**
 * @license MIT
 * @author Bardeson Lucky <flashup4all@gmail.com>
 *
 * This file is part of the EmmetBlue project, please read the license document
 * available in the root level of the project
 */
namespace EmmetBlue\Plugins\Nursing;

use EmmetBlue\Core\Builder\BuilderFactory as Builder;
use EmmetBlue\Core\Factory\DatabaseConnectionFactory as DBConnectionFactory;
use EmmetBlue\Core\Builder\QueryBuilder\QueryBuilder as QB;
use EmmetBlue\Core\Exception\SQLException;
use EmmetBlue\Core\Exception\UndefinedValueException;
use EmmetBlue\Core\Session\Session;
use EmmetBlue\Core\Logger\DatabaseLog;
use EmmetBlue\Core\Logger\ErrorLog;
use EmmetBlue\Core\Constant;

/**
 * class ServicesRenderedItems.
 *
 * ServicesRenderedItems Controller
 *
 * @author Bardeson Lucky <flashup4all@gmail.com>
 * @since v0.0.1 22/08/2016 03:29AM
 */
class ServicesRenderedItems
{
    /**
     * Creates a new ServicesRenderedItems
     *
     * @param $_POST
     */
    public static function newServicesRenderedItems(array $data)
    {
        $result = ServicesRenderedItems\ServicesRenderedItems::create($data);

        return $result;
    }

    /**
     * Selects ServicesRenderedItems
     */
    public static function viewServicesRenderedItems(int $resourceId=0)
    {
        $result = ServicesRenderedItems\ServicesRenderedItems::view($resourceId);

        return $result;
    }

    /**
     * edit ServicesRenderedItems
     */
    public static function editServicesRenderedItems(int $resourceId=0, array $data)
    {
        $result = ServicesRenderedItems\ServicesRenderedItems::edit($resourceId, $data);

        return $result;
    }

    /**
     * Deletes a ServicesRenderedItems
     */
    public static function deleteServicesRenderedItems(int $resourceId)
    {
        $result = ServicesRenderedItems\ServicesRenderedItems::delete($resourceId);

        return $result;
    }
}