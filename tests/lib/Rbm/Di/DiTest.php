<?php

namespace Rbm\Di;

use PHPUnit_Framework_TestCase;
use Rbm\Di\Di;

class DiTest  extends PHPUnit_Framework_TestCase
{
    protected $di;

    public function setup()
    {
        $this->di = new Di();
    }

  /**
   * @test
   */
  public function we_should_bind_and_resolve_a_closure()
  {
      $this->di->bind('SomeAlias', function ($value) {
        return new \ArrayObject((array) $value);
      });
      $array = [1,2,3];

      $this->assertEquals($this->di->make('SomeAlias', [$array])->getArrayCopy(), $array);
  }

  /**
   * @test
   */
  public function we_should_bind_to_a_class_and_make_with_0_params()
  {
      $this->di->bind('SomeAlias', '\ArrayObject');
      $array = [];

      $this->assertEquals($this->di->make('SomeAlias')->getArrayCopy(), $array);
  }

  /**
   * @test
   */
  public function we_should_bind_to_a_class_and_make_with_1_params()
  {
      $this->di->bind('SomeAlias', '\ArrayObject');
      $array = [1,2,3];

      $this->assertEquals($this->di->make('SomeAlias', [$array])->getArrayCopy(), $array);
  }

  /**
   * @test
   */
  public function we_should_bind_to_a_class_and_make_with_2_params()
  {
      $this->di->bind('SomeAlias', '\ArrayObject');
      $array = [1,2,3];

      $this->assertEquals($this->di->make('SomeAlias', [$array, 2])->getArrayCopy(), $array);
  }

  /**
   * @test
   */
  public function we_should_bind_to_a_class_and_make_with_3_params()
  {
      $this->di->bind('SomeAlias', '\ArrayObject');
      $array = [1,2,3];

      $this->assertEquals($this->di->make('SomeAlias', [$array, 2, \ArrayIterator::class])->getArrayCopy(), $array);
  }

  /**
   * @test
   * @expectedException \InvalidArgumentException
   */
  public function we_should_bind_to_a_class_and_make_with_4_params()
  {
      $this->di->bind('SomeAlias', '\ArrayObject');
      $array = [1,2,3];

      $this->assertEquals($this->di->make('SomeAlias', [$array, 2, \ArrayIterator::class, '4'])->getArrayCopy(), $array);
  }

  /**
   * @test
   * @expectedException \InvalidArgumentException
   */
  public function we_should_bind_to_a_class_and_make_with_5_params()
  {
      $this->di->bind('SomeAlias', '\ArrayObject');
      $array = [1,2,3];

      $this->assertEquals($this->di->make('SomeAlias', [$array, 2, \ArrayIterator::class, '4', '5'])->getArrayCopy(), $array);
  }

  /**
   * @test
   * @expectedException \Rbm\Di\Di\Exception
   */
  public function we_should_bind_to_an_non_existing_class()
  {
      $this->di->bind('SomeAlias', '\NonExistentClass');

      $this->assertEquals($this->di->make('SomeAlias'), null);
  }

  /**
   * @test
   */
  public function a_bind_should_create_different_instances()
  {
      $this->di->bind('SomeAlias', function ($value) {
        return new \ArrayObject((array) $value);
      });
      $array = [1,2,3];

      $this->assertNotSame($this->di->make('SomeAlias', [$array]), $this->di->make('SomeAlias', [$array]));
  }

  /**
   * @test
   */
  public function a_singleton_bind_should_not_create_different_instances()
  {
      $this->di->bindSingleton('SomeAlias', function ($value) {
        return new \ArrayObject((array) $value);
      });
      $array = [1,2,3];

      $this->assertSame($this->di->make('SomeAlias', [$array]), $this->di->make('SomeAlias', [$array]));
  }

  /**
   * @test
   * @expectedException Rbm\Di\Di\Exception
   */
  public function a_not_bind_should_throw_exception()
  {
      $this->di->make('foo');
  }

  /**
   * @test
   */
  public function a_bind_param_should_return_a_param()
  {
      $this->di = new Di();
      $this->di->bindParam('SomeParam', '1');
      $this->assertEquals($this->di->getParam('SomeParam'), '1');
  }
}
