<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength( 191 );

        \DB::listen( function ( $query ) {
            \Log::debug( $query->sql );
            \Log::debug( $query->bindings );
            \Log::debug( $query->time );
        } );
    }
}