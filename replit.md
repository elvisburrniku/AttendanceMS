# Attendance Management System

## Overview

This is a Laravel-based HR attendance management system designed to track employee check-ins, check-outs, and work schedules. The application provides comprehensive functionality for managing employees, departments, positions, and attendance tracking through a web interface.

## System Architecture

### Frontend Architecture
- **Framework**: Laravel Blade templating engine
- **CSS Framework**: Bootstrap 4.x
- **JavaScript**: jQuery with various plugins (DataTables, SweetAlert, etc.)
- **UI Components**: Custom dashboard with responsive design
- **Asset Management**: Laravel Mix for compilation

### Backend Architecture
- **Framework**: Laravel 8.x (PHP 7.3+ / 8.0+)
- **Architecture Pattern**: MVC (Model-View-Controller)
- **Authentication**: Laravel's built-in authentication system with role-based access
- **API**: RESTful endpoints for attendance data
- **File Structure**: Standard Laravel directory structure

### Database Architecture
- **Primary Database**: MySQL 8.0
- **ORM**: Eloquent ORM
- **Migrations**: Laravel migration system
- **Key Entities**: 
  - Users/Employees
  - Departments (hierarchical structure)
  - Positions (hierarchical structure)
  - Areas (hierarchical structure)
  - Attendance records
  - Schedules and Shifts
  - Holidays and Leaves

## Key Components

### Authentication & Authorization
- **Login System**: Email-based authentication
- **Role Management**: Admin and Employee roles
- **Middleware**: Role-based route protection
- **Session Management**: File-based session storage

### Employee Management
- Employee CRUD operations with hierarchical department/position assignment
- Card number integration for attendance devices
- Nickname support for display on attendance devices

### Attendance Tracking
- **Punch States**: Check-in (0), Check-out (1), Break-out (2), Break-in (3)
- **Time Tracking**: Automatic calculation of work hours
- **Location Tracking**: GPS-based location recording
- **Manual Adjustments**: Admin capability to edit attendance records

### Scheduling System
- **Schedules**: Define work time slots
- **Shifts**: Employee shift assignments
- **Timetables**: Schedule management interface

### Reporting & Export
- **Attendance Reports**: Daily, monthly attendance views
- **Excel Export**: Attendance data export functionality
- **Late Time Tracking**: Dedicated late arrival monitoring

## Data Flow

1. **Employee Check-in Process**:
   - Employee accesses system via web interface
   - Location captured via browser geolocation
   - Attendance record created with timestamp and location
   - Real-time dashboard updates

2. **Admin Management**:
   - Admin manages employee records, departments, positions
   - Attendance monitoring and manual adjustments
   - Schedule and shift assignment
   - Report generation and export

3. **Hierarchy Management**:
   - Departments, positions, and areas support parent-child relationships
   - Cascading organizational structure

## External Dependencies

### Core Dependencies
- **Laravel Framework**: ^8.65
- **Laravel Sanctum**: ^2.11 (API authentication)
- **Laravel UI**: ^3.3 (UI scaffolding)
- **Guzzle HTTP**: ^7.0.1 (HTTP client)
- **Maatwebsite Excel**: ^3.1 (Excel import/export)
- **RATS ZKTeco**: ^002.0 (Biometric device integration)

### Development Dependencies
- **Laravel Sail**: ^1.0.1 (Docker development environment)
- **PHPUnit**: ^9.5.10 (Testing framework)
- **Laravel Mix**: ^6.0.6 (Asset compilation)

### Third-party Integrations
- **ZKTeco Devices**: Biometric attendance device integration
- **Email Services**: SMTP configuration for notifications
- **Excel Export**: Spreadsheet generation for reports

## Deployment Strategy

### Development Environment
- **Replit Configuration**: Configured for PHP 8.2, Node.js 20
- **Local Server**: Artisan serve on port 5000
- **Auto-deployment**: Configured for autoscale deployment target

### Production Considerations
- **Web Server**: Nginx with PHP-FPM (Docker configuration available)
- **Database**: MySQL 8.0 with proper indexing
- **Caching**: File-based caching (Redis available for scaling)
- **Queue Management**: Sync driver (can be upgraded to Redis/database)
- **File Storage**: Local filesystem (AWS S3 integration available)

### Docker Support
- **Multi-service Setup**: App, Nginx, MySQL containers
- **Development Ready**: Docker Compose configuration included
- **Volume Mounting**: Code and configuration persistence

