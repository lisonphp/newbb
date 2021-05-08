<?php
namespace Newbee\Report;

use Edu\Permission\Http\Api\Middleware\RbacAuthenticate;
use Illuminate\Support\ServiceProvider;
use Newbee\Report\Repository\Contract\ErpReceiptCheckRepository;
use Newbee\Report\Repository\Contract\ErpSettlementRepository;
use Newbee\Report\Repository\Contract\ErpPayeeRepository;
use Newbee\Report\Repository\Contract\ErpReconciliationRepository;
use Newbee\Report\Repository\Eloquent\ErpReceiptCheckRepositoryEloquent;
use Newbee\Report\Repository\Eloquent\ErpSettlementRepositoryEloquent;
use Newbee\Report\Repository\Eloquent\ErpPayeeRepositoryEloquent;
use Newbee\Report\Repository\Eloquent\ErpReconciliationRepositoryEloquent;

class ReportServiceProvider extends ServiceProvider
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
        ErpReceiptCheckRepository::class => ErpReceiptCheckRepositoryEloquent::class,
        ErpSettlementRepository::class => ErpSettlementRepositoryEloquent::class,
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
