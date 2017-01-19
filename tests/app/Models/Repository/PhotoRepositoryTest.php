<?php

namespace App\Models\Repository;

use App\Models\Dao\PhotoDao;
use App\Models\Photo;
use PHPUnit_Framework_TestCase;
use App\Models\Repository\PhotoRepository;

class PhotoRepositoryTest extends PHPUnit_Framework_TestCase
{
    protected $repository = null;
    protected $daoMock = null;
    public function setup()
    {
        $this->daoMock = $this->getMockBuilder(PhotoDao::class)
                            ->disableOriginalConstructor()
                            ->setConstructorArgs([null])
                            ->getMock();
        $this->repository = new PhotoRepository($this->daoMock, function ($args) {
            return new Photo($args);
        });
    }

    /**
     *@test
     */
    public function a_get_list_should_return_an_array_of_photo()
    {
        $data = ['label' => 'Label','image' => 'img'];
        $this->daoMock
            ->expects($this->once())
            ->method('getList')
            ->willReturn([$data]);

        $result = $this->repository->getList();
        $this->assertTrue(is_array($result));
        $firstObject = current($result);
        $this->assertInstanceOf(Photo::class, $firstObject);
        $this->assertEquals($data['label'], $firstObject->label);
        $this->assertEquals($data['image'], $firstObject->image);
    }

    /**
     * @test
     */
    public function a_find_should_return_a_photo()
    {
        $data = ['label' => 'Label','image' => 'img'];
        $this->daoMock
            ->expects($this->once())
            ->method('find')
            ->willReturn($data);

        $firstObject = $this->repository->find(1);

        $this->assertInstanceOf(Photo::class, $firstObject);
        $this->assertEquals($data['label'], $firstObject->label);
        $this->assertEquals($data['image'], $firstObject->image);
    }
    /**
     * @test
     * @expectedException App\Models\Repository\Exception
     */
    public function a_find_should_raise_an_exception_when_not_found()
    {
        $this->daoMock
        ->expects($this->once())
        ->method('find')
        ->willReturn(null);

        $firstObject = $this->repository->find(1);
    }

    /**
     * @test
     */
    public function a_insert_should_call_insert_on_dao_and_will_return_true()
    {
        $data = ['label' => 'Label','image' => 'img'];
        $this->daoMock->expects($this->once())
                        ->method('insert')
                        ->willReturn(2);

        $result = $this->repository->insert($this->repository->newInstance($data));
        $this->assertEquals($result, true);
    }
    /**
     * @test
     */
    public function a_insert_should_call_insert_on_dao_and_will_return_false()
    {
        $data = ['label' => 'Label','image' => 'img'];
        $this->daoMock->expects($this->once())
                        ->method('insert')
                        ->willReturn(false);

        $result = $this->repository->insert($this->repository->newInstance($data));
        $this->assertEquals($result, false);
    }

    /**
     * @test
     */
    public function a_update_should_call_update_on_dao_and_will_return_true()
    {
        $data = ['id' => 1,'label' => 'Label','image' => 'img'];
        $this->daoMock->expects($this->once())
                        ->method('update')
                        ->willReturn(1);

        $result = $this->repository->update($this->repository->newInstance($data));
        $this->assertEquals($result, true);
    }

    /**
     * @test
     */
    public function a_update_should_call_update_on_dao_and_will_return_false()
    {
        $data = ['id' => 1,'label' => 'Label','image' => 'img'];
        $this->daoMock->expects($this->once())
                        ->method('update')
                        ->willReturn(false);

        $result = $this->repository->update($this->repository->newInstance($data));
        $this->assertEquals($result, false);
    }

    /**
     * @test
     */
    public function a_save_should_call_insert_on_dao()
    {
        $data = ['label' => 'Label','image' => 'img'];
        $this->daoMock->expects($this->once())
                        ->method('insert')
                        ->willReturn(false);

        $result = $this->repository->save($this->repository->newInstance($data));
        $this->assertEquals($result, false);
    }

    /**
     * @test
     */
    public function a_save_should_call_update_on_dao()
    {
        $data = ['id' => 1,'label' => 'Label','image' => 'img'];
        $this->daoMock->expects($this->once())
                        ->method('update')
                        ->willReturn(1);

        $result = $this->repository->save($this->repository->newInstance($data));
        $this->assertEquals($result, true);
    }

    /**
     * @test
     */
    public function a_delete_should_call_delete_on_dao_and_will_return_true()
    {
        $data = ['id' => 1,'label' => 'Label','image' => 'img'];
        $this->daoMock->expects($this->once())
                        ->method('delete')
                        ->willReturn(1);

        $result = $this->repository->delete($this->repository->newInstance($data));
        $this->assertEquals($result, true);
    }

    /**
     * @test
     */
    public function a_delete_should_call_delete_on_dao_and_will_return_false()
    {
        $data = ['id' => 1,'label' => 'Label','image' => 'img'];
        $this->daoMock->expects($this->once())
                        ->method('delete')
                        ->willReturn(false);

        $result = $this->repository->delete($this->repository->newInstance($data));
        $this->assertEquals($result, false);
    }
}