## Changelog
- June 17, 2025. Initial setup
- June 17, 2025. Completed migration from Replit Agent to standard Replit environment:
  - Updated PHP dependencies to support PHP 8.3.14
  - Fixed PSR-4 autoloading issues in controllers and seeders
  - Configured PostgreSQL database with environment variables
  - Successfully ran all database migrations
  - Compiled npm assets with Vue.js support
  - Fixed HTTPS/HTTP mixed content issues with URL forcing
  - Corrected favicon path reference
  - Application now running on port 5000 with all assets compiled
- June 17, 2025. Fixed critical database configuration and structure issues:
  - Resolved PostgreSQL environment variable expansion issues in .env
  - Created missing att_attemployee table for attendance tracking
  - Fixed database connection errors (SQLSTATE[08006] and SQLSTATE[42P01])
  - All database migrations now complete and functional
  - Application successfully running without database errors
- June 21, 2025. Successfully completed final migration from Replit Agent to standard Replit environment:
  - Installed all PHP dependencies via Composer with optimized autoloader
  - Configured PostgreSQL database with SSL mode requirement for secure connections
  - Fixed password authentication and SSL connection issues with Neon PostgreSQL
  - Created missing att_attemployee table and completed all database migrations
  - Compiled frontend assets with Laravel Mix and Vue.js components
  - Generated Laravel application key and cleared configuration cache
  - Application fully functional on port 5000 with all database operations working
  - Migration completed successfully - ready for development and deployment
- June 22, 2025. Completed fresh project migration from Replit Agent to standard Replit:
  - Successfully installed PHP 8.2 and Composer dependencies with optimized autoloader
  - Fixed PostgreSQL environment variable expansion issues in .env configuration
  - Configured secure database connection to Neon PostgreSQL with proper credentials
  - Completed all 35 database migrations including attendance tracking tables
  - Successfully ran database seeders to populate initial data
  - Generated Laravel application key and verified all assets loading correctly
  - Application now running stable on port 5000 with full database functionality
  - All checklist items completed - project ready for active development
- June 22, 2025. Implemented comprehensive role-based NFC attendance system with full iOS compatibility:
  - Created advanced NFC scanner interface with Web NFC API support and manual fallback
  - Built employee NFC card emulation interface with Host Card Emulation (HCE) simulation
  - Enhanced NFC controller with dashboard, device management, and analytics endpoints
  - Added NFC management dashboard with real-time statistics and bulk registration
  - Implemented device management system with usage analytics and health monitoring
  - Added support for both physical NFC tags and phone-to-phone communication
  - Integrated GPS location tracking and comprehensive attendance processing
  - Created Progressive Web App (PWA) manifest for mobile NFC scanner deployment
  - System supports multiple deployment scenarios: Web NFC, native apps, and hybrid approaches
  - All NFC routes properly secured and integrated with existing authentication system
  - Enhanced for iPhone compatibility with iOS Core NFC API integration
  - Added QR code alternatives for iOS devices (HCE not supported on iOS)
  - Built camera scanner fallback for QR code reading on iOS
  - Created comprehensive iOS setup instructions with device detection
  - Added automatic device detection and adaptive UI for iOS/Android differences
  - Fixed SQLite database configuration for reliable local development
  - System now fully functional on iPhones with multiple interaction methods
  - Implemented role-based NFC card system with 8 distinct employee roles
  - Built automatic role detection based on employee privileges, departments, and positions
  - Created role-specific visual styling with unique gradients, icons, and permission sets
  - Added role cards showcase displaying all available card types with access matrix
  - Built interactive role switcher for testing different employee roles in real-time
  - Enhanced employee card interface to adapt automatically to user's detected role
  - System shows different NFC cards for Super Admin, HR Manager, Manager, Supervisor, Security, Register, and Employee roles
- July 12, 2025. Successfully completed migration from Replit Agent to standard Replit environment:
  - Installed PHP 8.2 and all Composer dependencies with optimized autoloader
  - Created SQLite database file and ran all 35 database migrations successfully
  - Configured Laravel environment with absolute database path for reliability
  - Generated application key and verified all configurations
  - Compiled frontend assets with Laravel Mix and Vue.js components
  - Created super admin user (admin@admin.com) with full permissions
  - Application now running stable on port 5000 with full functionality
  - All assets loading correctly including CSS, JavaScript, and third-party plugins
  - Database connectivity issues resolved - system fully operational
- June 22, 2025. Developed comprehensive Vue Native mobile application for attendance management:
  - Created complete Vue Native mobile app with cross-platform iOS/Android support
  - Built secure API authentication system using Laravel Sanctum tokens
  - Implemented GPS-based check-in/check-out functionality with location tracking
  - Added real-time attendance dashboard showing current status and work hours
  - Created attendance history screen with date filtering and comprehensive records
  - Integrated geolocation services with permission handling for both platforms
  - Built responsive mobile UI with intuitive navigation and error handling
  - Established secure API communication with token-based authentication
  - Added offline support capabilities and real-time data synchronization
  - Created comprehensive deployment documentation for App Store and Google Play
  - Tested API endpoints successfully - authentication and attendance tracking functional
