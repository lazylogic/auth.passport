<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        \Log::info( "START >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>" );
        \Log::info( "[ REQUEST ] " . @$_SERVER['REQUEST_METHOD'] . " " . @$_SERVER['REQUEST_URI'] );

        parent::boot();

        /**
         * Create parameters via a signed URL for a named route.
         *
         * @param  string   $name       signature 생성에 사용할 api route 이름.
         *                              사전에 `named route` 로 등록되어 있어야 함.
         * @param  array    $parameters 추가 URL Query Parameters
         * @param  \DateTimeInterface|\DateInterval|int|null  $expiration
         * @param  bool     $absolute   url 앞에 현재 서버 schema 와 domain 을 추가 한다.
         *                              eg> http://loalhost:8000/...
         * @return array
         */
        URL::macro( 'buildSignedRouteParams', function (
            $name,
            $parameters = [],
            $expiration = null,
            $absolute = true
        ) {
            $parameters = $this->formatParameters( $parameters );

            if ( $expiration ) {
                $parameters['expires'] = $this->availableAt( $expiration );
            }

            $parameters['signature'] = hash_hmac(
                'sha256',
                $this->route( $name, $parameters, $absolute ),
                call_user_func( $this->keyResolver )
            );
            \Log::debug( '', $parameters );
            return $parameters;
        } );

        /**
         * Create a query string via a signed URL for a named route.
         *
         * @param  string   $name       signature 생성에 사용할 api route 이름.
         *                              사전에 `named route` 로 등록되어 있어야 함.
         * @param  array    $parameters 추가 URL Query Parameters
         * @param  \DateTimeInterface|\DateInterval|int|null  $expiration
         * @param  bool     $absolute   url 앞에 현재 서버 schema 와 domain 을 추가 한다.
         *                              eg> http://loalhost:8000/...
         * @return string
         */
        URL::macro( 'signedRouteQuery', function (
            $name,
            $parameters = [],
            $expiration = null,
            $absolute = true
        ) {
            return http_build_query(
                URL::buildSignedRouteParams( $name, $parameters, $expiration, $absolute )
            );
        } );

        /**
         * Create a query string via a temporary signed route URL for a named route.
         *
         * @param  string   $name       signature 생성에 사용할 api route 이름.
         *                              사전에 `named route` 로 등록되어 있어야 함.
         * @param  \DateTimeInterface|\DateInterval|int|null  $expiration
         * @param  array    $parameters 추가 URL Query Parameters
         * @param  bool     $absolute   url 앞에 현재 서버 schema 와 domain 을 추가 한다.
         *                              eg> http://loalhost:8000/...
         * @return string
         */
        URL::macro( 'temporarySignedRouteQuery', function (
            $name,
            $expiration,
            $parameters = [],
            $absolute = true
        ) {
            return URL::signedRouteQuery( $name, $parameters, $expiration, $absolute );
        } );

    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware( 'web' )
            ->namespace( $this->namespace )
            ->group( base_path( 'routes/web.php' ) );
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix( 'api' )
            ->middleware( 'api' )
            ->namespace( $this->namespace )
            ->group( base_path( 'routes/api.php' ) );
    }
}