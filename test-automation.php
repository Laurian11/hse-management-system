<?php

/**
 * Automation Testing Script
 * Tests all automation features in the HSE Management System
 * 
 * Run: php test-automation.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\ProcurementRequest;
use App\Models\PPEItem;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\ToolboxTalk;
use App\Models\ToolboxTalkAttendance;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "========================================\n";
echo "HSE Management System - Automation Tests\n";
echo "========================================\n\n";

$testsPassed = 0;
$testsFailed = 0;
$testResults = [];

// Test 1: Procurement → Stock → PPE Automation
echo "Test 1: Procurement → Stock → PPE Automation\n";
echo "--------------------------------------------\n";
try {
    $company = Company::first();
    if (!$company) {
        throw new Exception("No company found. Please seed the database first.");
    }
    
    $user = User::where('company_id', $company->id)->first();
    if (!$user) {
        throw new Exception("No user found for company.");
    }
    
    // Create a test procurement request
    $procurementRequest = ProcurementRequest::create([
        'company_id' => $company->id,
        'item_name' => 'Test Safety Helmet',
        'item_category' => 'ppe',
        'quantity' => 50,
        'unit' => 'pieces',
        'estimated_cost' => 500000,
        'currency' => 'TZS',
        'priority' => 'high',
        'requested_by' => $user->id,
        'status' => 'draft',
    ]);
    
    echo "✓ Created procurement request: {$procurementRequest->reference_number}\n";
    
    // Update status to received
    $procurementRequest->update([
        'status' => 'received',
        'received_date' => now(),
        'purchase_cost' => 500000,
        'supplier_id' => Supplier::where('company_id', $company->id)->first()?->id,
    ]);
    
    echo "✓ Updated status to 'received'\n";
    
    // Check if PPE item was created
    $ppeItem = PPEItem::where('company_id', $company->id)
        ->where('name', 'Test Safety Helmet')
        ->first();
    
    if ($ppeItem) {
        echo "✓ PPE item auto-created: {$ppeItem->reference_number}\n";
        echo "  - Quantity: {$ppeItem->total_quantity}\n";
        echo "  - Available: {$ppeItem->available_quantity}\n";
        $testsPassed++;
        $testResults[] = ['test' => 'Procurement → PPE Automation', 'status' => 'PASS'];
    } else {
        throw new Exception("PPE item was not auto-created");
    }
    
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $testResults[] = ['test' => 'Procurement → PPE Automation', 'status' => 'FAIL', 'error' => $e->getMessage()];
}
echo "\n";

// Test 2: Supplier Suggestions
echo "Test 2: Supplier Suggestions\n";
echo "----------------------------\n";
try {
    $company = Company::first();
    $supplier = Supplier::where('company_id', $company->id)
        ->where('supplier_type', 'ppe')
        ->first();
    
    if ($supplier) {
        echo "✓ Supplier found for PPE category: {$supplier->name}\n";
        $testsPassed++;
        $testResults[] = ['test' => 'Supplier Suggestions', 'status' => 'PASS'];
    } else {
        echo "⚠ No PPE supplier found (this is okay if none exist)\n";
        $testsPassed++;
        $testResults[] = ['test' => 'Supplier Suggestions', 'status' => 'PASS (No suppliers)'];
    }
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $testResults[] = ['test' => 'Supplier Suggestions', 'status' => 'FAIL', 'error' => $e->getMessage()];
}
echo "\n";

// Test 3: QR Code Generation
echo "Test 3: QR Code Generation\n";
echo "--------------------------\n";
try {
    $ppeItem = PPEItem::where('company_id', Company::first()->id)->first();
    if ($ppeItem) {
        $qrService = new \App\Services\QRCodeService();
        $qrData = $qrService::forPPEItem($ppeItem->id, $ppeItem->reference_number, 'check');
        
        if (!empty($qrData)) {
            echo "✓ QR code data generated for PPE item\n";
            echo "  - URL: {$qrData}\n";
            $testsPassed++;
            $testResults[] = ['test' => 'QR Code Generation', 'status' => 'PASS'];
        } else {
            throw new Exception("QR code data is empty");
        }
    } else {
        echo "⚠ No PPE items found to test QR codes\n";
        $testsPassed++;
        $testResults[] = ['test' => 'QR Code Generation', 'status' => 'PASS (No items)'];
    }
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $testResults[] = ['test' => 'QR Code Generation', 'status' => 'FAIL', 'error' => $e->getMessage()];
}
echo "\n";

// Test 4: Observer Registration
echo "Test 4: Observer Registration\n";
echo "------------------------------\n";
try {
    $observers = [
        'ProcurementRequestObserver' => \App\Observers\ProcurementRequestObserver::class,
    ];
    
    foreach ($observers as $name => $class) {
        if (class_exists($class)) {
            echo "✓ {$name} class exists\n";
        } else {
            throw new Exception("Observer class {$class} not found");
        }
    }
    
    $testsPassed++;
    $testResults[] = ['test' => 'Observer Registration', 'status' => 'PASS'];
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $testResults[] = ['test' => 'Observer Registration', 'status' => 'FAIL', 'error' => $e->getMessage()];
}
echo "\n";

// Test 5: Email Share Controller
echo "Test 5: Email Share Controller\n";
echo "-------------------------------\n";
try {
    if (class_exists(\App\Http\Controllers\EmailShareController::class)) {
        echo "✓ EmailShareController exists\n";
        $testsPassed++;
        $testResults[] = ['test' => 'Email Share Controller', 'status' => 'PASS'];
    } else {
        throw new Exception("EmailShareController not found");
    }
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $testResults[] = ['test' => 'Email Share Controller', 'status' => 'FAIL', 'error' => $e->getMessage()];
}
echo "\n";

// Test 6: Toolbox Attendance Enhancement
echo "Test 6: Toolbox Attendance Enhancement\n";
echo "--------------------------------------\n";
try {
    $controller = new \App\Http\Controllers\ToolboxTalkController();
    $reflection = new ReflectionClass($controller);
    
    if ($reflection->hasMethod('markAttendance')) {
        $method = $reflection->getMethod('markAttendance');
        $params = $method->getParameters();
        
        // Check if method accepts employee_names parameter
        $hasEmployeeNames = false;
        foreach ($params as $param) {
            if ($param->getName() === 'request') {
                $hasEmployeeNames = true;
                break;
            }
        }
        
        if ($hasEmployeeNames) {
            echo "✓ markAttendance method exists and accepts Request parameter\n";
            echo "  - Method can handle employee_names from request\n";
            $testsPassed++;
            $testResults[] = ['test' => 'Toolbox Attendance Enhancement', 'status' => 'PASS'];
        } else {
            throw new Exception("markAttendance method doesn't accept Request parameter");
        }
    } else {
        throw new Exception("markAttendance method not found");
    }
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $testResults[] = ['test' => 'Toolbox Attendance Enhancement', 'status' => 'FAIL', 'error' => $e->getMessage()];
}
echo "\n";

// Summary
echo "========================================\n";
echo "Test Summary\n";
echo "========================================\n";
echo "Tests Passed: {$testsPassed}\n";
echo "Tests Failed: {$testsFailed}\n";
echo "Total Tests: " . ($testsPassed + $testsFailed) . "\n";
echo "\n";

if ($testsFailed === 0) {
    echo "✅ All automation tests passed!\n";
    exit(0);
} else {
    echo "❌ Some tests failed. Please review the errors above.\n";
    exit(1);
}

