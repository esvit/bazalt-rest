<?php

namespace tests;

use Bazalt\Rest;
use Tonic;

class UploaderTest extends \Bazalt\Rest\Test\BaseCase
{
    protected $uploader;

    protected function setUp()
    {
        $this->uploader = new \Bazalt\Rest\Uploader(array('jpg'), 1024);

        if(file_exists('/tmp/uploads_test')) {
            $this->rrmdir('/tmp/uploads_test');
        }
        mkdir('/tmp/uploads_test');
        file_put_contents('/tmp/test.jpg', 'test');
    }

    protected function tearDown()
    {
        $this->uploader = null;
        unlink('/tmp/test.jpg');
        $this->rrmdir('/tmp/uploads_test');
    }

    protected function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        $this->rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /**
     * @expectedException \Exception
     */
    public function testHandleUploadInvalidDir()
    {
        $this->uploader->handleUpload('/');
    }

    /**
     * @expectedException \Exception
     */
    public function testHandleUploadInvalidFile()
    {
        $_FILES['file'] = array(
            'size' => 0
        );
        $res = $this->uploader->handleUpload('/tmp');
        $this->assertEquals(array('error' => 'File is empty.'), $res);
    }

    /**
     * @expectedException \Exception
     */
    public function testHandleUploadInvalidFile2()
    {
        $_FILES['file'] = array(
            'size' => 10,
            'name' => '/tmp/test.jpg'
        );
        $this->uploader = new \Bazalt\Rest\Uploader(array('png'), 1024);
        $res = $this->uploader->handleUpload('/tmp');
    }

    /**
     * @expectedException \Exception
     */
    public function testHandleUploadError()
    {
        $_FILES['file'] = array(
            'size' => 10,
            'error' => 1,
            'name' => '/tmp/test.jpg'
        );
        $res = $this->uploader->handleUpload('/tmp');
    }

    public function testHandleUpload()
    {
        $this->uploader = $this->getMock('\Bazalt\Rest\Uploader', array('moveUploadedFile'), array(array('jpg'), 1024));
        $_FILES['file'] = array(
            'size' => 10,
            'error' => 0,
            'tmp_name' => '/tmp/test.jpg',
            'name' => '/tmp/test.jpg'
        );
        $this->uploader->expects($this->once())
            ->method('moveUploadedFile');
        $res = $this->uploader->handleUpload('/tmp/uploads_test', array(21, 'photos'));
        $this->assertTrue(strstr($res, '/21/photos/') !== false);
    }
}