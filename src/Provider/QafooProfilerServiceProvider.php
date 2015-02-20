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

            $certificationFile = isset($app['qafoo.profiler.certification_file']) ? $app['qafoo.profiler.certification_file'] : null;
            $connectionTimeout = isset($app['qafoo.profiler.connection_timeout']) ? $app['qafoo.profiler.connection_timeout'] : 1;
            $timeout = isset($app['qafoo.profiler.timeout']) ? $app['qafoo.profiler.timeout'] : 1;

            Profiler::startDevelopment($app['qafoo.profiler.key']);
            Profiler::setBackend(
                new Profiler\CurlBackend(
                    $certificationFile,
                    $connectionTimeout,
                    $timeout
                )
            );
        } else {
            $sampleRate = isset($app['qafoo.profiler.sample_rate']) ? $app['qafoo.profiler.sample_rate'] : null;
            Profiler::start($app['qafoo.profiler.key'], $sampleRate);
        }
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
        // Priority 8, because
        // - Symfony Router is at 32, we need to be after that, to get the route name
        // - Symfony Firewall is at 8, so if you register this before the firewall
        //   it will log firewall-rejected requests; if you register it later, it won't
        // (see http://symfony.com/doc/current/reference/dic_tags.html#kernel-request)
        $app->before([$this, 'setProfilerTransaction'], 8);

        if ($app->offsetExists('security')) {
            $app->after(function() use ($app) {
                $this->setTransactionUserId($app);
            });
        }
    }

    /**
     * @param Application $app
     */
    public function setTransactionUserId(Application $app)
    {
        $token = $app['security']->getToken();
        if (null === $token) {
            return;
        }

        $user = $token->getUser();
        if (null === $user || !method_exists($user, 'getId')) {
            return;
        }

        Profiler::setCustomVariable('userId', $user->getId());
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
