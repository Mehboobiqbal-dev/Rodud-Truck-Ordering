# Rodud Truck Ordering Mobile App

A React Native mobile application built with Expo for ordering trucks. This app allows users to register, log in, create truck orders, view order history, and receive notifications. Admins can manage orders and communicate with users via SMS.

## Features

- **User Authentication**: Secure login and registration using Laravel Sanctum
- **Order Management**: Create new truck orders with detailed specifications
- **Dashboard**: View order history and status updates
- **Real-time Notifications**: Receive email and SMS notifications for order updates
- **Admin Communication**: Direct messaging between admins and users via SMS using Twilio
- **Cross-platform**: Runs on Android and iOS devices

## Tech Stack

- **Frontend**: React Native with Expo
- **Backend**: Laravel 11 API with Sanctum authentication
- **Database**: MySQL
- **Notifications**: Laravel Notifications (Database, Mail, SMS via Twilio)
- **Build Tool**: Expo Application Services (EAS)

## Prerequisites

- Node.js (v18 or higher)
- npm or yarn
- Expo CLI
- Android Studio (for Android development)
- Xcode (for iOS development, macOS only)

## Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd mobile
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Install Expo CLI globally:
   ```bash
   npm install -g @expo/cli
   ```

## Configuration

1. Create a `.env` file in the mobile directory (if not present) and configure the API endpoint:
   ```
   API_BASE_URL=https://your-production-api-url.com/api
   ```

2. Ensure the backend API is running and accessible.

## Running the App

### Development Mode

1. Start the Expo development server:
   ```bash
   npx expo start
   ```

2. Choose your target platform:
   - Press `a` for Android emulator
   - Press `i` for iOS simulator
   - Scan QR code with Expo Go app on your device

### Development Build

For a more accurate development experience:

1. Install EAS CLI:
   ```bash
   npm install -g eas-cli
   ```

2. Build a development client:
   ```bash
   eas build --platform android --profile development
   # or
   eas build --platform ios --profile development
   ```

3. Install the build on your device and run:
   ```bash
   npx expo start --dev-client
   ```

## Building for Production

### Android APK

1. Configure EAS build:
   ```bash
   eas build:configure
   ```

2. Build the production APK:
   ```bash
   eas build --platform android --profile production
   ```

3. Download the APK from the EAS dashboard or the provided link.

### iOS (macOS only)

1. Configure EAS build:
   ```bash
   eas build:configure
   ```

2. Build the production IPA:
   ```bash
   eas build --platform ios --profile production
   ```

## Backend Setup

This mobile app requires a Laravel backend API. Ensure the backend is deployed and configured with:

- Laravel Sanctum for authentication
- Twilio for SMS notifications
- Proper CORS settings for mobile app communication

Refer to the backend README for detailed setup instructions.

## Project Structure

```
mobile/
├── app/                    # App screens and navigation
├── assets/                 # Images and static assets
├── components/             # Reusable UI components
├── constants/              # App constants and configuration
├── context/                # React context providers
├── hooks/                  # Custom React hooks
├── scripts/                # Build and utility scripts
└── services/               # API service functions
```

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin feature/your-feature`
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please contact the development team or create an issue in the repository.
