## silex-qafoo-provider

#### Setup

```php
$app['qafoo.profiler.key'] = '{enter here your qafoo key}';
$app['qafoo.profiler.sample_rate'] = 20; // value equals %

// If you are in develop mode, this will ignore the sample rate and use 100% 
$app['qafoo.profiler.development'] = true;

$app->register(new \Easybib\Silex\Provider\QafooProfilerServiceProvider());
```

The provider will setup the qafoo profiler as soon as the register method is called.
