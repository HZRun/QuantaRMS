<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
class MyGuestbookTest extends TestCase
{
use TestCaseTrait;
/**
* @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
*/
public function getConnection()
{
$pdo = new PDO('mysql::dbname:');
return $this->createDefaultDBConnection($pdo, ':dbname:');
}
/**
* @return PHPUnit_Extensions_Database_DataSet_IDataSet
*/
public function getDataSet()
{
return $this->createFlatXMLDataSet('./testDB.xml');
}
}
?>