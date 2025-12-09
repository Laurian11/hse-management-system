<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\KPICalculationService;
use App\Models\Company;
use App\Models\User;
use App\Models\Employee;
use Carbon\Carbon;

class CalculateKPIs extends Command
{
    protected $signature = 'kpis:calculate {--date=} {--period=daily} {--type=all}';
    protected $description = 'Calculate KPIs for companies, system, users, and employees';

    protected $kpiService;

    public function __construct(KPICalculationService $kpiService)
    {
        parent::__construct();
        $this->kpiService = $kpiService;
    }

    public function handle()
    {
        $date = $this->option('date') 
            ? Carbon::parse($this->option('date')) 
            : Carbon::today();
        
        $period = $this->option('period');
        $type = $this->option('type');
        
        $this->info("Calculating KPIs for {$date->format('Y-m-d')} ({$period})...");
        
        if ($type === 'all' || $type === 'system') {
            $this->info('Calculating System KPIs...');
            $this->kpiService->calculateSystemKPI($date, $period);
            $this->info('✓ System KPIs calculated');
        }
        
        if ($type === 'all' || $type === 'company') {
            $this->info('Calculating Company KPIs...');
            $companies = Company::where('is_active', true)->get();
            $bar = $this->output->createProgressBar($companies->count());
            $bar->start();
            
            foreach ($companies as $company) {
                $this->kpiService->calculateCompanyKPI($company, $date, $period);
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info("✓ Company KPIs calculated for {$companies->count()} companies");
        }
        
        if ($type === 'all' || $type === 'user') {
            $this->info('Calculating User KPIs...');
            $users = User::where('is_active', true)->get();
            $bar = $this->output->createProgressBar($users->count());
            $bar->start();
            
            foreach ($users as $user) {
                $this->kpiService->calculateUserKPI($user, $date, $period);
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info("✓ User KPIs calculated for {$users->count()} users");
        }
        
        if ($type === 'all' || $type === 'employee') {
            $this->info('Calculating Employee KPIs...');
            $employees = Employee::where('is_active', true)->get();
            $bar = $this->output->createProgressBar($employees->count());
            $bar->start();
            
            foreach ($employees as $employee) {
                $this->kpiService->calculateEmployeeKPI($employee, $date, $period);
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info("✓ Employee KPIs calculated for {$employees->count()} employees");
        }
        
        $this->info("\n✓ KPI calculation complete!");
        
        return 0;
    }
}
