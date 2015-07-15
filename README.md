## silex-qafoo-provider

Aka, the silex-**tideways**-provider!

## Tideways? Qafoo?

Check out https://www.tideways.io.

## Setup

```php
$app['qafoo.profiler.key'] = '{enter here your qafoo/tideways key}';
$app['qafoo.profiler.sample_rate'] = 20; // value equals %

$app->register(new \Easybib\Silex\Provider\QafooProfilerServiceProvider());
```

## Configuration

List of options:

 * qafoo.profiler.key (required)
 * qafoo.profiler.sample_rate

The provider will setup the qafoo profiler as soon as the register method is called.
