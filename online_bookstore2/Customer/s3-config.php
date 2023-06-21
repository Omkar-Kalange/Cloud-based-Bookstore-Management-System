<?php
    $serverPath = $_SERVER["DOCUMENT_ROOT"];
    require $serverPath.'/vendor/autoload.php';

    use Aws\S3\S3Client;
    use Aws\Exception\AwsException;

    $s3 = NULL;
    try
    {
        $s3 = S3Client::factory([
            'credentials' => [],
            'version' => '',
            'region' => ''
        ]);

    }
    catch(Exception $e)
    {
        die($e->getMessage());
    }
?>