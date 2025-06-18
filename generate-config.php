<?php

/**
 * Configuration Generator for Attendance Integration
 * Generates custom configuration files for different project structures
 */

class ConfigGenerator
{
    private $projectsDir;
    private $config;

    public function __construct($projectsDir = './projects')
    {
        $this->projectsDir = rtrim($projectsDir, '/');
        $this->config = $this->getDefaultConfig();
    }

    private function getDefaultConfig()
    {
        return [
            'exclude_apps' => [],
            'include_apps' => [],
            'auto_migrate' => true,
            'auto_sync_users' => true,
            'backup_before_integration' => true,
            'post_integration_commands' => [
                'php artisan config:clear',
                'php artisan route:clear',
                'php artisan view:clear'
            ],
            'tenant_settings' => [
                'detect_tenant_package' => true,
                'tenant_column' => 'tenant_id',
                'auto_add_tenant_scopes' => true
            ],
            'notification_settings' => [
                'send_integration_summary' => false,
                'email_recipients' => [],
                'slack_webhook' => ''
            ]
        ];
    }

    public function scanProjects()
    {
        if (!is_dir($this->projectsDir)) {
            throw new Exception("Projects directory not found: {$this->projectsDir}");
        }

        $apps = [];
        $tenantApps = [];
        $excludeApps = [];

        foreach (scandir($this->projectsDir) as $item) {
            $path = $this->projectsDir . '/' . $item;
            
            if ($item === '.' || $item === '..' || !is_dir($path)) {
                continue;
            }

            if ($this->isLaravelApp($path)) {
                $apps[] = $item;
                
                if ($this->isMultiTenantApp($path)) {
                    $tenantApps[] = $item;
                }

                // Auto-exclude common patterns
                if ($this->shouldAutoExclude($item)) {
                    $excludeApps[] = $item;
                }
            }
        }

        return [
            'all_apps' => $apps,
            'tenant_apps' => $tenantApps,
            'suggested_excludes' => $excludeApps
        ];
    }

    private function isLaravelApp($path)
    {
        return file_exists($path . '/artisan') && file_exists($path . '/composer.json');
    }

    private function isMultiTenantApp($path)
    {
        $composerPath = $path . '/composer.json';
        if (!file_exists($composerPath)) {
            return false;
        }

        $composer = json_decode(file_get_contents($composerPath), true);
        if (!$composer || !isset($composer['require'])) {
            return false;
        }

        $tenantPackages = [
            'stancl/tenancy',
            'spatie/laravel-multitenancy',
            'hyn/multi-tenant'
        ];

        foreach ($tenantPackages as $package) {
            if (isset($composer['require'][$package])) {
                return true;
            }
        }

        return false;
    }

    private function shouldAutoExclude($appName)
    {
        $excludePatterns = [
            '/^test-/',
            '/^testing-/',
            '/^backup-/',
            '/^old-/',
            '/^archive-/',
            '/^temp-/',
            '/^demo-/',
            '/-backup$/',
            '/-old$/',
            '/-archive$/'
        ];

        foreach ($excludePatterns as $pattern) {
            if (preg_match($pattern, $appName)) {
                return true;
            }
        }

        return false;
    }

    public function generateInteractiveConfig()
    {
        $scan = $this->scanProjects();
        
        echo "=== Attendance Integration Configuration Generator ===\n\n";
        echo "Found " . count($scan['all_apps']) . " Laravel applications:\n";
        
        foreach ($scan['all_apps'] as $app) {
            $status = in_array($app, $scan['tenant_apps']) ? ' (Multi-tenant)' : '';
            $exclude = in_array($app, $scan['suggested_excludes']) ? ' [Suggested exclude]' : '';
            echo "  - $app$status$exclude\n";
        }
        
        echo "\n";

        // Source app selection
        $sourceApp = $this->promptSourceApp($scan['all_apps']);
        
        // Exclude apps selection
        $excludeApps = $this->promptExcludeApps($scan['all_apps'], $scan['suggested_excludes'], $sourceApp);
        
        // Include apps selection (if not excluding)
        $includeApps = $this->promptIncludeApps($scan['all_apps'], $excludeApps, $sourceApp);
        
        // Migration settings
        $autoMigrate = $this->promptYesNo("Run migrations automatically after integration?", true);
        $autoSync = $this->promptYesNo("Sync existing users automatically?", true);
        $backup = $this->promptYesNo("Create backup before integration?", true);
        
        // Advanced settings
        $advanced = $this->promptYesNo("Configure advanced settings?", false);
        
        if ($advanced) {
            $this->configureAdvancedSettings();
        }

        // Update configuration
        $this->config['exclude_apps'] = $excludeApps;
        $this->config['include_apps'] = $includeApps;
        $this->config['auto_migrate'] = $autoMigrate;
        $this->config['auto_sync_users'] = $autoSync;
        $this->config['backup_before_integration'] = $backup;

        return [
            'config' => $this->config,
            'source_app' => $sourceApp,
            'scan_results' => $scan
        ];
    }

