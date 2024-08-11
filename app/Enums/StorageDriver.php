<?php

namespace App\Enums;

enum StorageDriver
{
    case Local;
    case S3;
    case FTP;
}
