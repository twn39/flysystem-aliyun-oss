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
        $this->writeStream('hello.csv', __DIR__.'/../test.csv', new Config())->shouldBe(true);
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
        $this->rename('hello.csv', 'world.csv')->shouldBe(true);
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
        $this->getMetadata('world.txt')->shouldHaveKey('_info');
    }

    public function it_should_get_size()
    {
        $this->getSize('world.txt')->shouldBeEqualTo('12');
    }

    public function it_should_get_mime_type()
    {
        $this->getMimeType('world.txt')->shouldBeEqualTo('text/plain');
    }

    public function it_should_get_timestamp()
    {
        $this->getTimestamp('world.txt')->shouldHaveType('DateTime');
    }
}