    private function promptSourceApp($apps)
    {
        echo "Select source attendance application:\n";
        
        $attendanceApps = array_filter($apps, function($app) {
            return stripos($app, 'attendance') !== false || 
                   stripos($app, 'hr') !== false ||
                   stripos($app, 'time') !== false;
        });

        if (count($attendanceApps) === 1) {
            $suggested = reset($attendanceApps);
            echo "Suggested: $suggested\n";
            if ($this->promptYesNo("Use suggested source app?", true)) {
                return $suggested;
            }
        }

        foreach ($apps as $index => $app) {
            echo "  " . ($index + 1) . ") $app\n";
        }

        do {
            $choice = (int)readline("Enter choice (1-" . count($apps) . "): ") - 1;
        } while ($choice < 0 || $choice >= count($apps));

        return $apps[$choice];
    }

    private function promptExcludeApps($apps, $suggestedExcludes, $sourceApp)
    {
        $excludeApps = [];
        
        if (!empty($suggestedExcludes)) {
            echo "\nSuggested apps to exclude:\n";
            foreach ($suggestedExcludes as $app) {
                echo "  - $app\n";
            }
            
            if ($this->promptYesNo("Exclude suggested apps?", true)) {
                $excludeApps = $suggestedExcludes;
            }
        }

        if ($this->promptYesNo("Manually select additional apps to exclude?", false)) {
            $availableApps = array_filter($apps, function($app) use ($excludeApps, $sourceApp) {
                return !in_array($app, $excludeApps) && $app !== $sourceApp;
            });

            echo "Available apps:\n";
            foreach ($availableApps as $index => $app) {
                echo "  " . ($index + 1) . ") $app\n";
            }

            echo "Enter app numbers to exclude (comma-separated, or 0 for none): ";
            $input = trim(readline());
            
            if ($input !== '0' && $input !== '') {
                $choices = array_map('trim', explode(',', $input));
                foreach ($choices as $choice) {
                    $index = (int)$choice - 1;
                    if (isset(array_values($availableApps)[$index])) {
                        $excludeApps[] = array_values($availableApps)[$index];
                    }
                }
            }
        }

        return array_unique($excludeApps);
    }

    private function promptIncludeApps($apps, $excludeApps, $sourceApp)
    {
        if ($this->promptYesNo("Specify only certain apps to include (instead of all non-excluded)?", false)) {
            $availableApps = array_filter($apps, function($app) use ($excludeApps, $sourceApp) {
                return !in_array($app, $excludeApps) && $app !== $sourceApp;
            });

            echo "Available apps:\n";
            foreach ($availableApps as $index => $app) {
                echo "  " . ($index + 1) . ") $app\n";
            }

            echo "Enter app numbers to include (comma-separated): ";
            $input = trim(readline());
            
            $includeApps = [];
            if ($input !== '') {
                $choices = array_map('trim', explode(',', $input));
                foreach ($choices as $choice) {
                    $index = (int)$choice - 1;
                    if (isset(array_values($availableApps)[$index])) {
                        $includeApps[] = array_values($availableApps)[$index];
                    }
                }
            }

            return $includeApps;
        }

        return [];
    }

