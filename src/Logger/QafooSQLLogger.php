<?php
/**
 * @package  Till Klampaeckel <till@php.net>
 * @license  BSD-2-Clause
 * @link     http://www.imagineeasy.com/
 */
namespace EasyBib\Silex\Logger;

use Doctrine\DBAL\Logging\SQLLogger;

/**
 * Purpose   A sql logger and timer for the qafoo profiler.
 *
 * @author   Till Klampaeckel <till@php.net>
 * @author   Benjamin Eberlei
 * @license  BSD-2-Clause
 * @link     http://www.imagineeasy.com/
 */
class QafooSQLLogger implements SQLLogger
{
    private $id;

    private $span;

    /**
     * @param string $sql
     * @param array  $params
     * @param array  $types
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        if (extension_loaded('tideways') && class_exists('\Tideways\Profiler')) {
            $this->span = \Tideways\Profiler::createSpan('sql');
            $this->span->startTimer();
            $this->span->annotate(['title' => $sql]);

            return;
        }

        if (class_exists('\QafooLabs') and method_exists('\QafooLabs', 'startCustomTimer')) {
            $this->id = \QafooLabs\Profiler::startCustomTimer('sql', $sql);
        }
    }
    public function stopQuery()
    {
        if (null !== $this->span) {
            $this->span->stopTimer();

            return;
        }

        if (null !== $this->id) {
            \QafooLabs\Profiler::stopCustomTimer($this->id);
        }
    }
}
