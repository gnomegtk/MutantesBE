<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Utils\Stats;

$baseDir = __DIR__ . '/../';

$loader = require $baseDir . '/vendor/autoload.php';

$app = new Application();

$app['debug'] = true;

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$app->register(
    new DoctrineServiceProvider(),
    [
        'db.options' => [
            'driver'        => 'pdo_mysql',
            'host'          => '127.0.0.1',
            'dbname'        => 'mutants',
            'user'          => 'root',
            'password'      => 'root',
            'charset'       => 'utf8',
            'driverOptions' => [
                1002 => 'SET NAMES utf8',
            ],
        ],
    ]
);

$app->register(new DoctrineOrmServiceProvider(), [
    'orm.proxies_dir'             => $baseDir . 'src/App/Entity/Proxy',
    'orm.auto_generate_proxies'   => $app['debug'],
    'orm.em.options'              => [
        'mappings' => [
            [
                'type'                         => 'annotation',
                'namespace'                    => 'Entities\\',
                'path'                         => $baseDir. 'src/Entities',
                'use_simple_annotation_reader' => false,
            ],
        ],
    ]
]);

$app->get('/', function () {
    return 'It works!';
});

$app->post('/mutant', function (Request $request) use ($app) {

    $dna = $request->get('dna');
    if (!$dna) {
        $app->abort(403, "DNA not sent");
    }

    $checkMutant = new \Utils\CheckMutants();
    $result = $checkMutant->isMutant(json_decode($dna));

    $stats = new Stats($app['orm.em']);
    $stats->save($dna, $result);

    if (!$result) {
        $app->abort(403, "DNA not mutant");
    }

    return 'DNA mutant';
});

$app->get('/stats', function () use ($app) {

    $stats = new Stats($app['orm.em']);

    $countMutantDna = $stats->getTotal(true);
    $countHumanDna  = $stats->getTotal(false);

    $ratio = 0;
    $total = $countHumanDna + $countMutantDna;
    if ($total) {
        $ratio = round($countMutantDna / $total, 1);
    }

    return '
        {
           "count_mutant_dna": ' . $countMutantDna . ',
           "count_human_dna": ' . $countHumanDna . ',
           "ratio": '. $ratio . '
        }
    ';
});

$app->run();
