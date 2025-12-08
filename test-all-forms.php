<?php

/**
 * Comprehensive Form Testing Script
 * Tests all create and edit forms in the HSE Management System
 * 
 * Run: php test-all-forms.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "HSE Management System - Form Testing\n";
echo "========================================\n\n";

$formsTested = 0;
$formsPassed = 0;
$formsFailed = 0;
$formsSkipped = 0;
$results = [];

// Get all routes
$routes = Route::getRoutes();

// Define form routes to test
$formRoutes = [
    // Admin Module
    'admin.companies.create' => 'Admin - Companies',
    'admin.companies.edit' => 'Admin - Companies',
    'admin.departments.create' => 'Admin - Departments',
    'admin.departments.edit' => 'Admin - Departments',
    'admin.users.create' => 'Admin - Users',
    'admin.users.edit' => 'Admin - Users',
    'admin.roles.create' => 'Admin - Roles',
    'admin.roles.edit' => 'Admin - Roles',
    'admin.employees.create' => 'Admin - Employees',
    'admin.employees.edit' => 'Admin - Employees',
    
    // Incidents
    'incidents.create' => 'Incidents',
    'incidents.edit' => 'Incidents',
    'incidents.investigations.create' => 'Incidents - Investigations',
    'incidents.investigations.edit' => 'Incidents - Investigations',
    'incidents.rca.create' => 'Incidents - RCA',
    'incidents.rca.edit' => 'Incidents - RCA',
    'incidents.capas.create' => 'Incidents - CAPAs',
    'incidents.capas.edit' => 'Incidents - CAPAs',
    
    // PPE
    'ppe.items.create' => 'PPE - Items',
    'ppe.items.edit' => 'PPE - Items',
    'ppe.issuances.create' => 'PPE - Issuances',
    'ppe.suppliers.create' => 'PPE - Suppliers',
    'ppe.suppliers.edit' => 'PPE - Suppliers',
    'ppe.inspections.create' => 'PPE - Inspections',
    'ppe.reports.create' => 'PPE - Reports',
    
    // Procurement
    'procurement.requests.create' => 'Procurement - Requests',
    'procurement.requests.edit' => 'Procurement - Requests',
    'procurement.suppliers.create' => 'Procurement - Suppliers',
    'procurement.suppliers.edit' => 'Procurement - Suppliers',
    'procurement.equipment-certifications.create' => 'Procurement - Equipment Certifications',
    'procurement.stock-reports.create' => 'Procurement - Stock Reports',
    'procurement.stock-reports.edit' => 'Procurement - Stock Reports',
    
    // Risk Assessment
    'risk-assessment.risk-assessments.create' => 'Risk Assessment',
    'risk-assessment.risk-assessments.edit' => 'Risk Assessment',
    'risk-assessment.hazards.create' => 'Risk Assessment - Hazards',
    'risk-assessment.hazards.edit' => 'Risk Assessment - Hazards',
    'risk-assessment.control-measures.create' => 'Risk Assessment - Control Measures',
    'risk-assessment.control-measures.edit' => 'Risk Assessment - Control Measures',
    'risk-assessment.jsas.create' => 'Risk Assessment - JSAs',
    'risk-assessment.jsas.edit' => 'Risk Assessment - JSas',
    'risk-assessment.risk-reviews.create' => 'Risk Assessment - Risk Reviews',
    'risk-assessment.risk-reviews.edit' => 'Risk Assessment - Risk Reviews',
    
    // Training
    'training.training-needs.create' => 'Training - Needs',
    'training.training-needs.edit' => 'Training - Needs',
    'training.training-plans.create' => 'Training - Plans',
    'training.training-plans.edit' => 'Training - Plans',
    'training.training-sessions.create' => 'Training - Sessions',
    'training.training-sessions.edit' => 'Training - Sessions',
    
    // Toolbox Talks
    'toolbox-talks.create' => 'Toolbox Talks',
    'toolbox-talks.edit' => 'Toolbox Talks',
    'toolbox-topics.create' => 'Toolbox Topics',
    
    // Work Permits
    'work-permits.create' => 'Work Permits',
    'work-permits.edit' => 'Work Permits',
    'work-permits.types.create' => 'Work Permits - Types',
    'work-permits.types.edit' => 'Work Permits - Types',
    'work-permits.gca-logs.create' => 'Work Permits - GCA Logs',
    'work-permits.gca-logs.edit' => 'Work Permits - GCA Logs',
    
    // Inspections
    'inspections.create' => 'Inspections',
    'inspections.edit' => 'Inspections',
    'inspections.schedules.create' => 'Inspections - Schedules',
    'inspections.schedules.edit' => 'Inspections - Schedules',
    'inspections.checklists.create' => 'Inspections - Checklists',
    'inspections.checklists.edit' => 'Inspections - Checklists',
    'inspections.audits.create' => 'Inspections - Audits',
    'inspections.audits.edit' => 'Inspections - Audits',
    'inspections.ncrs.create' => 'Inspections - NCRs',
    'inspections.ncrs.edit' => 'Inspections - NCRs',
    'inspections.audit-findings.create' => 'Inspections - Audit Findings',
    'inspections.audit-findings.edit' => 'Inspections - Audit Findings',
    
    // Emergency
    'emergency.contacts.create' => 'Emergency - Contacts',
    'emergency.contacts.edit' => 'Emergency - Contacts',
    'emergency.equipment.create' => 'Emergency - Equipment',
    'emergency.equipment.edit' => 'Emergency - Equipment',
    'emergency.fire-drills.create' => 'Emergency - Fire Drills',
    'emergency.fire-drills.edit' => 'Emergency - Fire Drills',
    'emergency.evacuation-plans.create' => 'Emergency - Evacuation Plans',
    'emergency.evacuation-plans.edit' => 'Emergency - Evacuation Plans',
    'emergency.response-teams.create' => 'Emergency - Response Teams',
    'emergency.response-teams.edit' => 'Emergency - Response Teams',
    
    // Environmental
    'environmental.waste-management.create' => 'Environmental - Waste Management',
    'environmental.waste-management.edit' => 'Environmental - Waste Management',
    
    // Health
    'health.surveillance.create' => 'Health - Surveillance',
    
    // Documents
    'documents.hse-documents.create' => 'Documents - HSE Documents',
    'documents.hse-documents.edit' => 'Documents - HSE Documents',
    'documents.versions.create' => 'Documents - Versions',
    'documents.versions.edit' => 'Documents - Versions',
    'documents.templates.create' => 'Documents - Templates',
    'documents.templates.edit' => 'Documents - Templates',
    
    // Compliance
    'compliance.requirements.create' => 'Compliance - Requirements',
    'compliance.requirements.edit' => 'Compliance - Requirements',
    'compliance.permits-licenses.create' => 'Compliance - Permits & Licenses',
    'compliance.permits-licenses.edit' => 'Compliance - Permits & Licenses',
    'compliance.audits.create' => 'Compliance - Audits',
    'compliance.audits.edit' => 'Compliance - Audits',
    
    // Housekeeping
    'housekeeping.inspections.create' => 'Housekeeping - Inspections',
    'housekeeping.inspections.edit' => 'Housekeeping - Inspections',
    'housekeeping.5s-audits.create' => 'Housekeeping - 5S Audits',
    'housekeeping.5s-audits.edit' => 'Housekeeping - 5S Audits',
    
    // Waste & Sustainability
    'waste-sustainability.records.create' => 'Waste & Sustainability - Records',
    'waste-sustainability.records.edit' => 'Waste & Sustainability - Records',
    'waste-sustainability.carbon-footprint.create' => 'Waste & Sustainability - Carbon Footprint',
    'waste-sustainability.carbon-footprint.edit' => 'Waste & Sustainability - Carbon Footprint',
    
    // Notifications
    'notifications.rules.create' => 'Notifications - Rules',
    'notifications.rules.edit' => 'Notifications - Rules',
    'notifications.escalation-matrices.create' => 'Notifications - Escalation Matrices',
    'notifications.escalation-matrices.edit' => 'Notifications - Escalation Matrices',
    
    // Safety Communications
    'safety-communications.create' => 'Safety Communications',
];

echo "Testing " . count($formRoutes) . " form routes...\n\n";

foreach ($formRoutes as $routeName => $module) {
    $formsTested++;
    
    try {
        // Check if route exists
        $route = $routes->getByName($routeName);
        
        if ($route) {
            $uri = $route->uri();
            $methods = $route->methods();
            
            // Check if it's a GET route (form display)
            if (in_array('GET', $methods) || in_array('HEAD', $methods)) {
                echo "✓ {$module} - {$routeName}\n";
                echo "  Route: {$uri}\n";
                $formsPassed++;
                $results[] = [
                    'module' => $module,
                    'route' => $routeName,
                    'status' => 'PASS',
                    'uri' => $uri
                ];
            } else {
                echo "⚠ {$module} - {$routeName} (Not a GET route)\n";
                $formsSkipped++;
                $results[] = [
                    'module' => $module,
                    'route' => $routeName,
                    'status' => 'SKIP',
                    'reason' => 'Not a GET route'
                ];
            }
        } else {
            echo "✗ {$module} - {$routeName} (Route not found)\n";
            $formsFailed++;
            $results[] = [
                'module' => $module,
                'route' => $routeName,
                'status' => 'FAIL',
                'reason' => 'Route not found'
            ];
        }
    } catch (Exception $e) {
        echo "✗ {$module} - {$routeName} (Error: {$e->getMessage()})\n";
        $formsFailed++;
        $results[] = [
            'module' => $module,
            'route' => $routeName,
            'status' => 'FAIL',
            'reason' => $e->getMessage()
        ];
    }
}

echo "\n========================================\n";
echo "Test Summary\n";
echo "========================================\n";
echo "Forms Tested: {$formsTested}\n";
echo "Forms Passed: {$formsPassed}\n";
echo "Forms Failed: {$formsFailed}\n";
echo "Forms Skipped: {$formsSkipped}\n";
echo "\n";

// Generate detailed report
echo "========================================\n";
echo "Failed Routes\n";
echo "========================================\n";
$failedRoutes = array_filter($results, function($r) { return $r['status'] === 'FAIL'; });
if (count($failedRoutes) > 0) {
    foreach ($failedRoutes as $result) {
        echo "✗ {$result['module']} - {$result['route']}\n";
        echo "  Reason: {$result['reason']}\n";
    }
} else {
    echo "No failed routes!\n";
}

echo "\n========================================\n";
echo "Skipped Routes\n";
echo "========================================\n";
$skippedRoutes = array_filter($results, function($r) { return $r['status'] === 'SKIP'; });
if (count($skippedRoutes) > 0) {
    foreach ($skippedRoutes as $result) {
        echo "⚠ {$result['module']} - {$result['route']}\n";
        echo "  Reason: {$result['reason']}\n";
    }
} else {
    echo "No skipped routes!\n";
}

echo "\n";

if ($formsFailed === 0) {
    echo "✅ All form routes are registered!\n";
    echo "\nNext Steps:\n";
    echo "1. Test forms manually in browser\n";
    echo "2. Verify form validation\n";
    echo "3. Test form submission\n";
    echo "4. Verify data saving\n";
    exit(0);
} else {
    echo "❌ Some form routes are missing. Please review the failed routes above.\n";
    exit(1);
}

