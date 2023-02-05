<?php

namespace App\Http\Middleware;

use App\Exceptions\CodeError\Middleware\CustomValidatorMiddlewareException;
use Closure;
use Validator;

class CustomValidatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guard)
    {
        foreach ($request->route()->computedMiddleware as $key => $value) {
            $validator = explode(':', $value);
            if (!empty($validator[1])) {
                $guard = explode(',', $validator[1]);
            }
            $category = explode('.', $validator[0]);
            if ($category[0] === 'validator' && !empty($category[1])) {
                try {
                    $this->verifier($category[1], $request, $guard);
                } catch (CustomValidatorMiddlewareException $e) {
                    return response()->json(['result' => false, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
                }
            }
        }

        return $next($request);
    }

    private function verifier($category, $request, $guard)
    {
        $param = $null = $must = [];
        $param_all = config('param_validator.'.$category.'.parameter');

        foreach ($guard as $key => $value) {
            $data = explode('@', $value);
            if (!empty($data[1])) {
                switch ($data[1]) {
                case 'null':
                    $null = array_merge($null, [$data[0]]);
                    break;
                case 'must':
                    $must = array_merge($must, [$data[0]]);
                    break;
                }
                $param[$data[0]] = $param_all[$data[0]];
            }
        }

        // 參數檢查格式
        $checkParam = Validator::make($request->all(), $param);

        if ($checkParam->fails()) {
            foreach ($checkParam->errors()->toArray() as $key => $value) {
                if (!in_array($key, $null) || (in_array($key, $null) && !empty($request->all()[$key]))) {
                    throw new CustomValidatorMiddlewareException($key, $category);
                }
            }
        }

        // 檢查是否有必帶參數
        if (!empty($must)) {
            $required = array_fill_keys($must, 'required');

            $checkParamRequired = Validator::make($request->all(), $required);

            if ($checkParamRequired->fails()) {
                foreach ($checkParamRequired->errors()->toArray() as $key => $value) {
                    if (!in_array($key, $null) || (in_array($key, $null) && !$request->exists($key))) {
                        throw new CustomValidatorMiddlewareException($key, $category);
                    }
                }
            }
        }

        return true;
    }
}
