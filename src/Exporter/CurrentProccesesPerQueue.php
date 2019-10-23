<?php


namespace LKDevelopment\HorizonPrometheusExporter\Exporter;


use Laravel\Horizon\Contracts\MetricsRepository;
use Laravel\Horizon\Contracts\WorkloadRepository;
use LKDevelopment\HorizonPrometheusExporter\Contracts\Exporter;
use Prometheus\CollectorRegistry;
use Superbalist\LaravelPrometheusExporter\PrometheusExporter;

class CurrentProccesesPerQueue implements Exporter
{
    protected $gauge;
    public function metrics(CollectorRegistry $prometheusExporter)
    {

        $this->gauge = $prometheusExporter->registerGauge(
            'horizon_current_processes',
            'Current processes of all queues',
            ['queue']
        );
    }

    public function collect()
    {
        $workloadRepository = app(WorkloadRepository::class);
        $workloads = collect($workloadRepository->get())->sortBy('name')->values();

        $workloads->each(function ($workload) {
            $this->gauge->set($workload['processes'], [$workload['name']]);
        });
    }
}
