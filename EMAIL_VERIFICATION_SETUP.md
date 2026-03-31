# Email Verification Setup Instructions

## Overview
Your BloodLink application now includes email OTP verification for user registration. Users must verify their email address before they can log in.

## How It Works

1. **Registration**: When a user registers, they receive a 6-digit OTP code via email
2. **Verification**: User enters the OTP on the verification page
3. **Login**: Only verified users can log in to the system
4. **Resend**: Users can request a new OTP if needed

## Email Configuration

### Option 1: Development (MailHog/Mailpit)
For local development, the application is configured to use MailHog/Mailpit by default:

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@bloodlink.com"
MAIL_FROM_NAME="BloodLink"
```

Install MailHog:
```bash
# Using Docker
docker run -p 1025:1025 -p 8025:8025 mailhog/mailhog

# Access web interface at: http://localhost:8025
```

### Option 2: Gmail SMTP
For production, you can use Gmail SMTP:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@bloodlink.com"
MAIL_FROM_NAME="BloodLink"
```

**Note**: For Gmail, you need to:
1. Enable 2-factor authentication
2. Create an App Password (not your regular password)
3. Use the App Password in the configuration

### Option 3: SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@bloodlink.com"
MAIL_FROM_NAME="BloodLink"
```

## Database Migration

Run the migration to add email verification fields:

```bash
php artisan migrate
```

## Features Included

### Backend (Laravel)
- **EmailVerificationService**: Handles OTP generation and verification
- **OTPMail**: Beautiful HTML email template
- **Updated AuthService**: Registration now sends OTP
- **New API Endpoints**:
  - `POST /api/verify-email` - Verify OTP
  - `POST /api/resend-otp` - Resend OTP

### Frontend (React)
- **EmailVerification Component**: OTP input and verification
- **Updated Auth Component**: Handles verification flow
- **Automatic Redirects**: Users are redirected based on verification status

## Security Features

- **OTP Expiration**: Codes expire in 15 minutes
- **Rate Limiting**: Users can request new OTP after waiting
- **Secure Storage**: OTPs stored in cache, not database
- **Input Validation**: All inputs are properly validated

## User Flow

1. User fills registration form
2. System sends OTP to user's email
3. User redirected to verification page
4. User enters 6-digit code
5. Upon success, user is automatically logged in and redirected to dashboard
6. If user tries to login without verification, they're prompted to verify first

## Testing

1. Register a new account
2. Check your email (or MailHog interface at localhost:8025)
3. Enter the OTP code
4. Verify successful login and redirect

## Troubleshooting

### Emails not sending?
- Check mail configuration in `.env`
- Verify mail server is running
- Check Laravel logs: `php artisan log:clear` then try again

### OTP not working?
- Ensure you're using the latest OTP (they expire)
- Check for leading/trailing spaces
- Verify email address is correct

### Migration issues?
- Run `php artisan migrate:rollback` if needed
- Check database connection settings

## Production Considerations

- Use a reliable email service (SendGrid, Mailgun, etc.)
- Set up proper domain authentication (SPF, DKIM)
- Monitor email deliverability
- Consider using queues for better performance
