#!/bin/bash

# Batch Attendance Integration Script
# Integrates attendance system into multiple Laravel applications

set -e

# Default configuration
PROJECTS_DIR="./projects"
SOURCE_APP="attendance-app"
CONFIG_FILE="integration-config.json"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header() {
    echo -e "${BLUE}=== $1 ===${NC}"
}

# Function to show usage
show_usage() {
    cat << EOF
Batch Attendance Integration Script

Usage: $0 [OPTIONS]

Options:
    -p, --projects-dir DIR     Directory containing Laravel projects (default: ./projects)
    -s, --source-app NAME      Name of source attendance app (default: attendance-app)
    -c, --config FILE          Configuration file (default: integration-config.json)
    -t, --target-app NAME      Integrate into specific app only
    -l, --list                 List available Laravel applications
    -h, --help                 Show this help message

Examples:
    $0                                          # Integrate into all apps in ./projects
    $0 -p /var/www -s attendance-system        # Custom projects directory and source
    $0 -t my-app                               # Integrate into specific app only
    $0 -l                                      # List available applications

Configuration file format (integration-config.json):
{
    "exclude_apps": ["test-app", "backup-app"],
    "include_apps": ["main-app", "client-app"],
    "auto_migrate": true,
    "auto_sync_users": true,
    "backup_before_integration": true
}
EOF
}

# Function to create default config
create_default_config() {
    cat > "$CONFIG_FILE" << 'EOF'
{
    "exclude_apps": [],
    "include_apps": [],
    "auto_migrate": true,
    "auto_sync_users": true,
    "backup_before_integration": true,
    "post_integration_commands": [
        "php artisan config:clear",
        "php artisan route:clear",
        "php artisan view:clear"
    ]
}
EOF
    print_status "Created default configuration file: $CONFIG_FILE"
}

# Function to validate Laravel application
is_laravel_app() {
    local app_dir="$1"
    [[ -f "$app_dir/artisan" && -f "$app_dir/composer.json" ]]
}

# Function to check if app uses multi-tenancy
is_multitenant_app() {
    local app_dir="$1"
    grep -q "stancl/tenancy\|spatie/laravel-multitenancy" "$app_dir/composer.json" 2>/dev/null
}

