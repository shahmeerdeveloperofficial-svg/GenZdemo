<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RemoveEmptyQueryParameters
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isMethod('GET') || $request->ajax() || $request->expectsJson()) {
            return $next($request);
        }

        $query = $request->query();
        $cleanQuery = $this->withoutEmptyValues($query);

        if ($cleanQuery === $query) {
            return $next($request);
        }

        $queryString = http_build_query($cleanQuery, '', '&', PHP_QUERY_RFC3986);
        $url = $request->url() . ($queryString === '' ? '' : '?' . $queryString);

        return redirect()->to($url);
    }

    private function withoutEmptyValues(array $values): array
    {
        $isList = array_is_list($values);
        $clean = [];

        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $value = $this->withoutEmptyValues($value);
                if ($value === []) {
                    continue;
                }
            } elseif ($value === null || (is_string($value) && trim($value) === '')) {
                continue;
            }

            $clean[$key] = $value;
        }

        return $isList ? array_values($clean) : $clean;
    }
}
