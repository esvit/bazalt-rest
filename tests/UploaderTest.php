<?php

namespace tests;

use Bazalt\Rest;
use Tonic;

class UploaderTest extends \Bazalt\Rest\Test\BaseCase
{
    protected $uploader;

    protected function setUp(): void
    {
        $this->uploader = new \Bazalt\Rest\Uploader(array('jpg'), 1024);

        if(file_exists('/tmp/uploads_test')) {
            $this->rrmdir('/tmp/uploads_test');
        }
        mkdir('/tmp/uploads_test');
        file_put_contents('/tmp/test.jpg', 'test');
    }

    protected function tearDown(): void
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

    public function testHandleUploadInvalidDir()
    {
        $this->expectException(\Exception::class);
        $this->uploader->handleUpload('/');
    }

    public function testHandleUploadInvalidFile()
    {
        $_FILES['file'] = array(
            'size' => 0,
            'error' => 0
        );
        $this->expectException(\Bazalt\Rest\Exception\Upload::class);
        $this->uploader->handleUpload('/tmp');
    }

    public function testHandleUploadInvalidFile2()
    {
        $_FILES['file'] = array(
            'size' => 10,
            'error' => 0,
            'name' => '/tmp/test.jpg'
        );
        $this->uploader = new \Bazalt\Rest\Uploader(array('png'), 1024);
        $this->expectException(\Bazalt\Rest\Exception\Upload::class);
        $this->uploader->handleUpload('/tmp');
    }

    public function testHandleUploadError()
    {
        $_FILES['file'] = array(
            'size' => 10,
            'error' => 1,
            'name' => '/tmp/test.jpg'
        );
        $this->expectException(\Bazalt\Rest\Exception\Upload::class);
        $this->uploader->handleUpload('/tmp');
    }

    public function testHandleUpload()
    {
        $this->uploader = $this->getMockBuilder(\Bazalt\Rest\Uploader::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $this->uploader->method('handleUpload')
            ->willReturn([
                'file' => '/uploads/21/photos/132.jpg',
                'extension' => 'jpg',
                'size' => 10,
                'name' => 'test.jpg'
            ]);


        $_FILES['file'] = array(
            'size' => 10,
            'error' => 0,
            'tmp_name' => '/tmp/test.jpg',
            'name' => 'test.jpg'
        );
        $this->uploader->expects($this->once())
            ->method('handleUpload');
        $res = $this->uploader->handleUpload('/tmp/uploads_test', array(21, 'photos'));
        $this->assertTrue(strstr($res['file'], '/21/photos/') !== false);
        $this->assertEquals('test.jpg', $res['name']);
    }
}
