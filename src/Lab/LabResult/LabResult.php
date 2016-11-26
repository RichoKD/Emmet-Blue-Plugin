<?php declare(strict_types=1);
/**
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 *
 * This file is part of the EmmetBlue project, please read the license document
 * available in the root level of the project
 */
namespace EmmetBlue\Plugins\Lab\LabResult;

use EmmetBlue\Core\Builder\BuilderFactory as Builder;
use EmmetBlue\Core\Factory\DatabaseConnectionFactory as DBConnectionFactory;
use EmmetBlue\Core\Factory\DatabaseQueryFactory as DBQueryFactory;
use EmmetBlue\Core\Builder\QueryBuilder\QueryBuilder as QB;
use EmmetBlue\Core\Exception\SQLException;
use EmmetBlue\Core\Session\Session;
use EmmetBlue\Core\Logger\DatabaseLog;
use EmmetBlue\Core\Logger\ErrorLog;
use EmmetBlue\Core\Constant;

use EmmetBlue\Plugins\Permission\Permission as Permission;

/**
 * class LabResult.
 *
 * LabResult Controller
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 01/01/2016 04:21pm
 */
class LabResult
{
    /**
     * creates new lab resources
     *
     * @param array $data
     */
    public static function create(array $data)
    {
        $patientLabNumber = $data['patientLabNumber'] ?? null;
        $investigationName = $data['investigationName'] ?? null;
        $report = $data['report'] ?? null;
        $reportedBy = $data['reportedBy'] ?? null;

        $report = serialize($report);

        try
        {
            $query = "SELECT PatientID FROM Lab.Patients WHERE PatientLabNumber = $patientLabNumber";
            $patientId = ((DBConnectionFactory::getConnection()->query($query))->fetchAll())[0]["PatientID"];
            $repoData = [
                "patient"=>$patientId,
                "name"=>$investigationName,
                "type"=>"lab_result",
                "creator"=>$reportedBy
            ];
            $repoId = \EmmetBlue\Plugins\Patients\PatientRepository\PatientRepository::create($repoData)["lastInsertId"];

            $repoItemData = [
                "repository"=>$repoId,
                "name"=>"Investigation Conclusion",
                "category"=>"json",
                "json"=>unserialize($report),
                "creator"=>$reportedBy
            ];

            \EmmetBlue\Plugins\Patients\RepositoryItem\RepositoryItem::create($repoItemData);

            $result = DBQueryFactory::insert('Lab.LabResults', [
                'PatientLabNumber'=>QB::wrapString((string)$patientLabNumber, "'"),
                'RepositoryID'=>$repoId,
                'Report'=>QB::wrapString((string)$report, "'"),
                'ReportedBy'=>QB::wrapString((string)$reportedBy, "'"),
            ]);

            $result["repoId"] = $repoId;
            DatabaseLog::log(
                Session::get('USER_ID'),
                Constant::EVENT_SELECT,
                'Lab',
                'LabResults',
                (string)(serialize($result))
            );
            return $result;
        }
        catch (\PDOException $e)
        {
            throw new SQLException(sprintf(
                "Unable to process request (LabResult not created), %s",
                $e->getMessage()
            ), Constant::UNDEFINED);
        }
    }

    /**
     * view
     */
    public static function view(int $resourceId)
    {
        $selectBuilder = "SELECT * FROM Lab.LabResults WHERE PatientLabNumber = $resourceId";
        try
        {
            $viewOperation = (DBConnectionFactory::getConnection()->query((string)$selectBuilder))->fetchAll(\PDO::FETCH_ASSOC);

            DatabaseLog::log(
                Session::get('USER_ID'),
                Constant::EVENT_SELECT,
                'Lab',
                'LabResults',
                (string)$selectBuilder
            );

            foreach ($viewOperation as $key => $value) {
                $viewOperation[$key]["Report"] = unserialize($value["Report"]);
            }

            return $viewOperation;        
        } 
        catch (\PDOException $e) 
        {
            throw new SQLException(
                sprintf(
                    "Error procesing request"
                ),
                Constant::UNDEFINED
            );
            
        }
    }

    /**
     * Modifies a Ward resource
     */
    public static function edit(int $resourceId, array $data)
    {
        // $updateBuilder = (new Builder("QueryBuilder", "Update"))->getBuilder();

        // try
        // {
        //     if (isset($data['FullName'])){
        //         $data['FullName'] = QB::wrapString($data['FullName'], "'");
        //     }
        //     $updateBuilder->table("Lab.LabResults");
        //     $updateBuilder->set($data);
        //     $updateBuilder->where("LabResultLabNumber = $resourceId");

        //     $result = (
        //             DBConnectionFactory::getConnection()
        //             ->query((string)$updateBuilder)
        //         );

        //     return $result;
        // }
        // catch (\PDOException $e)
        // {
        //     throw new SQLException(sprintf(
        //         "Unable to process update, %s",
        //         $e->getMessage()
        //     ), Constant::UNDEFINED);
        // }
    }

    /**
     * delete
     */
    public static function delete(int $resourceId)
    {
        $deleteBuilder = (new Builder("QueryBuilder", "Delete"))->getBuilder();

        try
        {
            $deleteBuilder
                ->from("Lab.LabResults")
                ->where("ResultID = $resourceId");
            
            $result = (
                    DBConnectionFactory::getConnection()
                    ->exec((string)$deleteBuilder)
                );

            DatabaseLog::log(
                Session::get('USER_ID'),
                Constant::EVENT_SELECT,
                'Lab',
                'LabResults',
                (string)$deleteBuilder
            );

            return $result;
        }
        catch (\PDOException $e)
        {
            throw new SQLException(sprintf(
                "Unable to process delete request, %s",
                $e->getMessage()
            ), Constant::UNDEFINED);
        }
    }
}