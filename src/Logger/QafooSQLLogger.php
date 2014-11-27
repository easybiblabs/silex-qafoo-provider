<?php
/**
 * @package  Till Klampaeckel <till@php.net>
 * @license  BSD-2-Clause
 * @link     http://www.imagineeasy.com/
 */
namespace EasyBib\Silex\Logger;

use Doctrine\DBAL\Logging\SQLLogger;
use QafooLabs\Profiler;

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
    /**
     * @param string $sql
     * @param array  $params
     * @param array  $types
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->id = Profiler::startCustomTimer('sql', $sql);
    }
    public function stopQuery()
    {
        Profiler::stopCustomTimer($this->id);
    }
}
