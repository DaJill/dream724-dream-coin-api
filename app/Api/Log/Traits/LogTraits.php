<?php

namespace App\Api\Log\Traits;
use App\Model\Log\ApiLog;

trait LogTraits
{

    public function createLog($method, array $content = [])
    {
        $aQuery = ['method' => $method, 'content' => $content];
        $result = ApiLog::createLog($aQuery);

        return response()->json(['result' => true, 'data' => $result]);
    }

}