    private function configureAdvancedSettings()
    {
        echo "\n=== Advanced Settings ===\n";
        
        // Tenant settings
        if ($this->promptYesNo("Configure tenant-specific settings?", false)) {
            $this->config['tenant_settings']['auto_add_tenant_scopes'] = 
                $this->promptYesNo("Automatically add tenant scopes to models?", true);
                
            echo "Tenant column name (default: tenant_id): ";
            $tenantColumn = trim(readline());
            if ($tenantColumn) {
                $this->config['tenant_settings']['tenant_column'] = $tenantColumn;
            }
        }

        // Post-integration commands
        if ($this->promptYesNo("Add custom post-integration commands?", false)) {
            echo "Enter commands (one per line, empty line to finish):\n";
            $commands = [];
            while (true) {
                $cmd = trim(readline());
                if ($cmd === '') break;
                $commands[] = $cmd;
            }
            
            if (!empty($commands)) {
                $this->config['post_integration_commands'] = array_merge(
                    $this->config['post_integration_commands'],
                    $commands
                );
            }
        }

        // Notification settings
        if ($this->promptYesNo("Configure notifications?", false)) {
            $this->config['notification_settings']['send_integration_summary'] = true;
            
            echo "Email recipients (comma-separated): ";
            $emails = trim(readline());
            if ($emails) {
                $this->config['notification_settings']['email_recipients'] = 
                    array_map('trim', explode(',', $emails));
            }
            
            echo "Slack webhook URL (optional): ";
            $slack = trim(readline());
            if ($slack) {
                $this->config['notification_settings']['slack_webhook'] = $slack;
            }
        }
    }

    private function promptYesNo($question, $default = null)
    {
        $defaultText = $default === null ? '' : ($default ? ' [Y/n]' : ' [y/N]');
        echo "$question$defaultText: ";
        
        $input = strtolower(trim(readline()));
        
        if ($input === '') {
            return $default;
        }
        
        return in_array($input, ['y', 'yes', '1', 'true']);
    }

    public function saveConfig($filename, $config, $sourceApp)
    {
        $fullConfig = array_merge($config, [
            'generated_at' => date('Y-m-d H:i:s'),
            'projects_dir' => $this->projectsDir,
            'source_app' => $sourceApp
        ]);

        file_put_contents($filename, json_encode($fullConfig, JSON_PRETTY_PRINT));
        
        echo "\nConfiguration saved to: $filename\n";
        return $fullConfig;
    }

    public function generateBashScript($sourceApp, $config)
    {
        $script = "#!/bin/bash\n\n";
        $script .= "# Auto-generated integration script\n";
        $script .= "# Generated on: " . date('Y-m-d H:i:s') . "\n\n";
        $script .= "PROJECTS_DIR=\"{$this->projectsDir}\"\n";
        $script .= "SOURCE_APP=\"$sourceApp\"\n";
        $script .= "CONFIG_FILE=\"integration-config.json\"\n\n";
        $script .= "./batch-integrate.sh -p \"\$PROJECTS_DIR\" -s \"\$SOURCE_APP\" -c \"\$CONFIG_FILE\"\n";

        file_put_contents('run-integration.sh', $script);
        chmod('run-integration.sh', 0755);
        
        echo "Generated bash script: run-integration.sh\n";
    }

    public function generateBatchScript($sourceApp, $config)
    {
        $script = "@echo off\n";
        $script .= "REM Auto-generated integration script\n";
        $script .= "REM Generated on: " . date('Y-m-d H:i:s') . "\n\n";
        $script .= "set \"PROJECTS_DIR={$this->projectsDir}\"\n";
        $script .= "set \"SOURCE_APP=$sourceApp\"\n";
        $script .= "set \"CONFIG_FILE=integration-config.json\"\n\n";
        $script .= "batch-integrate.bat -p \"%PROJECTS_DIR%\" -s \"%SOURCE_APP%\" -c \"%CONFIG_FILE%\"\n";
        $script .= "pause\n";

        file_put_contents('run-integration.bat', $script);
        
        echo "Generated batch script: run-integration.bat\n";
    }
}

// CLI execution
if (php_sapi_name() === 'cli') {
    $projectsDir = $argv[1] ?? './projects';
    
    try {
        $generator = new ConfigGenerator($projectsDir);
        $result = $generator->generateInteractiveConfig();
        
        $filename = 'integration-config.json';
        $generator->saveConfig($filename, $result['config'], $result['source_app']);
        
        echo "\n=== Generated Files ===\n";
        $generator->generateBashScript($result['source_app'], $result['config']);
        $generator->generateBatchScript($result['source_app'], $result['config']);
        
        echo "\nTo run the integration:\n";
        echo "  Linux/Mac: ./run-integration.sh\n";
        echo "  Windows:   run-integration.bat\n";
        echo "  Manual:    ./batch-integrate.sh -c integration-config.json\n";
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}