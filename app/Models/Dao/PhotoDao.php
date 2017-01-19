<?php
/**
*  @todo Make an abstract class to avoid repeat logic
*/
namespace App\Models\Dao;

use PDO;
use App\Models\Dao\Exception as DaoException;

/**
 * @class PhotoDao*
 */
class PhotoDao
{
    /**
     * @param PDO
     */
    protected $conn;

    /**
     *@param string
     */
    protected $table = 'photos';

    /**
     * @var null|array Database metadata
     **/
    protected $metadata = null;

    /**
     * @var null|array
     **/
    protected $attributes = null;

    /**
     * @param PDO $connection
     */
    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    public function getTable()
    {
        return $this->table;
    }

    /**
     *
     **/
    public function getList(array $where = [], array $order = null, $limit = null, $offset = null)
    {
        $where = $this->filterData($where);

        $whereParamValues = [];
        $whereArray = $this->buildWhereClause($where);
        $orderClause = null;
        $orderClause = $this->buildOrderClause($order);

        $limitClause = $this->buildLimitOffsetClause($limit, $offset);

        $sql = "SELECT * from `{$this->table}` {$whereArray['where']} {$orderClause} {$limitClause}";

        $statement = $this->conn->prepare($sql);

        if (!$statement->execute($whereArray['values']) && $statement->errorCode()) {
            throw new DaoException(implode(' ', $statement->errorInfo()));
        }

        return  $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function find($id)
    {
        $result = current($this->getList(['id' => $id]));
        if (!$result) {
            return;
        }

        return $result;
    }
    public function insert(array $data)
    {
        $data = $this->filterData($data);
        $parameters = [];
        $paramValues = [];
        foreach ($data as $key => $value) {
            $parameters[] = ":{$key}";
            $paramValues[":{$key}"] = ($value);
        }

        $fields = implode(', ', array_keys($data));
        $parameters = implode(', ', $parameters);

        $sql = "INSERT INTO `{$this->table}`($fields) values($parameters)";
        $statement = $this->conn->prepare($sql);

        if (!$statement->execute($paramValues) && $statement->errorCode()) {
            throw new DaoException(implode(' ', $statement->errorInfo()));
        }

        return $this->conn->lastInsertId();
    }
    public function update(array $data, $where)
    {
        $data = $this->filterData($data);
        $fieldParameters = [];
        foreach ($data as $key => $value) {
            $fieldParameters[] = "{$key} = :{$key}";
            $paramValues[":{$key}"] = ($value);
        }

        $fieldParameters = implode(', ', $fieldParameters);

        $where = $this->filterData($where);
        $whereArray = $this->buildWhereClause($where);

        $sql = "UPDATE `{$this->table}` SET {$fieldParameters} {$whereArray['where']}";

        $statement = $this->conn->prepare($sql);
        $paramValues = array_merge($paramValues, $whereArray['values']);

        if (!$statement->execute($paramValues) && $statement->errorCode()) {
            throw new DaoException(implode(' ', $statement->errorInfo()));
        }

        return true;
    }

    public function delete($where)
    {
        $where = $this->filterData($where);
        $whereArray = $this->buildWhereClause($where);
        $sql = "DELETE FROM `{$this->table}` {$whereArray['where']}";
        $statement = $this->conn->prepare($sql);
        $paramValues = $whereArray['values'];

        if (!$statement->execute($paramValues) && $statement->errorCode()) {
            throw new DaoException(implode(' ', $statement->errorInfo()));
        }

        return true;
    }

    /***************************************************************************
        Protected
    ****************************************************************************/
    /**
     * Check database fields and return only fields that is in database;.
     * @param array $data
     * @return array - Filtered by fields on database
     */
    protected function filterData($data)
    {
        if (null === $this->attributes) {
            $this->attributes = [];
            $metadata = $this->getMetadata();
            foreach ($metadata as $value) {
                $value = array_change_key_case($value, CASE_LOWER);
                $this->attributes[$value['field']] = $value['field'];
            }
        }

        return array_intersect_key($data, $this->attributes);
    }

    protected function getMetadata()
    {
        if (null === $this->metadata) {
            $this->metadata = $this->conn->query("DESCRIBE `{$this->table}`")->fetchAll(PDO::FETCH_ASSOC);
        }

        return $this->metadata;
    }

    /**
     *
     */
    protected function buildWhereClause(array $where = [])
    {
        $whereClause = null;
        $whereParamValues = [];
        if ($where) {
            foreach ($where as $key => $value) {
                $whereClause[] = "{$key} = :{$key}";
                $whereParamValues[":{$key}"] = $value;
            }
            $whereClause = ' WHERE '.implode(' AND ', $whereClause);
        }

        return ['where' => $whereClause, 'values' => $whereParamValues];
    }

    protected function buildLimitOffsetClause($limit = null, $offset = null)
    {
        if (!$limit) {
            return;
        }

        $limit = (int) $limit; #avoid sql injection
            $limitClause = "LIMIT {$limit}";
        if ($offset) {
            $offset = (int) $offset;
            $limitClause .= ", {$offset}";
        }

        return $limitClause;
    }

    protected function buildOrderClause(array $order = null)
    {
        if (!$order) {
            return;
        }
        $order = $this->filterData($order);
        array_walk($order, function (&$v, $k) {
            $v = strtoupper($v);
            if ($v !== 'DESC') {
                $v = 'ASC';
            }
            $v = "`$k` $v";
        });
        $order = implode(', ', $order);

        return "ORDER BY {$order}";
    }
}
