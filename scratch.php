<?php
$svc = new \App\Services\GeneticAlgorithmService(1);
$res = $svc->runEvolution();
echo json_encode([
    'generations' => $res['generations'],
    'fitness' => $res['fitness'],
    'genes_count' => count($res['chromosome'])
]);
