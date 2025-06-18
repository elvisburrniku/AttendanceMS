@echo off
setlocal enabledelayedexpansion

REM Batch Attendance Integration Script for Windows
REM Integrates attendance system into multiple Laravel applications

set "PROJECTS_DIR=.\projects"
set "SOURCE_APP=attendance-app"
set "CONFIG_FILE=integration-config.json"
set "TARGET_APP="
set "LIST_ONLY=false"

:parse_args
if "%~1"=="" goto :main
if "%~1"=="-p" (
    set "PROJECTS_DIR=%~2"
    shift
    shift
    goto :parse_args
)
if "%~1"=="--projects-dir" (
    set "PROJECTS_DIR=%~2"
    shift
    shift
    goto :parse_args
)
if "%~1"=="-s" (
    set "SOURCE_APP=%~2"
    shift
    shift
    goto :parse_args
)
if "%~1"=="--source-app" (
    set "SOURCE_APP=%~2"
    shift
    shift
    goto :parse_args
)
if "%~1"=="-c" (
    set "CONFIG_FILE=%~2"
    shift
    shift
    goto :parse_args
)
if "%~1"=="--config" (
    set "CONFIG_FILE=%~2"
    shift
    shift
    goto :parse_args
)
if "%~1"=="-t" (
    set "TARGET_APP=%~2"
    shift
    shift
    goto :parse_args
)
if "%~1"=="--target-app" (
    set "TARGET_APP=%~2"
    shift
    shift
    goto :parse_args
)
if "%~1"=="-l" (
    set "LIST_ONLY=true"
    shift
    goto :parse_args
)
if "%~1"=="--list" (
    set "LIST_ONLY=true"
    shift
    goto :parse_args
)
if "%~1"=="-h" goto :show_usage
if "%~1"=="--help" goto :show_usage
echo Unknown option: %~1
goto :show_usage

:show_usage
echo Batch Attendance Integration Script for Windows
echo.
echo Usage: %~n0 [OPTIONS]
echo.
echo Options:
echo     -p, --projects-dir DIR     Directory containing Laravel projects (default: .\projects)
echo     -s, --source-app NAME      Name of source attendance app (default: attendance-app)
echo     -c, --config FILE          Configuration file (default: integration-config.json)
echo     -t, --target-app NAME      Integrate into specific app only
echo     -l, --list                 List available Laravel applications
echo     -h, --help                 Show this help message
echo.
echo Examples:
echo     %~n0                                           # Integrate into all apps in .\projects
echo     %~n0 -p C:\www -s attendance-system           # Custom projects directory and source
echo     %~n0 -t my-app                                # Integrate into specific app only
echo     %~n0 -l                                       # List available applications
goto :eof

:print_header
echo ===============================================================================
echo === %~1 ===
echo ===============================================================================
goto :eof

:print_status
echo [INFO] %~1
goto :eof

:print_warning
echo [WARNING] %~1
goto :eof

:print_error
echo [ERROR] %~1
goto :eof

:is_laravel_app
if exist "%~1\artisan" (
    if exist "%~1\composer.json" (
        exit /b 0
    )
)
exit /b 1

:list_apps
call :print_header "Available Laravel Applications in %PROJECTS_DIR%"

if not exist "%PROJECTS_DIR%" (
    call :print_error "Projects directory not found: %PROJECTS_DIR%"
    exit /b 1
)

set "count=0"
for /d %%d in ("%PROJECTS_DIR%\*") do (
    call :is_laravel_app "%%d"
    if !errorlevel! equ 0 (
        set "app_name=%%~nd"
        echo   - !app_name!
        set /a count+=1
    )
)

if !count! equ 0 (
    call :print_warning "No Laravel applications found in %PROJECTS_DIR%"
) else (
    call :print_status "Found !count! Laravel application(s)"
)
goto :eof

:create_default_config
echo Creating default configuration file: %CONFIG_FILE%
(
echo {
echo     "exclude_apps": [],
echo     "include_apps": [],
echo     "auto_migrate": true,
echo     "auto_sync_users": true,
echo     "backup_before_integration": true,
echo     "post_integration_commands": [
echo         "php artisan config:clear",
echo         "php artisan route:clear",
echo         "php artisan view:clear"
echo     ]
echo }
) > "%CONFIG_FILE%"
goto :eof

