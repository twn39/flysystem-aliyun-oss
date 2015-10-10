A aliyun oss adapter for flysystem.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a14a77b7-682e-4347-93bf-b822b3c4bb0e/big.png)](https://insight.sensiolabs.com/projects/a14a77b7-682e-4347-93bf-b822b3c4bb0e)
[![StyleCI](https://styleci.io/repos/42989215/shield)](https://styleci.io/repos/42989215)
[![Code Climate](https://codeclimate.com/github/twn39/flysystem-aliyun-oss/badges/gpa.svg)](https://codeclimate.com/github/twn39/flysystem-aliyun-oss)

**Note:** setVisibility总是返回false. SDK没有提供删除目录，所以deleteDir也总是返回false.

SDK 可直接从阿里云下载，也可使用本人的`composer`包，对SDK未作任何改动。

demo code:

```php
<?php

use League\Flysystem\Filesystem;
use League\Flysystem\AliyunOSS\AliyunOSSAdapter;

$OSSClient = new \ALIOSS($accessKey, $accessSecret, $endPoint);

$adapter = new AliyunOSSAdapter($OSSClient, 'files-bucket');
$flysystem = new Filesystem($adapter);

$flysystem->write('test.txt', 'this is test file content.');

```

#### TODO：

**完善单元测试**
