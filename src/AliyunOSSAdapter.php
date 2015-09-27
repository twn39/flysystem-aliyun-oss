<?php

namespace League\Flysystem\AliyunOSS;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Config;

class AliyunOSSAdapter extends AbstractAdapter
{
    private $aliyunClient;
    private $bucket;

    public function __construct(\ALIOSS $client, $bucket, $prefix = '')
    {
        $this->aliyunClient = $client;
        $this->bucket = $bucket;
        $this->setPathPreFix($prefix);
        // note: sometimes the create bucket will fail, require test.
        $this->createBucket();
    }

    /**
     * @return bool
     */
    private function createBucket()
    {
        $oss = $this->aliyunClient;
        $bucket = $this->getBucket();
        $acl = \ALIOSS::OSS_ACL_TYPE_PUBLIC_READ;
        $oss->create_bucket($bucket, $acl);

        return true;
    }

    /**
     * @return mixed
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param $path
     *
     * @return array
     */
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
        $options = [
            'content' => $contents,
            'length'  => strlen($contents),
        ];
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
        $contents = stream_get_contents($resource);

        $options = [
            'content' => $contents,
            'length'  => strlen($contents),
        ];
        $this->aliyunClient->upload_file_by_content($this->bucket, $path, $options);

        if (is_resource($resource)) {
            fclose($resource);
        }

        return true;
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
        $options = [
            'content' => $contents,
            'length'  => strlen($contents),
        ];
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
        $contents = stream_get_contents($resource);
        $options = [
            'content' => $contents,
            'length'  => strlen($contents),
        ];
        $this->aliyunClient->upload_file_by_content($this->bucket, $path, $options);

        return true;
    }

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newPath
     *
     * @return bool
     */
    public function rename($path, $newPath)
    {
        $options = [
        ];
        $this->aliyunClient->copy_object($this->bucket, $path, $this->bucket, $newPath, $options);

        $this->aliyunClient->delete_object($this->bucket, $path);

        return true;
    }

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newPath
     *
     * @return bool
     */
    public function copy($path, $newPath)
    {
        $options = [
        ];
        $this->aliyunClient->copy_object($this->bucket, $path, $this->bucket, $newPath, $options);

        return true;
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
        return false;
    }

    /**
     * Create a directory.
     *
     * @param string $dirName directory name
     * @param Config $config
     *
     * @return array|false
     */
    public function createDir($dirName, Config $config)
    {
        $this->aliyunClient->create_object_dir($this->bucket, $dirName);

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
        return false;
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
        $options = [];
        $res = $this->aliyunClient->get_object($this->bucket, $path, $options);

        return [
            'contents' => $res->body,
        ];
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
        $options = [];
        $res = $this->aliyunClient->get_object($this->bucket, $path, $options);
        $url = $res->header['oss-request-url'];
        $handle = fopen($url, 'r');

        return [
            'stream' => $handle,
        ];
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
        if ($recursive) {
            $delimiter = '';
        } else {
            $delimiter = '/';
        }

        $prefix = $directory.'/';
        $next_marker = '';
        $maxkeys = 100;
        $options = [
            'delimiter' => $delimiter,
            'prefix'    => $prefix,
            'max-keys'  => $maxkeys,
            'marker'    => $next_marker,
        ];
        $res = $this->aliyunClient->list_object($this->bucket, $options);

        if ($res->isOK()) {
            $body = $res->body;
            $xml = new \SimpleXMLElement($body);

            $paths = [];
            foreach ($xml->Contents as $content) {
                $filePath = (string) $content->Key;

                $type = (substr($filePath, -1) == '/') ? 'dir' : 'file';

                if ($type == 'dir') {
                    $paths[] = [
                        'type' => $type,
                        'path' => $filePath,
                    ];
                } else {
                    $paths[] = [
                        'type'      => $type,
                        'path'      => $filePath,
                        'timestamp' => strtotime($content->LastModified),
                        'size'      => (int) $content->Size,
                    ];
                }
            }
            foreach ($xml->CommonPrefixes as $content) {
                $paths[] = [
                    'type' => 'dir',
                    'path' => (string) $content->Prefix,
                ];
            }

            return $paths;
        }
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
        return false;
    }
}
