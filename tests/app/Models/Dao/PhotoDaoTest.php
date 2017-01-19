<?php

namespace App\Models\Dao;

use App\Models\Photo;
use PHPUnit_Framework_TestCase;

class PDODecorator
{
    protected $pdo;
    protected $queries;
    public function getQueries()
    {
        return $this->queries;
    }
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function __call($name, $args)
    {
        return call_user_func_array([$this->pdo, $name], $args);
    }
    public function prepare($sql)
    {
        $this->queries[] = $sql;

        return call_user_func_array([$this->pdo, 'prepare'], func_get_args());
    }
}

class PhotoDaoTest extends PHPUnit_Framework_TestCase
{
    protected $dao;
    protected $conn;
    public static function setupBeforeClass()
    {
        $conn = db_connection();
        $conn->query('truncate table photos');
    }
    public static function tearDownAfterClass()
    {
        $conn = db_connection();
        $conn->query('truncate table photos');
    }

    public function setup()
    {
        $this->conn = new PDODecorator(db_connection());
        $this->dao = new PhotoDao($this->conn);
    }

    protected function invalidateDao()
    {
        $photoDao = $this->dao;
        $reflectionObject = new \ReflectionObject($photoDao);
        $method = $reflectionObject->getMethod('getMetadata');
        $method->setAccessible(true);
        $method->invoke($photoDao);

        $tableProperty = $reflectionObject->getProperty('table');
        $tableProperty->setAccessible(true);
        $tableProperty->setValue($photoDao, 'Invalid');
    }
    /**
     * @test
     */
    public function a_get_list_without_params_should_have_a_valid_sql_string()
    {
        $photoDao = $this->dao;
        $this->assertEquals([], $photoDao->getList());
        $table = $photoDao->getTable();
        $this->assertContains("SELECT * from `{$table}`", current($this->conn->getQueries()));
    }
    /**
     * @test
     */
    public function a_get_list_with_order_desc_should_return_valid_query()
    {
        $photoDao = $this->dao;
        $this->assertEquals([], $photoDao->getList([], ['id' => 'DESC']));
        $this->assertContains('ORDER BY `id` DESC', current($this->conn->getQueries()));
    }
    /**
     * @test
     */
    public function a_get_list_with_order_asc_should_return_valid_query()
    {
        $photoDao = $this->dao;
        $this->assertEquals([], $photoDao->getList([], ['id' => 'asc']));
        $this->assertContains('ORDER BY `id` ASC', current($this->conn->getQueries()));
    }
    /**
     * @test
     */
    public function a_get_list_with_invalid_order_should_return_asc()
    {
        $photoDao = $this->dao;
        $this->assertEquals([], $photoDao->getList([], ['id' => 'INVALID']));
        $this->assertContains('ORDER BY `id` ASC', current($this->conn->getQueries()));
    }

    /**
     * @test
     */
    public function a_get_list_with_limit_should_return_valid_query()
    {
        $photoDao = $this->dao;
        $this->assertEquals([], $photoDao->getList([], ['id' => 'NonExist'], 1));
        $this->assertContains('ORDER BY `id` ASC LIMIT 1', current($this->conn->getQueries()));
    }

    /**
     * @test
     */
    public function a_get_list_with_limit_offset_should_return_valid_query()
    {
        $photoDao = $this->dao;
        $this->assertEquals([], $photoDao->getList([], ['id' => 'NonExist'], 1, 2));
        $this->assertContains('ORDER BY `id` ASC LIMIT 1, 2', current($this->conn->getQueries()));
    }

    /**
     * @test
     * @expectedException App\Models\Dao\Exception
     */
    public function a_invalid_query_should_raise_exception()
    {
        $photoDao = $this->dao;
        $this->invalidateDao();
        $photoDao->getList();
    }

    /**
     * @test
     */
    public function a_find_method_should_return_a_valid_query()
    {
        $photoDao = $this->dao;
        $this->assertNull($photoDao->find(1));
    }

    /**
     * @test
     */
    public function a_insert_method_should_insert_on_database()
    {
        $photoDao = $this->dao;
        $data = ['label' => 'label','image' => 'img'];
        $id = $photoDao->insert($data);
        $result = $photoDao->find($id);
        $this->assertContains($data['label'], $result);
        $this->assertContains($data['image'], $result);
    }

    /**
     * @test
     * @expectedException App\Models\Dao\Exception
     */
    public function a_insert_method_with_invalid_sql_should_throw_exception()
    {
        $data = ['id' => 2,'label' => 'label','image' => 'img'];
        $photoDao = $this->dao;
        $this->invalidateDao();

        $id = $photoDao->insert($data);
    }

    /**
     * @test
     */
    public function a_update_method_should_update_on_database()
    {
        $photoDao = $this->dao;
        $data = ['label' => 'label','image' => 'img'];
        $id = $photoDao->insert($data);
        $newData = ['label' => 'label-updated','image' => 'img-updated'];
        $photoDao->update($newData, ['id' => $id]);
        $result = $photoDao->find($id);
        $this->assertContains($newData['label'], $result);
        $this->assertContains($newData['image'], $result);
    }
    /**
     * @test
     * @expectedException App\Models\Dao\Exception
     */
    public function a_update_method_with_invalid_sql_should_throw_exception()
    {
        $data = ['id' => 2,'label' => 'label','image' => 'img'];
        $photoDao = $this->dao;
        $this->invalidateDao();

        $id = $photoDao->update($data, ['id' => '2']);
    }

    /**
     * @test
     */
    public function a_delete_method_should_delete_on_database()
    {
        $photoDao = $this->dao;
        $data = ['label' => 'label','image' => 'img'];
        $id = $photoDao->insert($data);
        $newData = ['label' => 'label-updated','image' => 'img-updated'];
        $photoDao->delete(['id' => $id]);
        $result = $photoDao->find($id);
        $this->assertNull($result);
    }
    /**
     * @test
     * @expectedException App\Models\Dao\Exception
     */
    public function a_delete_method_with_invalid_sql_should_throw_exception()
    {
        $data = ['id' => 2,'label' => 'label','image' => 'img'];
        $photoDao = $this->dao;
        $this->invalidateDao();

        $id = $photoDao->delete(['id' => '2']);
    }
}
