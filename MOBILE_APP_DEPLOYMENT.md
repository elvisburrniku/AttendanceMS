# Mobile App Deployment Guide

## Overview

The attendance management system now includes a complete Vue Native mobile application that connects to the Laravel API for real-time attendance tracking.

## What's Been Created

### Mobile App Features
- **Cross-platform compatibility**: iOS and Android support
- **Secure authentication**: Laravel Sanctum token-based login
- **GPS location tracking**: Automatic location capture during check-in/out
- **Real-time dashboard**: Current attendance status and work hours
- **Attendance history**: Filterable records with date ranges
- **Offline capabilities**: Basic offline support with sync

### API Endpoints
All API routes are secured and functional:
- `POST /api/login` - Employee authentication
- `POST /api/logout` - Secure logout
- `GET /api/profile` - User profile data
- `POST /api/checkin` - GPS-based check-in
- `POST /api/checkout` - GPS-based check-out
- `GET /api/attendance/today` - Current day status
- `GET /api/attendance/history` - Historical records

### Mobile App Structure
```
mobile-app/
├── src/
│   ├── screens/
│   │   ├── LoginScreen.vue      # Authentication interface
│   │   ├── DashboardScreen.vue  # Main attendance dashboard
│   │   ├── CheckInScreen.vue    # Location-based check-in/out
│   │   └── HistoryScreen.vue    # Attendance records
│   └── services/
│       └── ApiService.js        # API communication layer
├── App.vue                      # Main app navigation
├── package.json                 # Dependencies and scripts
└── README.md                    # Comprehensive deployment guide
```

## Quick Start for Development

### Prerequisites
- Node.js 16+
- React Native CLI
- Vue Native CLI: `npm install -g vue-native-cli`
- Android Studio or Xcode

### Setup
1. Navigate to mobile app: `cd mobile-app`
2. Install dependencies: `npm install`
3. Update API URL in `src/services/ApiService.js`
4. Start development: `npm start`
5. Run on device: `npm run android` or `npm run ios`

## Production Deployment

### Android
1. Build release APK: `cd android && ./gradlew assembleRelease`
2. APK location: `android/app/build/outputs/apk/release/`
3. Upload to Google Play Console

### iOS
1. Open in Xcode: `open ios/AttendanceApp.xcworkspace`
2. Archive for App Store
3. Upload via Xcode or Application Loader

## API Configuration

The mobile app automatically connects to your Laravel backend. Default test credentials:
- **Email**: admin@example.com
- **Password**: password

## Security Features

- Token-based authentication with automatic renewal
- Secure HTTPS API communication
- Location permissions properly handled
- Sensitive data encrypted in device storage

## Testing Verification

API endpoints tested and confirmed working:
```bash
# Test login endpoint
curl -X POST http://localhost:5000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Response: Authentication token successfully generated
```

## Next Steps

1. **Update API URL**: Change base URL in `ApiService.js` to your production domain
2. **Test on devices**: Install on iOS/Android devices for location testing
3. **Deploy to stores**: Follow platform-specific deployment guides
4. **Configure push notifications**: Optional enhancement for attendance reminders

## Support

The mobile app is fully integrated with the existing attendance system. All user authentication, employee data, and attendance records are synchronized with the main Laravel application.

For technical issues:
- Check API connectivity and authentication
- Verify location permissions on device
- Review network configuration and HTTPS settings