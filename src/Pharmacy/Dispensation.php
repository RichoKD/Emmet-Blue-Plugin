<?php declare(strict_types=1);
/**
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 *
 * This file is part of the EmmetBlue project, please read the license document
 * available in the root level of the project
 */
namespace EmmetBlue\Plugins\Pharmacy;

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
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 08/06/2016 14:20
 */
class Dispensation
{
	public static function newDispensation(array $data)
	{
		return Dispensation\Dispensation::create($data);
	}

	public static function viewDispensation(int $resourceId=0, array $data = [])
	{
		return Dispensation\Dispensation::view($resourceId, $data);
	}

	public static function retract(int $resourceId, array $data = [])
	{
		return Dispensation\Dispensation::retract($resourceId, $data);
	}

	public static function deleteDispensation(int $resourceId)
	{
		return Dispensation\storeInventory::delete($resourceId);
	}

    public static function editDispensation(int $resourceId, array $data)
    {
        $result = Dispensation\Dispensation::editDispensation($resourceId, $data);

        return $result;
    }
    
    public static function editDispensedItems(int $resourceId, array $data)
    {
        $result = Dispensation\Dispensation::editDispenseditems($resourceId, $data);

        return $result;
    }

    public static function retrieveDispensedItemsReport(int $resourceId = 0, array $data = []){
		$result = Dispensation\Reports::retrieveDispensedItems($resourceId, $data);

        return $result;
    }
}