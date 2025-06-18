<?php

/**
 * Demo Setup Script for Attendance Integration
 * Creates a sample project structure for testing the integration
 */

class DemoSetup
{
    private $baseDir;

    public function __construct($baseDir = './projects')
    {
        $this->baseDir = rtrim($baseDir, '/');
    }

    public function createDemoStructure()
    {
        echo "Creating demo project structure...\n";

        // Create projects directory
        if (!is_dir($this->baseDir)) {
            mkdir($this->baseDir, 0755, true);
        }

        // Create sample Laravel apps
        $apps = [
            'attendance-app' => 'Source attendance system',
            'main-app' => 'Primary multi-tenant application', 
            'client-app' => 'Client-specific application',
            'hr-portal' => 'HR management portal'
        ];

        foreach ($apps as $appName => $description) {
            $this->createMockLaravelApp($appName, $description);
        }

        echo "Demo structure created successfully!\n\n";
        $this->printInstructions();
    }

    private function createMockLaravelApp($name, $description)
    {
        $appDir = $this->baseDir . '/' . $name;
        
        if (!is_dir($appDir)) {
            mkdir($appDir, 0755, true);
        }

        // Create basic Laravel structure
        $directories = [
            'app/Models',
            'app/Http/Controllers', 
            'database/migrations',
            'resources/views',
            'public',
            'config'
        ];

        foreach ($directories as $dir) {
            $fullPath = $appDir . '/' . $dir;
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
        }

        // Create artisan file
        file_put_contents($appDir . '/artisan', "#!/usr/bin/env php\n<?php\n// Mock artisan file for $name");
        chmod($appDir . '/artisan', 0755);

        // Create composer.json
        $composer = [
            'name' => "demo/$name",
            'description' => $description,
            'type' => 'project',
            'require' => [
                'php' => '^8.0',
                'laravel/framework' => '^9.0'
            ]
        ];

        // Add tenant package for non-attendance apps
        if ($name !== 'attendance-app') {
            $composer['require']['stancl/tenancy'] = '^3.0';
        }

        file_put_contents($appDir . '/composer.json', json_encode($composer, JSON_PRETTY_PRINT));

        echo "  Created: $name ($description)\n";
    }

    private function printInstructions()
    {
        echo "Next steps:\n";
        echo "1. Generate configuration:\n";
        echo "   php generate-config.php {$this->baseDir}\n\n";
        echo "2. Run integration:\n";
        echo "   ./batch-integrate.sh -p {$this->baseDir}\n\n";
        echo "3. Or run specific app:\n";
        echo "   ./batch-integrate.sh -p {$this->baseDir} -t main-app\n\n";
    }
}

// Run demo setup
if (php_sapi_name() === 'cli') {
    $baseDir = $argv[1] ?? './projects';
    $demo = new DemoSetup($baseDir);
    $demo->createDemoStructure();
}