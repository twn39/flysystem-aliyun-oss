<?php

namespace spec\League\Flysystem\AliyunOSS;

use Dotenv\Dotenv;
use League\Flysystem\Config;
use OSS\OssClient;
use PhpSpec\ObjectBehavior;

class AliyunOSSAdapterSpec extends ObjectBehavior
{
    private $bucket;

    public function let()
    {
        $dotenv = new Dotenv(__DIR__.'/../', '.env');
        $dotenv->load();

        $accessKeyId = getenv('ACCESSKEYID');
        $accessKeySecret = getenv('ACCESSKEYSECRET');
        $endpoint = getenv('ENDPOINT');
        $bucket = getenv('BUCKET');
        $OSSClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

        $bucketExist = $OSSClient->doesBucketExist($bucket);

        if (!$bucketExist) {
            $OSSClient->createBucket($bucket, OssClient::OSS_ACL_TYPE_PUBLIC_READ);
        }

        $this->beConstructedWith($OSSClient, $bucket);

        $this->bucket = $bucket;
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('League\Flysystem\AliyunOSS\AliyunOSSAdapter');
        $this->shouldHaveType('League\Flysystem\Adapter\AbstractAdapter');
    }

    public function it_should_write_content()
    {
        $this->write('hello.txt', 'hello', new Config())->shouldBe(true);
    }

    public function it_should_has_object()
    {
        $this->has('hello.txt')->shouldBe(true);
    }

    public function it_should_read_content()
    {
        $this->read('hello.txt')->shouldHaveKeyWithValue('contents', 'hello');
    }

    public function it_should_update_content()
    {
        $this->update('hello.txt', 'hello world!', new Config())->shouldBe(true);
        $this->read('hello.txt')->shouldHaveKeyWithValue('contents', 'hello world!');
    }

    public function it_should_write_stream()
    {
        $imagePath = __DIR__ . '/../logo.png';
        
        $imageStream = fopen($imagePath, 'r+');

        $this->writeStream('hello.png', $imageStream, new Config())->shouldBe(true);
    }

    public function it_should_copy_object()
    {
        $this->copy('hello.txt', 'world.txt')->shouldBe(true);
    }

    public function it_should_delete_object()
    {
        $this->delete('hello.txt')->shouldBe(true);
    }

    public function it_should_rename_object()
    {
        $this->rename('hello.png', 'logo.png')->shouldBe(true);
    }

    public function it_should_create_dir()
    {
        $this->createDir('images', new Config())->shouldBe(true);
    }

    public function it_should_delete_dir()
    {
        $this->deleteDir('images')->shouldBe(false);
    }

    public function it_should_get_object_meta()
    {
        $this->getMetadata('logo.png')->shouldHaveKey('_info');
    }

    public function it_should_get_size()
    {
        $this->getSize('logo.png')->shouldHaveKey('size');
    }

    public function it_should_get_mime_type()
    {
        $this->getMimeType('logo.png')->shouldHaveKeyWithValue('mimetype', 'image/png');
    }

    public function it_should_get_timestamp()
    {
        $this->getTimestamp('logo.png')->shouldHaveType('DateTime');
    }
    
    public function it_should_read_stream()
    {
        $this->readStream('logo.png')->shouldHaveKey('stream');
    }
}

