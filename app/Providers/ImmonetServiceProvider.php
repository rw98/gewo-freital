<?php

namespace App\Providers;

use App\Immonet\Client as ImmoscoutClient;
use App\Immonet\Immowelt\Client as ImmoweltClient;
use App\Immonet\Immowelt\ListingTransferService as ImmoweltTransferService;
use App\Immonet\Immowelt\OpenImmoGenerator;
use App\Immonet\ListingTransferService as ImmoscoutTransferService;
use App\Immonet\TransferManager;
use Illuminate\Support\ServiceProvider;

class ImmonetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // ImmobilienScout24
        $this->app->singleton(ImmoscoutClient::class, function () {
            return new ImmoscoutClient(
                baseUrl: config('services.immoscout.base_url'),
                consumerKey: config('services.immoscout.consumer_key'),
                consumerSecret: config('services.immoscout.consumer_secret'),
                accessToken: config('services.immoscout.access_token'),
                accessTokenSecret: config('services.immoscout.access_token_secret'),
            );
        });

        $this->app->singleton(ImmoscoutTransferService::class, function ($app) {
            return new ImmoscoutTransferService($app->make(ImmoscoutClient::class));
        });

        // Immowelt
        $this->app->singleton(ImmoweltClient::class, function () {
            return new ImmoweltClient(
                ftpHost: config('services.immowelt.ftp_host'),
                ftpPort: (int) config('services.immowelt.ftp_port', 21),
                ftpUsername: config('services.immowelt.ftp_username'),
                ftpPassword: config('services.immowelt.ftp_password'),
                ftpPath: config('services.immowelt.ftp_path', '/'),
                ftpSsl: (bool) config('services.immowelt.ftp_ssl', false),
            );
        });

        $this->app->singleton(OpenImmoGenerator::class, function () {
            return new OpenImmoGenerator;
        });

        $this->app->singleton(ImmoweltTransferService::class, function ($app) {
            return new ImmoweltTransferService(
                $app->make(ImmoweltClient::class),
                $app->make(OpenImmoGenerator::class),
            );
        });

        // Transfer Manager (unified access)
        $this->app->singleton(TransferManager::class, function ($app) {
            return new TransferManager(
                $app->make(ImmoscoutTransferService::class),
                $app->make(ImmoweltTransferService::class),
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