# Function to list Laravel applications
list_apps() {
    print_header "Available Laravel Applications in $PROJECTS_DIR"
    
    if [[ ! -d "$PROJECTS_DIR" ]]; then
        print_error "Projects directory not found: $PROJECTS_DIR"
        return 1
    fi
    
    local count=0
    for app_dir in "$PROJECTS_DIR"/*; do
        if [[ -d "$app_dir" ]] && is_laravel_app "$app_dir"; then
            local app_name=$(basename "$app_dir")
            local multitenant=""
            if is_multitenant_app "$app_dir"; then
                multitenant=" ${GREEN}(Multi-tenant)${NC}"
            fi
            echo -e "  - $app_name$multitenant"
            ((count++))
        fi
    done
    
    if [[ $count -eq 0 ]]; then
        print_warning "No Laravel applications found in $PROJECTS_DIR"
    else
        print_status "Found $count Laravel application(s)"
    fi
}

# Function to backup application
backup_app() {
    local app_dir="$1"
    local app_name=$(basename "$app_dir")
    local backup_dir="${app_dir}_backup_$(date +%Y%m%d_%H%M%S)"
    
    print_status "Creating backup of $app_name..."
    cp -r "$app_dir" "$backup_dir"
    print_status "Backup created: $backup_dir"
}

# Function to load configuration
load_config() {
    if [[ -f "$CONFIG_FILE" ]]; then
        print_status "Loading configuration from $CONFIG_FILE"
    else
        print_warning "Configuration file not found. Creating default..."
        create_default_config
    fi
}

# Function to check if app should be processed
should_process_app() {
    local app_name="$1"
    
    # Check if jq is available for JSON parsing
    if ! command -v jq &> /dev/null; then
        print_warning "jq not found. Processing all apps except common exclusions."
        # Default exclusions
        case "$app_name" in
            "test-"*|"backup-"*|"old-"*|"archive-"*) return 1 ;;
            *) return 0 ;;
        esac
    fi
    
    # Parse JSON config
    local exclude_apps=$(jq -r '.exclude_apps[]?' "$CONFIG_FILE" 2>/dev/null || echo "")
    local include_apps=$(jq -r '.include_apps[]?' "$CONFIG_FILE" 2>/dev/null || echo "")
    
    # Check exclude list
    if echo "$exclude_apps" | grep -q "^$app_name$"; then
        return 1
    fi
    
    # If include list is specified and not empty, only process apps in the list
    if [[ -n "$include_apps" ]] && ! echo "$include_apps" | grep -q "^$app_name$"; then
        return 1
    fi
    
    return 0
}

# Function to get config value
get_config_value() {
    local key="$1"
    local default="$2"
    
    if command -v jq &> /dev/null && [[ -f "$CONFIG_FILE" ]]; then
        jq -r ".$key // \"$default\"" "$CONFIG_FILE" 2>/dev/null || echo "$default"
    else
        echo "$default"
    fi
}

# Function to run post-integration commands
run_post_integration_commands() {
    local app_dir="$1"
    local app_name=$(basename "$app_dir")
    
    if ! command -v jq &> /dev/null; then
        return 0
    fi
    
    local commands=$(jq -r '.post_integration_commands[]?' "$CONFIG_FILE" 2>/dev/null)
    
    if [[ -n "$commands" ]]; then
        print_status "Running post-integration commands for $app_name..."
        cd "$app_dir"
        
        while IFS= read -r cmd; do
            if [[ -n "$cmd" ]]; then
                print_status "Executing: $cmd"
                eval "$cmd" || print_warning "Command failed: $cmd"
            fi
        done <<< "$commands"
        
        cd - > /dev/null
    fi
}

# Function to integrate attendance system into an app
integrate_app() {
    local source_dir="$1"
    local target_dir="$2"
    local app_name=$(basename "$target_dir")
    
    print_header "Integrating attendance system into $app_name"
    
    # Check if backup is needed
    local backup_before=$(get_config_value "backup_before_integration" "true")
    if [[ "$backup_before" == "true" ]]; then
        backup_app "$target_dir"
    fi
    
    # Run integration script
    if php attendance-integration-script.php --source="$source_dir" --target="$target_dir"; then
        print_status "Integration script completed successfully for $app_name"
        
        # Change to target directory for Laravel commands
        cd "$target_dir"
        
        # Run migrations if configured
        local auto_migrate=$(get_config_value "auto_migrate" "true")
        if [[ "$auto_migrate" == "true" ]]; then
            print_status "Running migrations for $app_name..."
            php artisan migrate --force || print_warning "Migration failed for $app_name"
        fi
        
        # Sync users if configured
        local auto_sync=$(get_config_value "auto_sync_users" "true")
        if [[ "$auto_sync" == "true" ]]; then
            print_status "Syncing users for $app_name..."
            php artisan attendance:sync-users || print_warning "User sync failed for $app_name"
        fi
        
        # Run post-integration commands
        run_post_integration_commands "$target_dir"
        
        cd - > /dev/null
        
        print_status "Successfully integrated attendance system into $app_name"
        
    else
        print_error "Integration failed for $app_name"
        return 1
    fi
}

# Function to integrate into all apps
integrate_all_apps() {
    local source_dir="$PROJECTS_DIR/$SOURCE_APP"
    local processed=0
    local failed=0
    
    print_header "Starting batch integration"
    
    # Validate source directory
    if [[ ! -d "$source_dir" ]]; then
        print_error "Source attendance app not found: $source_dir"
        return 1
    fi
    
    if ! is_laravel_app "$source_dir"; then
        print_error "Source directory is not a Laravel application: $source_dir"
        return 1
    fi
    
    print_status "Source attendance app: $source_dir"
    
    # Process each Laravel application
    for app_dir in "$PROJECTS_DIR"/*; do
        if [[ -d "$app_dir" ]] && is_laravel_app "$app_dir"; then
            local app_name=$(basename "$app_dir")
            
            # Skip source app
            if [[ "$app_name" == "$SOURCE_APP" ]]; then
                continue
            fi
            
            # Check if app should be processed
            if should_process_app "$app_name"; then
                print_status "Processing $app_name..."
                
                if integrate_app "$source_dir" "$app_dir"; then
                    ((processed++))
                else
                    ((failed++))
                fi
                
                echo # Add spacing between apps
            else
                print_status "Skipping $app_name (excluded by configuration)"
            fi
        fi
    done
    
    # Print summary
    print_header "Integration Summary"
    print_status "Applications processed: $processed"
    if [[ $failed -gt 0 ]]; then
        print_error "Applications failed: $failed"
    fi
}

# Function to integrate into specific app
integrate_specific_app() {
    local target_app="$1"
    local source_dir="$PROJECTS_DIR/$SOURCE_APP"
    local target_dir="$PROJECTS_DIR/$target_app"
    
    # Validate directories
    if [[ ! -d "$source_dir" ]]; then
        print_error "Source attendance app not found: $source_dir"
        return 1
    fi
    
    if [[ ! -d "$target_dir" ]]; then
        print_error "Target app not found: $target_dir"
        return 1
    fi
    
    if ! is_laravel_app "$target_dir"; then
        print_error "Target directory is not a Laravel application: $target_dir"
        return 1
    fi
    
    integrate_app "$source_dir" "$target_dir"
}

# Parse command line arguments
PROJECTS_DIR="./projects"
SOURCE_APP="attendance-app"
CONFIG_FILE="integration-config.json"
TARGET_APP=""
LIST_ONLY=false

while [[ $# -gt 0 ]]; do
    case $1 in
        -p|--projects-dir)
            PROJECTS_DIR="$2"
            shift 2
            ;;
        -s|--source-app)
            SOURCE_APP="$2"
            shift 2
            ;;
        -c|--config)
            CONFIG_FILE="$2"
            shift 2
            ;;
        -t|--target-app)
            TARGET_APP="$2"
            shift 2
            ;;
        -l|--list)
            LIST_ONLY=true
            shift
            ;;
        -h|--help)
            show_usage
            exit 0
            ;;
        *)
            print_error "Unknown option: $1"
            show_usage
            exit 1
            ;;
    esac
done

# Main execution
print_header "Batch Attendance Integration Script"

# List apps if requested
if [[ "$LIST_ONLY" == "true" ]]; then
    list_apps
    exit 0
fi

# Load configuration
load_config

# Check if attendance integration script exists
if [[ ! -f "attendance-integration-script.php" ]]; then
    print_error "attendance-integration-script.php not found in current directory"
    exit 1
fi

# Execute based on parameters
if [[ -n "$TARGET_APP" ]]; then
    integrate_specific_app "$TARGET_APP"
else
    integrate_all_apps
fi

print_header "Batch Integration Complete"