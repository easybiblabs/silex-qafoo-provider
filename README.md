## silex-qafoo-provider

#### Setup

```
$app['qafoo.profiler.key'] = '{enter here your qafoo key}';
$app['qafoo.profiler.development'] = true;

$app->register(new \Easybib\Silex\Provider\QafooProfilerServiceProvider());
```

The provider will setup the qafoo profiler as soon as the register method is called.
