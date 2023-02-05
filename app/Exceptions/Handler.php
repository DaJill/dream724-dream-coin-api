<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use App\Exceptions\SystemError\SystemErrorException;
use App\Exceptions\SystemError\UnknownErrorException;
use App\Exceptions\CodeError\CodeErrorException;
use Symfony\Component\Debug\Exception\FlattenException;
class Handler extends ExceptionHandler
{
    private $timestamp = 0;
    private $errorCode = 0;
    private $message = '';
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];
    
    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if (!config('app.debug')) {
            $this->timestamp = Carbon::now()->tz('Asia/Taipei')->timestamp;
            if ($exception instanceof SystemErrorException || $exception instanceof CodeErrorException) {
                $this->errorCode = $exception->getCode();
                $this->message = $exception->getMessage();
            } elseif ($exception instanceof NotFoundHttpException) {
                $this->errorCode = 404;
                $this->message = 'page not found';
            } elseif ($exception instanceof UnauthorizedHttpException) {
                $this->errorCode = 401;
                $this->message = 'no permission';
            } else {
                $this->errorCode = SystemErrorException::MainCodeMap[UnknownErrorException::class].UnknownErrorException::CodeMap['UnknowError'];
            }

            $trace = [];
            $e = FlattenException::create($exception);
            foreach ($e->toArray() as $key => $value) {
                $trace[$key]['file'] = $value['trace'][0]['file'] . "(line:{$value['trace'][0]['line']})";
                $trace[$key]['message'] = $value['message'];
            }

            if (!empty(request()->server('HTTP_X_FORWARDED_FOR'))) {
                $remoteAddr = explode(',', request()->server('HTTP_X_FORWARDED_FOR'))[0];
            } else {
                $remoteAddr = request()->server('REMOTE_ADDR');
            }

            $requestParams = request()->all();
            foreach ($requestParams as $key => &$params) {
                if (preg_match("/password/i", $key)) {
                    $params = '******';
                }
            }

            $log = array(
                    'timestamp'   => $this->timestamp,
                    'request_url' => request()->fullUrl(),
                    'method'      => request()->method(),
                    'params'      => json_encode($requestParams),
                    'host'        => request()->server('SERVER_NAME'),
                    'exception'   => class_basename($exception),
                    'error_file'  => $exception->getFile() . ':' . $exception->getLine(),
                    'error_info'  => $exception->getMessage(),
                    'error_code'  => $this->errorCode,
                    'error_trace' => json_encode(array_reverse($trace)),
                    'server_addr' => getenv('REAL_ADDR') ? getenv('REAL_ADDR') : request()->server('SERVER_ADDR'),
                    'remote_addr' => $remoteAddr,
                    'route'       => request()->server('HTTP_ROUTE'),
                );
            Log::error('error_log', $log);
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if (!config('app.debug')) {
            if ($exception instanceof NotFoundHttpException) {
                return response()->json(['result' => false, 'message' => $this->message, 'code' => $this->errorCode, 'timestamp' => $this->timestamp], 404);
            } elseif($exception instanceof CodeErrorException) {
                return response()->json(['result' => false, 'message' =>  $this->message, 'code' => $this->errorCode, 'timestamp' => $this->timestamp], 500);
            } elseif($exception instanceof UnauthorizedHttpException) {
                return response()->json(['result' => false, 'message' => $this->message, 'code' => $this->errorCode, 'timestamp' => $this->timestamp], 401);
            } else {
                return response()->json(['result' => false, 'message' => 'system error', 'code' => 500, 'timestamp' => $this->timestamp], 500);
            }
        }
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['result' => false, 'message' => 'system error', 'code' => $this->errorCode, 'timestamp' => $this->timestamp], 500);
        }
        return redirect()->guest('login');
    }
}