<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
        ],
        'worker' => [
            'class' => 'console\controllers\WorkerController',
        ],
    ],
    'components' => [
        'log' => [
            'traceLevel' => 0,
            'flushInterval' => 1,
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'exportInterval' => 1, // <-- and here
                    'logFile' => '@console/runtime/logs/console.log',
                    'logVars' => []
                ],
                [
                    'class' => 'yii\log\FileTarget', //в файл
                    'levels' => ['info'],
                    'exportInterval' => 1, // <-- and here
                    'categories' => ['mails'], //категория логов
                    'logFile' => '@console/runtime/logs/mails.log', //куда сохранять
                    'logVars' => [] //не добавлять в лог глобальные переменные ($_SERVER, $_SESSION...)
                ],
            ],
        ],

    ],
    'params' => $params,
];
