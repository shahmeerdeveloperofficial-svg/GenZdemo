<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class AffiliateTrackingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    
        $affiliateKey = $request->query('affiliate') ?? $request->query('key') ?? $request->query('aff') ?? $request->query('ref');

        if ($affiliateKey) {
            $affiliate = User::where('affiliate_key', $affiliateKey)
                ->where('role', USER_ROLE_AFFILIATE)
                ->where('status', STATUS_ACTIVE)
                ->first();

            if ($affiliate) {

                session(['affiliate_id' => $affiliate->id, 'affiliate_key' => $affiliateKey]);
                Cookie::queue('affiliate_referral_key', $affiliateKey, 60*24*30);
                $response = $next($request);
                $content = $response->getContent();
                if (is_string($content) && strpos($content, '</body>') !== false) {
                    $script = "<script>
                        if (typeof(Storage) !== 'undefined') {
                            localStorage.setItem('affiliate_referral_key', '{$affiliateKey}');
                        }
                    </script>";
                    $content = str_replace('</body>', $script . '</body>', $content);
                    $response->setContent($content);
                }

                return $response;
            }
        }

        return $next($request);
    }
}
