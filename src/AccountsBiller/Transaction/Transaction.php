<?php declare(strict_types=1);
/**
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.calculhmac(clent, data)om>
 *
 * This file is part of the EmmetBlue project, please read the license document
 * available in the root level of the project
 */
namespace EmmetBlue\Plugins\AccountsBiller\Transaction;

use EmmetBlue\Core\Builder\BuilderFactory as Builder;
use EmmetBlue\Core\Factory\DatabaseConnectionFactory as DBConnectionFactory;
use EmmetBlue\Core\Factory\DatabaseQueryFactory as DBQueryFactory;
use EmmetBlue\Core\Builder\QueryBuilder\QueryBuilder as QB;
use EmmetBlue\Core\Exception\SQLException;
use EmmetBlue\Core\Exception\UndefinedValueException;
use EmmetBlue\Core\Session\Session;
use EmmetBlue\Core\Logger\DatabaseLog;
use EmmetBlue\Core\Logger\ErrorLog;
use EmmetBlue\Core\Constant;

/**
 * class BillingTransaction.
 *
 * BillingTransaction Controller
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 08/06/2016 14:20
 */
class Transaction
{
    public static function create(array $data)
    {
        $metaId = $data['metaId'] ?? null;
        $customerName = $data['customerName'] ?? null;
        $customerPhone = $data['customerPhone'] ?? null;
        $customerAddress = $data['customerAddress'] ?? null;
        $paymentMethod = $data['paymentMethod'] ?? null;
        $amountPaid = $data['amountPaid'] ?? 0;
        $transactionStatus = $data["transactionStatus"] ?? "";
        $staff = $data["staff"] ?? null;

        $query = "SELECT TOP 1 a.BilledAmountTotal, b.BillingAmountBalance FROM Accounts.BillingTransactionMeta a FULL OUTER JOIN Accounts.BillingTransaction b ON a.BillingTransactionMetaID = b.BillingTransactionMetaID WHERE a.BillingTransactionMetaID = $metaId ORDER BY b.BillingTransactionDate DESC";

        $queryResult = (DBConnectionFactory::getConnection()->query($query))->fetchAll(\PDO::FETCH_ASSOC);
        $totalBilledAmount = (int)$queryResult[0]["BilledAmountTotal"];
        $totalBalLeft = $queryResult[0]["BillingAmountBalance"];

        if ($totalBalLeft !== "" && $totalBalLeft !== null && $totalBalLeft !== 0){
            $amountBalance = (int)$totalBalLeft - (int)$amountPaid;
        }
        else {
            $amountBalance = $totalBilledAmount - (int)$amountPaid;
        }

        try
        {
            $result = DBQueryFactory::insert('Accounts.BillingTransaction', [
                'BillingTransactionMetaID'=>$metaId,
                'BillingTransactionDate'=>'GETDATE()',
                'BillingTransactionCustomerName'=>(is_null($customerName)) ? "NULL" : QB::wrapString((string)$customerName, "'"),
                'BillingTransactionCustomerPhone'=>(is_null($customerPhone)) ? "NULL" : QB::wrapString((string)$customerPhone, "'"),
                'BillingTransactionCustomerAddress'=>(is_null($customerAddress)) ? "NULL" : QB::wrapString((string)$customerAddress, "'"),
                'BillingPaymentMethod'=>(is_null($paymentMethod)) ? "NULL" : QB::wrapString((string)$paymentMethod, "'"),
                'BillingAmountPaid'=>(is_null($amountPaid)) ? "NULL" : QB::wrapString((string)$amountPaid, "'"),
                'BillingAmountBalance'=>(is_null($amountBalance)) ? "NULL" : QB::wrapString((string)$amountBalance, "'"),
                'StaffID'=>$staff,
            ]);

            $id = $result["lastInsertId"];

            $q = "UPDATE Accounts.PaymentRequest SET RequestFulfillmentStatus = 1 WHERE AttachedInvoice = $metaId";
            $r =  (DBConnectionFactory::getConnection()->exec($q));

            $q = "UPDATE Accounts.BillingTransactionMeta SET Status = 'deleted' WHERE BillingTransactionMetaID = $metaId";
            $r =  (DBConnectionFactory::getConnection()->exec($q));

            $query = "SELECT * FROM Accounts.BillingTransaction WHERE BillingTransactionID = $id";
            $result = (DBConnectionFactory::getConnection()->query($query))->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        }
        catch (\PDOException $e)
        {
            throw new SQLException(sprintf(
                "Unable to process request, %s",
                $e->getMessage()
            ), Constant::UNDEFINED);
        }
    }

