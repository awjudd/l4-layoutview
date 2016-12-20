<?php namespace Awjudd\Layoutview;

use Illuminate\Support\ServiceProvider;

class LayoutviewServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfiguration();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('view', function ($app) {
            // Next we need to grab the engine resolver instance that will be used by the
            // environment. The resolver will be used by an environment to get each of
            // the various engine implementations such as plain PHP or Blade engine.
            $resolver = $app['view.engine.resolver'];

            $finder = $app['view.finder'];

            $env = new LayoutView($resolver, $finder, $app['events']);

            // We will also set the container instance on this view environment since the
            // view composers may be classes registered in the container, which allows
            // for great testable, flexible composers for the application developer.
            $env->setContainer($app);

            $env->share('app', $app);

            return $env;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['view'];
    }

    /**
     * Register configuration files, with L5 fallback
     */
    protected function registerConfiguration()
    {
        $this->publishes([
            $this->baseConfigurationFile() => config_path('layout-view.php'),
        ]);
    }

    private function baseConfigurationFile()
    {
        return __DIR__.'/../../../config/layout-view.php';
    }
}
