<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once 'db_connect.php';
require_once '/var/www/html/aws/aws-autoloader.php'; // Path to the S3Client file

use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;

// Set your AWS credentials and region
$credentials = CredentialProvider::defaultProvider();

// Create an S3 client
$s3 = new S3Client([
    'credentials' => $credentials,
    'region' => $awsRegion,
    'version' => 'latest'
]);

// Specify the bucket name
$bucketName = 'testt-bucket';

// List objects in the bucket
try {
    $objects = $s3->listObjects(['Bucket' => $bucketName]);
    foreach ($objects['Contents'] as $object) {
        echo $object['Key'] . "\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

