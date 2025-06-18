<?php

/**
 * Integration Test Script
 * Tests the attendance integration toolkit
 */

class IntegrationTester
{
    private $projectsDir;
    private $sourceApp;
    private $testResults = [];

    public function __construct($projectsDir = './projects', $sourceApp = 'attendance-app')
    {
        $this->projectsDir = rtrim($projectsDir, '/');
        $this->sourceApp = $sourceApp;
    }

    public function runTests()
    {
        echo "=== Attendance Integration Test Suite ===\n\n";

        $this->testRequiredFiles();
        $this->testProjectStructure();
        $this->testLaravelDetection();
        $this->testConfigGeneration();
        $this->testScriptPermissions();

        $this->printResults();
    }

    private function testRequiredFiles()
    {
        echo "Testing required files...\n";

        $requiredFiles = [
            'attendance-integration-script.php' => 'Core integration script',
            'batch-integrate.sh' => 'Linux/Mac batch script',
            'batch-integrate.bat' => 'Windows batch script',
            'generate-config.php' => 'Configuration generator',
            'README.md' => 'Documentation',
            'ATTENDANCE_INTEGRATION_PLAN.md' => 'Detailed plan',
            'INTEGRATION_QUICKSTART.md' => 'Quick start guide'
        ];

        foreach ($requiredFiles as $file => $description) {
            if (file_exists($file)) {
                $this->pass("File exists: $file");
            } else {
                $this->fail("Missing file: $file ($description)");
            }
        }
    }

    private function testProjectStructure()
    {
        echo "\nTesting project structure...\n";

        if (is_dir($this->projectsDir)) {
            $this->pass("Projects directory exists: {$this->projectsDir}");

            $sourceDir = $this->projectsDir . '/' . $this->sourceApp;
            if (is_dir($sourceDir)) {
                $this->pass("Source app directory exists: $sourceDir");

                if (file_exists($sourceDir . '/artisan')) {
                    $this->pass("Source app has artisan file");
                } else {
                    $this->fail("Source app missing artisan file");
                }

                if (file_exists($sourceDir . '/composer.json')) {
                    $this->pass("Source app has composer.json");
                } else {
                    $this->fail("Source app missing composer.json");
                }
            } else {
                $this->fail("Source app directory not found: $sourceDir");
            }
        } else {
            $this->fail("Projects directory not found: {$this->projectsDir}");
        }
    }

    private function testLaravelDetection()
    {
        echo "\nTesting Laravel app detection...\n";

        if (!is_dir($this->projectsDir)) {
            $this->fail("Cannot test Laravel detection - projects directory missing");
            return;
        }

        $laravelApps = [];
        foreach (scandir($this->projectsDir) as $item) {
            $path = $this->projectsDir . '/' . $item;
            if ($item !== '.' && $item !== '..' && is_dir($path)) {
                if ($this->isLaravelApp($path)) {
                    $laravelApps[] = $item;
                }
            }
        }

        if (count($laravelApps) > 0) {
            $this->pass("Found " . count($laravelApps) . " Laravel apps: " . implode(', ', $laravelApps));
        } else {
            $this->fail("No Laravel apps detected in projects directory");
        }
    }

    private function testConfigGeneration()
    {
        echo "\nTesting configuration generation...\n";

        if (file_exists('generate-config.php')) {
            $this->pass("Configuration generator exists");

            // Test if we can create a sample config
            $sampleConfig = [
                'exclude_apps' => ['test-app'],
                'include_apps' => [],
                'auto_migrate' => true,
                'auto_sync_users' => true,
                'backup_before_integration' => true
            ];

            $testConfigFile = 'test-config.json';
            file_put_contents($testConfigFile, json_encode($sampleConfig, JSON_PRETTY_PRINT));

            if (file_exists($testConfigFile)) {
                $this->pass("Can create configuration files");
                unlink($testConfigFile); // Clean up
            } else {
                $this->fail("Cannot create configuration files");
            }
        } else {
            $this->fail("Configuration generator missing");
        }
    }

    private function testScriptPermissions()
    {
        echo "\nTesting script permissions...\n";

        if (file_exists('batch-integrate.sh')) {
            if (is_executable('batch-integrate.sh')) {
                $this->pass("Bash script is executable");
            } else {
                $this->fail("Bash script is not executable (run: chmod +x batch-integrate.sh)");
            }
        }

        // Test PHP syntax
        $phpFiles = [
            'attendance-integration-script.php',
            'generate-config.php',
            'demo-setup.php'
        ];

        foreach ($phpFiles as $file) {
            if (file_exists($file)) {
                $output = [];
                $return = 0;
                exec("php -l $file 2>&1", $output, $return);
                
                if ($return === 0) {
                    $this->pass("PHP syntax valid: $file");
                } else {
                    $this->fail("PHP syntax error in $file: " . implode(' ', $output));
                }
            }
        }
    }

    private function isLaravelApp($path)
    {
        return file_exists($path . '/artisan') && file_exists($path . '/composer.json');
    }

    private function pass($message)
    {
        echo "  ✓ $message\n";
        $this->testResults[] = ['status' => 'pass', 'message' => $message];
    }

    private function fail($message)
    {
        echo "  ✗ $message\n";
        $this->testResults[] = ['status' => 'fail', 'message' => $message];
    }

    private function printResults()
    {
        echo "\n=== Test Results ===\n";

        $passed = count(array_filter($this->testResults, function($r) { return $r['status'] === 'pass'; }));
        $failed = count(array_filter($this->testResults, function($r) { return $r['status'] === 'fail'; }));
        $total = count($this->testResults);

        echo "Passed: $passed\n";
        echo "Failed: $failed\n";
        echo "Total:  $total\n\n";

        if ($failed === 0) {
            echo "🎉 All tests passed! The integration toolkit is ready to use.\n\n";
            $this->printUsageInstructions();
        } else {
            echo "❌ Some tests failed. Please fix the issues above before proceeding.\n";
        }
    }

    private function printUsageInstructions()
    {
        echo "Usage Instructions:\n";
        echo "1. Set up demo projects: php demo-setup.php\n";
        echo "2. Generate config: php generate-config.php\n";
        echo "3. Run integration: ./batch-integrate.sh\n\n";
        echo "For specific app: ./batch-integrate.sh -t app-name\n";
        echo "For help: ./batch-integrate.sh --help\n";
    }
}

// Run tests
if (php_sapi_name() === 'cli') {
    $projectsDir = $argv[1] ?? './projects';
    $sourceApp = $argv[2] ?? 'attendance-app';
    
    $tester = new IntegrationTester($projectsDir, $sourceApp);
    $tester->runTests();
}