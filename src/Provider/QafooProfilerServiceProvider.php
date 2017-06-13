<?php

namespace EasyBib\Silex\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class QafooProfilerServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        if (!class_exists('Tideways\Profiler')) {
            return;
        }

        // override key if specified, or attempt starting if tideways is not configured to autostart
        if (isset($app['qafoo.profiler.key']) || \Tideways\Profiler::isStarted()) {
            $sampleRate = isset($app['qafoo.profiler.sample_rate']) ? $app['qafoo.profiler.sample_rate'] : null;
            \Tideways\Profiler::start($app['qafoo.profiler.key'], $sampleRate);
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
        if (!class_exists('Tideways\Profiler')){
            return;
        }

        /** @var TokenInterface $token */
        $token = $app['security']->getToken();
        if (null === $token) {
            return;
        }

        $user = $token->getUser();
        if (null === $user || !method_exists($user, 'getId')) {
            return;
        }

        \Tideways\Profiler::setCustomVariable('userId', $user->getId());
    }

    /**
     * @param Request $request
     * @param Application $app
     */
    public function setProfilerTransaction(Request $request, Application $app)
    {
        if (!class_exists('Tideways\Profiler')) {
            return;
        }

        $actionName = $request->get('_route');

        $blacklist = isset($app['qafoo.profiler.route_blacklist']) ? array_flip($app['qafoo.profiler.route_blacklist']) : [];
        if (isset($blacklist[$actionName])) {
            \Tideways\Profiler::ignoreTransaction();
            return;
        }

        if (strpos($actionName, '__') === 0) {
            $actionName = $request->get('_controller');
        }

        \Tideways\Profiler::setTransactionName(
            sprintf(
                '%s %s',
                $request->getMethod(),
                $actionName
            )
        );
    }
}