:backup_app
set "app_dir=%~1"
set "app_name=%~n1"
for /f "tokens=1-4 delims=/ " %%i in ('date /t') do set "mydate=%%l%%j%%k"
for /f "tokens=1-2 delims=: " %%i in ('time /t') do set "mytime=%%i%%j"
set "backup_dir=%app_dir%_backup_%mydate%_%mytime%"

call :print_status "Creating backup of %app_name%..."
xcopy "%app_dir%" "%backup_dir%" /e /i /h /y >nul
call :print_status "Backup created: %backup_dir%"
goto :eof

:integrate_app
set "source_dir=%~1"
set "target_dir=%~2"
set "app_name=%~n2"

call :print_header "Integrating attendance system into %app_name%"

REM Check if backup is needed (simplified - always backup)
call :backup_app "%target_dir%"

REM Run integration script
call :print_status "Running PHP integration script for %app_name%..."
php attendance-integration-script.php --source="%source_dir%" --target="%target_dir%"

if !errorlevel! equ 0 (
    call :print_status "Integration script completed successfully for %app_name%"
    
    REM Change to target directory
    pushd "%target_dir%"
    
    REM Run migrations
    call :print_status "Running migrations for %app_name%..."
    php artisan migrate --force
    
    REM Sync users
    call :print_status "Syncing users for %app_name%..."
    php artisan attendance:sync-users
    
    REM Clear caches
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    popd
    
    call :print_status "Successfully integrated attendance system into %app_name%"
    exit /b 0
) else (
    call :print_error "Integration failed for %app_name%"
    exit /b 1
)

:integrate_all_apps
set "source_dir=%PROJECTS_DIR%\%SOURCE_APP%"
set "processed=0"
set "failed=0"

call :print_header "Starting batch integration"

REM Validate source directory
if not exist "%source_dir%" (
    call :print_error "Source attendance app not found: %source_dir%"
    exit /b 1
)

call :is_laravel_app "%source_dir%"
if !errorlevel! neq 0 (
    call :print_error "Source directory is not a Laravel application: %source_dir%"
    exit /b 1
)

call :print_status "Source attendance app: %source_dir%"

REM Process each Laravel application
for /d %%d in ("%PROJECTS_DIR%\*") do (
    set "app_name=%%~nd"
    
    REM Skip source app
    if not "!app_name!"=="%SOURCE_APP%" (
        call :is_laravel_app "%%d"
        if !errorlevel! equ 0 (
            call :print_status "Processing !app_name!..."
            
            call :integrate_app "%source_dir%" "%%d"
            if !errorlevel! equ 0 (
                set /a processed+=1
            ) else (
                set /a failed+=1
            )
            echo.
        )
    )
)

REM Print summary
call :print_header "Integration Summary"
call :print_status "Applications processed: %processed%"
if %failed% gtr 0 (
    call :print_error "Applications failed: %failed%"
)
goto :eof

:integrate_specific_app
set "target_app=%~1"
set "source_dir=%PROJECTS_DIR%\%SOURCE_APP%"
set "target_dir=%PROJECTS_DIR%\%target_app%"

REM Validate directories
if not exist "%source_dir%" (
    call :print_error "Source attendance app not found: %source_dir%"
    exit /b 1
)

if not exist "%target_dir%" (
    call :print_error "Target app not found: %target_dir%"
    exit /b 1
)

call :is_laravel_app "%target_dir%"
if !errorlevel! neq 0 (
    call :print_error "Target directory is not a Laravel application: %target_dir%"
    exit /b 1
)

call :integrate_app "%source_dir%" "%target_dir%"
goto :eof

:main
call :print_header "Batch Attendance Integration Script for Windows"

REM List apps if requested
if "%LIST_ONLY%"=="true" (
    call :list_apps
    goto :eof
)

REM Check if attendance integration script exists
if not exist "attendance-integration-script.php" (
    call :print_error "attendance-integration-script.php not found in current directory"
    exit /b 1
)

REM Create default config if it doesn't exist
if not exist "%CONFIG_FILE%" (
    call :create_default_config
)

REM Execute based on parameters
if not "%TARGET_APP%"=="" (
    call :integrate_specific_app "%TARGET_APP%"
) else (
    call :integrate_all_apps
)

call :print_header "Batch Integration Complete"