<?php

namespace EasyBib\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use QafooLabs\Profiler;

class QafooProfilerServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        if (isset($app['qafoo.profiler.development']) && $app['qafoo.profiler.development']) {
            Profiler::startDevelopment($app['qafoo.profiler.key']);
        } else {
            Profiler::start($app['qafoo.profiler.key']);
        }
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
        $app->before([$this, 'setProfilerTransaction'], Application::EARLY_EVENT);
    }

    /**
     * @param Request $request
     */
    public function setProfilerTransaction(Request $request)
    {
        $actionName = $request->get('_route');
        if (strpos($actionName, '__') === 0) {
            $actionName = $request->get('_controller');
        }

        Profiler::setTransactionName(
            sprintf(
                '%s %s',
                $request->getMethod(),
                $actionName
            )
        );
    }
}
