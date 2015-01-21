## silex-qafoo-provider

## Setup

```php
$app['qafoo.profiler.key'] = '{enter here your qafoo key}';
$app['qafoo.profiler.sample_rate'] = 20; // value equals %

// If you are in develop mode, this will ignore the sample rate and use 100%
$app['qafoo.profiler.development'] = true;

$app->register(new \Easybib\Silex\Provider\QafooProfilerServiceProvider());
```

## Configuration

You can configure timeouts like so:

```
$app->register(
    new \Easybib\Silex\Provider\QafooProfilerServiceProvider(),
    [
        'qafoo.profiler.connection_timeout' => 3,
        'qafoo.profiler.timeout' => 3,
    ]
);
```

List of options:

 * qafoo.profiler.key
 * qafoo.profiler.sample_rate (development mode)
 * qafoo.profiler.development
 * qafoo.profiler.certification_file (development mode)
 * qafoo.profiler.connection_timeout (development mode)
 * qafoo.profiler.timeout (development mode)


The provider will setup the qafoo profiler as soon as the register method is called.
