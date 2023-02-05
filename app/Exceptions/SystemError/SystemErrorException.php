<?php

namespace App\Exceptions\SystemError;

use RuntimeException;

class SystemErrorException extends RuntimeException {
    const MainCodeMap = [
        UnknownErrorException::class     => '2001',
    ];
}
