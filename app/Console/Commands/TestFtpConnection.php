<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestFtpConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:test {disk=ftp}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test FTP/SFTP connection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $disk = $this->argument('disk');
        
        $this->info("Testing {$disk} connection...");
        
        try {
            // Test write
            $testFile = 'test_' . time() . '.txt';
            $testContent = 'FTP Connection Test - ' . now()->toDateTimeString();
            
            $this->info("Attempting to write test file...");
            Storage::disk($disk)->put($testFile, $testContent);
            $this->info("✓ File written successfully!");
            
            // Test read
            $this->info("Attempting to read test file...");
            $content = Storage::disk($disk)->get($testFile);
            $this->info("✓ File read successfully!");
            $this->line("Content: {$content}");
            
            // Test exists
            $this->info("Checking if file exists...");
            $exists = Storage::disk($disk)->exists($testFile);
            $this->info($exists ? "✓ File exists!" : "✗ File not found");
            
            // Test delete
            $this->info("Attempting to delete test file...");
            Storage::disk($disk)->delete($testFile);
            $this->info("✓ File deleted successfully!");
            
            // List files
            $this->info("Listing files in root directory...");
            $files = Storage::disk($disk)->files();
            $this->info("Found " . count($files) . " files");
            if (count($files) > 0) {
                $this->line("Sample files:");
                foreach (array_slice($files, 0, 5) as $file) {
                    $this->line("  - {$file}");
                }
            }
            
            $this->info("\n✓ FTP connection test successful!");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("✗ FTP connection failed!");
            $this->error("Error: " . $e->getMessage());
            $this->line("\nTroubleshooting:");
            $this->line("1. Check FTP credentials in .env file");
            $this->line("2. Verify FTP server is accessible");
            $this->line("3. Check firewall allows FTP port");
            $this->line("4. Test with FTP client (FileZilla, WinSCP)");
            return 1;
        }
    }
}
