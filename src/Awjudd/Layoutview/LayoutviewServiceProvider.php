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
        $this->app->bindShared('view', function($app)
        {
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
        return array('view');
    }

    /**
     * Register configuration files, with L5 fallback
     */
    protected function registerConfiguration()
    {
        // Is it possible to register the config?
        if (method_exists($this->app['config'], 'package'))
        {
            $this->app['config']->package('awjudd/layoutview', __DIR__ . '/config');
        }
        else
        {
            // Derive the full path to the user's config
            $userConfig = app()->configPath() . '/packages/awjudd/layoutview/config.php';

            // Check if the user-configuration exists
            if(!file_exists($userConfig))
            {
                $userConfig = __DIR__ .'/../../config/config.php';
            }

            // Load the config for now..
            $config = $this->app['files']->getRequire($userConfig);
            $this->app['config']->set('layoutview::config', $config);
        }
    }

}