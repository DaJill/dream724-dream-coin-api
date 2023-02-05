<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $table = 'api_log';
    protected $connection = 'User';

    protected $fillable = ['method', 'content'];
    protected $casts = ['method' => 'string', 'content' => 'array'];

    /**
     * 新增Log
     *
     * @param  array   $parameters
     *
     * @return object
     */
    public static function createLog(array $parameters = [])
    {
        return self::create($parameters);
    }
}
