<?php

namespace Newbee\Syuue;

use Edu\Permission\Http\Api\Middleware\RbacAuthenticate;
use Illuminate\Support\ServiceProvider;
use Newbee\Syuue\Repository\Contract\ErpPayeeRepository;
use Newbee\Syuue\Repository\Contract\ErpReceiptCheckRepository;
use Newbee\Syuue\Repository\Contract\ErpReconciliationRepository;
use Newbee\Syuue\Repository\Eloquent\ErpPayeeRepositoryEloquent;
use Newbee\Syuue\Repository\Eloquent\ErpReceiptCheckRepositoryEloquent;
use Newbee\Syuue\Repository\Eloquent\ErpReconciliationRepositoryEloquent;

class SyuueServiceProvider extends ServiceProvider
{
    /**
     * The middleware aliases.
     *
     * @var array
     */
    protected $middlewareAliases = [
        'rbac.auth'=> RbacAuthenticate::class
    ];
    /**
     * 设定所有的容器绑定的对应关系
     *
     * @var array
     */
    public $bindings = [
        ErpSyuueRepository::class => ErpSyuueRepositoryEloquent::class,
        ErpPayeeRepository::class => ErpPayeeRepositoryEloquent::class,
        ErpReconciliationRepository::class => ErpReconciliationRepositoryEloquent::class,
    ];
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
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->aliasMiddleware();
    }


    /**
     * Alias the middleware.
     *
     * @return void
     */
    protected function aliasMiddleware()
    {
        $router = $this->app['router'];

        $method = method_exists($router, 'aliasMiddleware') ? 'aliasMiddleware' : 'middleware';

        foreach ($this->middlewareAliases as $alias => $middleware) {
            $router->$method($alias, $middleware);
        }
    }
}
