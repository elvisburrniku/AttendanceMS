# Attendance Management Mobile App

A Vue Native mobile application for employee attendance tracking with check-in/check-out functionality, GPS location tracking, and real-time synchronization with the Laravel backend API.

## Features

- **Employee Authentication**: Secure login using email and password
- **Real-time Check-in/Check-out**: Location-based attendance tracking
- **GPS Location Tracking**: Automatic location capture for attendance records
- **Attendance History**: View past attendance records with filtering options
- **Real-time Dashboard**: Current attendance status and work hours
- **Offline Support**: Basic offline functionality with sync when online

## Tech Stack

- **Vue Native**: Cross-platform mobile development framework
- **React Native**: Native mobile components
- **Axios**: HTTP client for API communication
- **AsyncStorage**: Local data storage
- **Geolocation**: GPS location services
- **Laravel Sanctum**: API authentication

## Prerequisites

Before setting up the mobile app, ensure you have:

- Node.js 16+ installed
- React Native CLI
- Android Studio (for Android development)
- Xcode (for iOS development on macOS)
- Vue Native CLI: `npm install -g vue-native-cli`

## Installation

1. **Navigate to mobile app directory**:
   ```bash
   cd mobile-app
   ```

2. **Install dependencies**:
   ```bash
   npm install
   ```

3. **Install iOS dependencies** (iOS only):
   ```bash
   cd ios && pod install && cd ..
   ```

## Configuration

1. **Update API endpoint**:
   Edit `src/services/ApiService.js` and update the `baseURL`:
   ```javascript
   this.baseURL = 'https://your-domain.com/api'; // Replace with your server URL
   ```

2. **Environment Configuration**:
   Create `.env` file in the mobile-app directory:
   ```
   API_BASE_URL=https://your-domain.com/api
   APP_NAME=Attendance Management
   ```

## Development

### Running the Development Server

1. **Start Metro bundler**:
   ```bash
   npm start
   ```

2. **Run on Android**:
   ```bash
   npm run android
   ```

3. **Run on iOS**:
   ```bash
   npm run ios
   ```

### Building for Production

#### Android APK Build

1. **Generate signed APK**:
   ```bash
   cd android
   ./gradlew assembleRelease
   ```

2. **The APK will be generated at**:
   ```
   android/app/build/outputs/apk/release/app-release.apk
   ```

#### iOS App Store Build

1. **Open iOS project in Xcode**:
   ```bash
   open ios/AttendanceApp.xcworkspace
   ```

2. **Archive and upload to App Store Connect**:
   - Select "Product" → "Archive"
   - Follow Xcode distribution workflow

## API Integration

The mobile app connects to the Laravel backend using these endpoints:

### Authentication
- `POST /api/login` - Employee login
- `POST /api/logout` - Employee logout
- `GET /api/profile` - Get user profile

### Attendance
- `POST /api/checkin` - Employee check-in
- `POST /api/checkout` - Employee check-out
- `GET /api/attendance/today` - Today's attendance
- `GET /api/attendance/history` - Attendance history

## Permissions

The app requires the following permissions:

### Android (android/app/src/main/AndroidManifest.xml)
```xml
<uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
<uses-permission android:name="android.permission.ACCESS_COARSE_LOCATION" />
<uses-permission android:name="android.permission.INTERNET" />
```

### iOS (ios/AttendanceApp/Info.plist)
```xml
<key>NSLocationWhenInUseUsageDescription</key>
<string>This app needs location access for attendance tracking</string>
```

## Troubleshooting

### Common Issues

1. **Metro bundler issues**:
   ```bash
   npm start --reset-cache
   ```

2. **Android build errors**:
   ```bash
   cd android && ./gradlew clean && cd ..
   ```

3. **iOS build errors**:
   ```bash
   cd ios && pod install && cd ..
   ```

4. **Location permissions not working**:
   - Ensure permissions are declared in manifest files
   - Check device location settings
   - Test on physical device (not emulator)

### Performance Optimization

1. **Enable Hermes** (Android):
   - Edit `android/app/build.gradle`:
   ```javascript
   project.ext.react = [
       enableHermes: true
   ]
   ```

2. **Optimize bundle size**:
   ```bash
   npx react-native bundle --platform android --dev false --entry-file index.js --bundle-output android/app/src/main/assets/index.android.bundle
   ```

## Security Considerations

1. **API Communication**: All API calls use HTTPS in production
2. **Token Storage**: Authentication tokens stored securely in AsyncStorage
3. **Location Privacy**: Location data only captured during check-in/out
4. **Data Validation**: All inputs validated on both client and server

## Testing

### Running Tests
```bash
npm test
```

### Testing on Devices

1. **Android**: Install APK on device or use Android emulator
2. **iOS**: Use Xcode simulator or deploy to physical device

## Deployment

### App Store Deployment

1. **Prepare app icons and splash screens**
2. **Update version numbers** in `package.json` and platform-specific files
3. **Build release versions**
4. **Upload to respective app stores**

### Internal Distribution

1. **Android**: Distribute APK file directly
2. **iOS**: Use TestFlight for beta testing

## Support

For technical support or questions about the mobile app:

1. Check the troubleshooting section
2. Review API documentation
3. Ensure backend server is running and accessible
4. Verify network connectivity and permissions

## License

This mobile application is part of the Attendance Management System and follows the same licensing terms as the main project.