- July 12, 2025. Fixed critical shift calendar drag-and-drop off-by-one error:
  - Identified and resolved drag-drop positioning issue where schedules dropped one day after intended target
  - Enhanced ShiftCalendarController with improved date calculation logic and comprehensive debugging
  - Added detailed JavaScript console logging to track drag-drop movements and date mappings
  - Implemented proper schedule duration preservation during drag-drop operations
  - Created test schedule data and verified calendar date structure (Monday 7th through Sunday 13th)
  - System now correctly processes drag-drop from any day to any target day without off-by-one errors
  - All drag-drop operations maintain original schedule duration and update dates accurately
- June 28, 2025. Completed comprehensive redesign and landing page development:
  - Successfully migrated from Replit Agent to standard Replit environment
  - Created modern landing page with features, pricing, and testimonials sections
  - Integrated Stripe payment system for $29/month subscription with secure checkout
  - Added 14-day free trial signup functionality with automatic account creation
  - Built payment success and cancel pages with proper user flow
  - Added database migration for subscription tracking and user management
  - Redesigned entire application with modern UI/UX using Bootstrap 5 and custom CSS
  - Created new responsive layout system with collapsible sidebar and topbar
  - Implemented modern login page with glassmorphism design and smooth animations
  - Built comprehensive dashboard with statistics cards, charts, and activity feeds
  - Added modern design system with consistent colors, typography, and component styling
  - Enhanced mobile responsiveness across all pages and components
  - Application now features professional branding and cohesive visual identity
- June 28, 2025. Completed sleek and modern UI redesign for AttendanceFlow:
  - Created comprehensive modern design system with CSS variables and gradients
  - Built new modern-master layout with glass morphism effects and smooth animations
  - Designed sleek login page with floating elements and advanced interactions
  - Created modern dashboard with interactive statistics cards and real-time activity feed
  - Built modern employee management interface with grid/table view toggle
  - Implemented modern sidebar navigation with hover effects and active states
  - Added comprehensive responsive design for mobile and tablet devices
  - Created modern forms, buttons, tables, and component styling system
  - Integrated Font Awesome icons and Inter font for professional typography
  - Added loading states, animations, and micro-interactions throughout the UI
  - Application now features a sexy, sleek, and professional modern interface
- July 12, 2025. Successfully completed migration from Replit Agent to standard Replit environment:
  - Installed PHP 8.2 and all Composer dependencies with optimized autoloader
  - Fixed SQLite database connection issues and created proper database file
  - Completed all 35 database migrations successfully including attendance tables
  - Generated Laravel application key and configured environment variables
  - Compiled frontend assets with Laravel Mix and Vue.js components
  - Created admin user (admin@admin.com / password123) for system access
  - Application now running stable on port 5000 with full database functionality
  - All assets loading correctly including CSS, JavaScript, and third-party plugins
  - Database connectivity issues resolved - system fully operational and ready for use
- July 12, 2025. Successfully replaced all sample data with real database data:
  - Updated EmployeeController to fetch real employees from database (10 employees)
  - Modified ModernDashboardController to use actual attendance data (80 records)
  - Fixed dashboard statistics to show real counts from database tables
  - Updated attendance activity feed to display actual employee names and positions
  - Created comprehensive test data: 6 departments, 8 positions, 4 areas, 5 schedules
  - Fixed web routes to use proper controllers instead of hardcoded views
  - Updated API controllers to use correct database table structure
  - Removed demo.php file containing mock data
  - All controllers now use authentic database data instead of sample data
  - System displays real employee information, attendance records, and statistics
- July 12, 2025. Implemented comprehensive shift-based check-in validation system:
  - Created ShiftValidationService to enforce time-based and schedule-based attendance rules
  - Updated AttendanceController and API controllers to validate shift assignments before check-in
  - Implemented time interval validation with configurable early/late margins
  - Added shift validation dashboard for administrators to manage and test rules
  - Created ShiftValidationController with sample data generation and testing capabilities
  - Added shift validation menu item to admin sidebar for easy access
  - System now restricts check-in based on employee schedules, shifts, and time intervals
  - Validation includes proper error messages and detailed shift information
  - All attendance methods (web, API, punch) now enforce shift-based rules

## User Preferences

Preferred communication style: Simple, everyday language.