<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/3/2021
 * Time: 9:28 PM
 */

namespace common\migrations\db;

use ReflectionClass;
use yii\db\Migration as BaseMigration;
use yii\db\Query;
use yii\helpers\ArrayHelper;
class Migration extends BaseMigration
{
    /**
     * Table encoding
     * @var string
     */
    public $tableEncoding = "CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    /**
     * Table engine
     * @var string
     */
    public $tableEngine = "ENGINE=InnoDB";

    /**
     * @inheritdoc
     */
    public function createTable($table, $columns, $options = null)
    {
        if ($options === null) {
            $options = $this->tableEncoding . ' ' . $this->tableEngine;
        }
        return parent::createTable($table, $columns, $options);
    }

    /**
     * Clear Relation
     * @param string  $tableName
     * @param string  $columnName
     * @param string  $refTableName
     * @param string  $refColumnName
     * @param boolean $delete
     * @param string  $primaryKey
     * @param string  $refPrimaryKey
     */
    /**
     * @param string $tableName
     * @param string $columnName
     * @param string $refTableName
     * @param string $refColumnName
     * @param bool $delete
     * @param string $primaryKey
     * @param string $refPrimaryKey
     */
    public function clearRelation(string $tableName, string $columnName, string $refTableName, string $refColumnName, bool $delete = true, string $primaryKey = 'id', string $refPrimaryKey = 'id'): void
    {
        $query = (new Query())
            ->select(["{$tableName}.{$primaryKey}"])
            ->from($tableName)
            ->leftJoin($refTableName, "{$tableName}.{$columnName} = {$refTableName}.{$refColumnName}")
            ->where(["{$refTableName}.{$refPrimaryKey}" => null]);
        var_dump($query->createCommand()->rawSql);

        $rows = $query->all();
        $ids = ArrayHelper::getColumn($rows, $primaryKey);

        if ($ids) {
            $where = [$primaryKey => $ids];
            if ($delete) {
                $this->delete($tableName, $where);
            } else {
                $this->update($tableName, [$columnName => null], $where);
            }
        }
    }

    /**
     * @return bool
     * @throws \ReflectionException
     */
    public function executeSqlFile()
    {
        $className = (new ReflectionClass($this))->getShortName();
        $fileName = $className . '.sql';
        $filePath = join(DIRECTORY_SEPARATOR, [__DIR__, $fileName]);
        $sql = file_get_contents($filePath);
        $this->execute($sql);
        return true;
    }
}