    /**
     * Modifies the content of a department group record
     */
    public static function edit(int $resourceId, array $data)
    {
        $updateBuilder = (new Builder("QueryBuilder", "Update"))->getBuilder();

        try
        {
            if (isset($data['BillingTransactionCustomerID'])){
                $data['BillingTransactionCustomerID'] = QB::wrapString((string)$data['BillingTransactionCustomerID'], "'");
            }
            if (isset($data['BillingPaymentMethod'])){
                $data['BillingPaymentMethod'] = QB::wrapString((string)$data['BillingPaymentMethod'], "'");
            }
            if (isset($data['BillingAmountPaid'])){
                $data['BillingAmountPaid'] = QB::wrapString((string)$data['BillingAmountPaid'], "'");
            }
            if (isset($data['BillingAmountBalance'])){
                $data['BillingAmountBalance'] = QB::wrapString((string)$data['BillingAmountBalance'], "'");
            }

            $updateBuilder->table("Accounts.BillingTransaction");
            $updateBuilder->set($data);
            $updateBuilder->where("BillingTransactionID = $resourceId");

            $result = (
                    DBConnectionFactory::getConnection()
                    ->exec((string)$updateBuilder)
                );

            return $result;
        }
        catch (\PDOException $e)
        {
            throw new SQLException(sprintf(
                "Unable to process update, %s",
                $e->getMessage()
            ), Constant::UNDEFINED);
        }
    }

    /**
     * Returns department group data
     *
     * @param int $resourceId optional
     */
    public static function view(int $resourceId = 0, array $data = [])
    {
        $selectBuilder = (new Builder("QueryBuilder", "Select"))->getBuilder();

        try
        {
            if (empty($data)){
                $selectBuilder->columns("*");
            }
            else {
                $selectBuilder->columns(implode(", ", $data));
            }
            
            $selectBuilder->from("Accounts.BillingTransaction a");

            if ($resourceId !== 0){
                $selectBuilder->where("a.BillingTransactionID = $resourceId");
            }
            
            $result = (
                DBConnectionFactory::getConnection()
                ->query((string)$selectBuilder)
            )->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        }
        catch (\PDOException $e)
        {
            throw new SQLException(sprintf(
                "Unable to retrieve requested data, %s",
                $e->getMessage()
            ), Constant::UNDEFINED);
        }
    }

    public static function viewByInvoice(int $resourceId = 0)
    {
        $query = "SELECT TOP 1 a.*, b.BillingTransactionNumber FROM Accounts.BillingTransaction a INNER JOIN Accounts.BillingTransactionMeta b ON a.BillingTransactionMetaID = b.BillingTransactionMetaID WHERE a.BillingTransactionMetaID = (SELECT AttachedInvoice FROM Accounts.PaymentRequest WHERE PaymentRequestID = $resourceId)";

        // die($query);

        try
        {            
            $result = (
                DBConnectionFactory::getConnection()
                ->query($query)
            )->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($result as $key => $value) {
                $result[$key]["invoiceData"] = \EmmetBlue\Plugins\AccountsBiller\TransactionMeta\TransactionMeta::viewByNumber((int) $value["BillingTransactionNumber"])[0];
            }

            if (isset($result[0])){
                $result = $result[0];
            }

            return $result;
        }
        catch (\PDOException $e)
        {
            throw new SQLException(sprintf(
                "Unable to retrieve requested data, %s",
                $e->getMessage()
            ), Constant::UNDEFINED);
        }
    }

    public static function delete(int $resourceId)
    {
        $deleteBuilder = (new Builder("QueryBuilder", "Delete"))->getBuilder();

        try
        {
            $deleteBuilder
                ->from("Accounts.BillingTransaction")
                ->where("BillingTransactionID = $resourceId");
            
            $result = (
                    DBConnectionFactory::getConnection()
                    ->exec((string)$deleteBuilder)
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