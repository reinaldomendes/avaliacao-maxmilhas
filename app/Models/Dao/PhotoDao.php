<?php
/**
*  @todo Make an abstract class to avoid repeat logic
*/
namespace App\Models\Dao;

use PDO;

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
    public function __construct(PDO $connection)
    {
        $this->conn = $connection;
    }

    /**
     *
     **/
    public function getList(array $where = [], $order = null, $limit = null, $offset = null)
    {
        $where = $this->filterData($where);

        $whereParamValues = [];
        $whereArray = $this->buildWhereClause($where);
        $orderClause = null;
        if ($order) {
            /*@todo avoid injection here*/
            $orderClause = "order by {$order}";
        }
        $limitClause = null;
        if ($limit) {
            $limit = (int) $limit; #avoid sql injection
            $limitClause = "limit {$limit}";
            if ($offset) {
                $offset = (int) $offset;
                $limitClause .= ", {$offset}";
            }
        }

        $sql = "SELECT * from `{$this->table}` {$whereArray['where']} {$orderClause} {$limitClause}";

        $statement = $this->conn->prepare($sql);
        $statement->execute($whereArray['values']);

        return  $statement->fetchAll();
    }
    public function find($id)
    {
        return current($this->getList(['id' => $id]));
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
        if ($statement->execute($paramValues)) {
            return $this->conn->lastInsertId();
        }

        return;
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

        if ($statement->execute($paramValues)) {
            return true;
        }

        return;
    }

    public function delete($where)
    {
        $where = $this->filterData($where);
        $whereArray = $this->buildWhereClause($where);
        $sql = "DELETE FROM `{$this->table}` {$whereArray['where']}";
        $statement = $this->conn->prepare($sql);

        if ($statement->execute($whereArray['values'])) {
            return true;
        }

        return;
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
            $metadata = $this->getMetdata();
            foreach ($metadata as $value) {
                $value = array_change_key_case($value, CASE_LOWER);
                $this->attributes[$value['field']] = $value['field'];
            }
        }

        return array_intersect_key($data, $this->attributes);
    }

    protected function getMetdata()
    {
        if (null === $this->metadata) {
            $this->metadata = $this->conn->query("describe `{$this->table}`")->fetchAll();
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
}
