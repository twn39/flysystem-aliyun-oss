<?php
namespace League\Flysystem\AliyunOSS;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Config;
use League\Flysystem\Util;

class AliyunOSSAdapter extends AbstractAdapter
{

    private $aliyunClient;
    private $bucket;

    public function __construct(\ALIOSS $client, $bucket, $prefix = '')
    {
        $this->aliyunClient = $client;
        $this->bucket = $bucket;
        $this->setPathPreFix($prefix);
        $acl = \ALIOSS::OSS_ACL_TYPE_PUBLIC_READ;
        $this->aliyunClient->create_bucket($this->bucket, $acl);
    }

    public function getBucket()
    {
        return $this->bucket;
    }

    private function getHeader($path)
    {
        $response = $this->aliyunClient->get_object_meta($this->bucket, $path);
        return $response->header;
    }

    /**
     * Write a new file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function write($path, $contents, Config $config)
    {
        $options = array(
            'content' => $contents,
            'length' => strlen($contents),
        );
        $this->aliyunClient->upload_file_by_content($this->bucket, $path, $options);
        return true;
    }

    /**
     * Write a new file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource, Config $config)
    {

    }

    /**
     * Update a file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function update($path, $contents, Config $config)
    {
        $options = array(
            'content' => $contents,
            'length' => strlen($contents),
        );
        $this->aliyunClient->upload_file_by_content($this->bucket, $path, $options);
        return true;

    }

    /**
     * Update a file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function updateStream($path, $resource, Config $config)
    {

    }

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename($path, $newpath)
    {

    }

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath)
    {

    }

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path)
    {
        $this->aliyunClient->delete_object($this->bucket, $path);
        return true;
    }

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {

    }

    /**
     * Create a directory.
     *
     * @param string $dirname directory name
     * @param Config $config
     *
     * @return array|false
     */
    public function createDir($dirname, Config $config)
    {
        $this->aliyunClient->create_object_dir($this->bucket, $dirname);
        return true;
    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false file meta data
     */
    public function setVisibility($path, $visibility)
    {

    }
    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return array|bool|null
     */
    public function has($path)
    {
        $response = $this->aliyunClient->is_object_exist($this->bucket, $path);
        if ($response->status === 404) {
            return false;
        }
        if ($response->status === 200) {
            return true;
        }

    }

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path)
    {

    }

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function readStream($path)
    {

    }

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {

    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path)
    {

        $response = $this->getHeader($path);
        return $response;
        // return $response->header['_info'];
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getSize($path)
    {
        $response = $this->getHeader($path);

        return [
            'size' => $response['content-length'],
        ];

    }

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMimetype($path)
    {
        $response = $this->aliyunClient->get_object_meta($this->bucket, $path);

        return [
            'mimetype' => $response->header['_info']['content_type'],
        ];

    }

    /**
     * Get the timestamp of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getTimestamp($path)
    {
        $response = $this->getHeader($path);

        return [
            'timestamp' => $response['last-modified'],
        ];
    }

    /**
     * Get the visibility of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getVisibility($path)
    {

    }
}
