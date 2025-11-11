<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    // Percaya semua proxy (aman untuk PaaS seperti Railway/Cloudflare)
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    // Pakai seluruh header X-Forwarded-* standar
    protected $headers = 
    Request::HEADER_X_FORWARDED_FOR |
    Request::HEADER_X_FORWARDED_HOST |
    Request::HEADER_X_FORWARDED_PORT |
    Request::HEADER_X_FORWARDED_PROTO;


    // Catatan:
    // Jika (dan hanya jika) kamu pasti di belakang AWS ELB,
    // pakai ini sebagai gantinya:
    // protected $headers = Request::HEADER_X_FORWARDED_AWS_ELB;
}
