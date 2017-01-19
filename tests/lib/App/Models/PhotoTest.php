<?php

namespace App\Models;

use App\Models\Photo;
use PHPUnit_Framework_TestCase;

if (!function_exists('upload_url')) {
    function upload_url($value)
    {
        return '/uploads';
    };
}

class PhotoTest extends PHPUnit_Framework_TestCase
{
    protected $photo;
    public function setup()
    {
        $this->photo = new Photo([]);
    }
    /**
     * @test
     */
    public function a_photo_should_have_to_array()
    {
        $this->photo->image = 'valor';
        $this->assertEquals($this->photo->toArray(), ['image' => 'valor']);
    }
    /**
     * @test
     */
    public function a_get_image_url_should_return_upload_url_with_the_value()
    {
        $this->photo->image = 'valor';
        $this->assertEquals($this->photo->getImageUrl(), upload_url('valor'));
    }

    /**
     * @test
     */
    public function a_non_defined_property_should_return_null()
    {
        $this->assertNull($this->photo->nao_existe);
    }
}
