# Authentication Sequence Diagrams

This document contains sequence diagrams for user authentication and account management flows in the Yousaha ERP system.

## 🔐 User Registration Flow

### Sign Up Process
**Description**: Complete user registration with email verification

```sequence
title User Registration Flow

User->Frontend: Fill registration form
Frontend->AuthController: POST /register
AuthController->Validator: Validate input data
Validator->AuthController: Validation result

alt Validation fails
    AuthController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    AuthController->User: Create new user record
    AuthController->EmailVerification: Create verification token
    AuthController->Mail: Send verification email
    Mail->User: Email with verification link
    AuthController->Frontend: Success message
    Frontend->User: Redirect to login page
end
```

**Key Features**:
- Email uniqueness validation
- Password confirmation requirement
- reCAPTCHA verification
- Automatic email verification setup
- Terms acceptance requirement

## 🔑 User Login Flow

### Sign In Process
**Description**: User authentication with email verification check

```sequence
title User Login Flow

User->Frontend: Enter credentials
Frontend->AuthController: POST /login
AuthController->Validator: Validate login data
Validator->AuthController: Validation result

alt Validation fails
    AuthController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    AuthController->Auth: Attempt authentication
    Auth->AuthController: Authentication result
    
    alt Authentication fails
        AuthController->Frontend: Return with error
        Frontend->User: Display login failed message
    else Authentication succeeds
        AuthController->User: Check email verification
        User->AuthController: Verification status
        
        alt Email not verified
            AuthController->Auth: Logout user
            AuthController->Frontend: Return verification required
            Frontend->User: Display verification message
        else Email verified
            AuthController->Frontend: Redirect to home
            Frontend->User: Access granted to system
        end
    end
end
```

**Key Features**:
- Email verification requirement before login
- Secure password hashing
- Session management
- Redirect to home page on success

## 📧 Email Verification Flow

### Email Verification Process
**Description**: Email address verification through token validation

```sequence
title Email Verification Flow

User->Frontend: Click verification link
Frontend->AuthController: GET /verify-email/{token}
AuthController->EmailVerification: Find verification record
EmailVerification->AuthController: Verification data

alt Token invalid/expired
    AuthController->Frontend: Error message
    Frontend->User: Display invalid token message
else Token valid
    AuthController->User: Update verification status
    User->AuthController: Save changes
    AuthController->EmailVerification: Delete verification record
    AuthController->Frontend: Success message
    Frontend->User: Display verification success
end
```

**Key Features**:
- Token-based verification
- Expiration handling (24 hours)
- Automatic cleanup of verification records
- Success confirmation

## 🔒 Password Reset Flow

### Forgot Password Process
**Description**: Password reset request and email delivery

```sequence
title Forgot Password Flow

User->Frontend: Request password reset
Frontend->AuthController: POST /forgot-password
AuthController->Validator: Validate email
Validator->AuthController: Validation result

alt Validation fails
    AuthController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    AuthController->PasswordResetToken: Create/update reset token
    PasswordResetToken->AuthController: Token created
    AuthController->Mail: Send reset email
    Mail->User: Email with reset link
    AuthController->Frontend: Success message
    Frontend->User: Display reset email sent
end
```

**Key Features**:
- Email existence validation
- Secure token generation
- 24-hour token expiration
- Email delivery confirmation

### Reset Password Process
**Description**: Password reset completion with token validation

```sequence
title Reset Password Flow

User->Frontend: Click reset link
Frontend->AuthController: GET /reset-password/{token}
AuthController->PasswordResetToken: Validate token
PasswordResetToken->AuthController: Token status

alt Token invalid/expired
    AuthController->Frontend: Error message
    Frontend->User: Display invalid token message
else Token valid
    Frontend->User: Show reset form
    User->Frontend: Enter new password
    Frontend->AuthController: POST /reset-password
    AuthController->Validator: Validate new password
    Validator->AuthController: Validation result
    
    alt Validation fails
        AuthController->Frontend: Return with errors
        Frontend->User: Display error messages
    else Validation passes
        AuthController->User: Update password
        User->AuthController: Save changes
        AuthController->PasswordResetToken: Delete reset token
        AuthController->Frontend: Success message
        Frontend->User: Display reset success
    end
end
```

**Key Features**:
- Token validation
- Password confirmation requirement
- Secure password hashing
- Automatic token cleanup

## 🔄 Resend Verification Flow

### Resend Verification Process
**Description**: Resend email verification for unverified accounts

```sequence
title Resend Verification Flow

User->Frontend: Request resend verification
Frontend->AuthController: POST /resend-verification
AuthController->Validator: Validate email
Validator->AuthController: Validation result

alt Validation fails
    AuthController->Frontend: Return with errors
    Frontend->User: Display error messages
else Validation passes
    AuthController->User: Check verification status
    User->AuthController: Verification status
    
    alt Already verified
        AuthController->Frontend: Return with error
        Frontend->User: Display already verified message
    else Not verified
        AuthController->EmailVerification: Create new verification
        EmailVerification->AuthController: Verification created
        AuthController->Mail: Send verification email
        Mail->User: New verification email
        AuthController->Frontend: Success message
        Frontend->User: Display resend success
    end
end
```

**Key Features**:
- Duplicate verification prevention
- New token generation
- Email delivery confirmation

## 👤 Profile Management Flow

### View Profile Process
**Description**: Display user profile information

```sequence
title View Profile Flow

User->Frontend: Access profile page
Frontend->AuthController: GET /profile
AuthController->Auth: Check authentication
Auth->AuthController: Authentication status

alt Not authenticated
    AuthController->Frontend: Redirect to login
    Frontend->User: Login required
else Authenticated
    AuthController->User: Get current user data
    User->AuthController: User information
    AuthController->Frontend: Return profile view
    Frontend->User: Display profile information
end
```

**Key Features**:
- Authentication requirement
- Current user data retrieval
- Profile information display

### Update Profile Process
**Description**: Update user profile information

```sequence
title Update Profile Flow

User->Frontend: Submit profile updates
Frontend->AuthController: PUT /profile
AuthController->Auth: Check authentication
Auth->AuthController: Authentication status

alt Not authenticated
    AuthController->Frontend: Redirect to login
    Frontend->User: Login required
else Authenticated
    AuthController->Validator: Validate profile data
    Validator->AuthController: Validation result
    
    alt Validation fails
        AuthController->Frontend: Return with errors
        Frontend->User: Display error messages
    else Validation passes
        AuthController->User: Update profile fields
        User->AuthController: Save changes
        AuthController->Frontend: Success message
        Frontend->User: Display update success
    end
end
```

**Key Features**:
- Authentication requirement
- Profile data validation
- Secure data update
- Success confirmation

### Change Password Process
**Description**: Update user password with current password verification

```sequence
title Change Password Flow

User->Frontend: Submit password change
Frontend->AuthController: PUT /password
AuthController->Auth: Check authentication
Auth->AuthController: Authentication status

alt Not authenticated
    AuthController->Frontend: Redirect to login
    Frontend->User: Login required
else Authenticated
    AuthController->Validator: Validate password data
    Validator->AuthController: Validation result
    
    alt Validation fails
        AuthController->Frontend: Return with errors
        Frontend->User: Display error messages
    else Validation passes
        AuthController->User: Verify current password
        User->AuthController: Password verification
        
        alt Current password incorrect
            AuthController->Frontend: Return with error
            Frontend->User: Display password error
        else Current password correct
            AuthController->User: Update password
            User->AuthController: Save changes
            AuthController->Frontend: Success message
            Frontend->User: Display password change success
        end
    end
end
```

**Key Features**:
- Authentication requirement
- Current password verification
- Secure password hashing
- Success confirmation

## 🚪 User Logout Flow

### Sign Out Process
**Description**: Secure user logout and session cleanup

```sequence
title User Logout Flow

User->Frontend: Click logout button
Frontend->AuthController: POST /logout
AuthController->Auth: Logout user
Auth->AuthController: Logout complete
AuthController->Frontend: Redirect to login
Frontend->User: Return to login page
```

**Key Features**:
- Session termination
- Authentication cleanup
- Secure redirect to login

## 🔐 Security Features

### Authentication Guards
- Session-based authentication
- CSRF protection
- Input validation and sanitization
- Secure password hashing (bcrypt)

### Email Verification
- Token-based verification
- 24-hour expiration
- Automatic cleanup
- Resend capability

### Password Security
- Minimum 8 characters
- Confirmation requirement
- Current password verification
- Secure reset process

### Access Control
- Authentication middleware
- Company-based data isolation
- Role-based permissions
- Session management

## 📧 Email Integration

### SMTP Configuration
- Configurable mail settings
- Template-based emails
- HTML email support
- Delivery confirmation

### Email Templates
- Verification email template
- Password reset template
- Professional formatting
- Brand consistency

## 🔄 Error Handling

### Validation Errors
- Field-specific error messages
- Input preservation on errors
- User-friendly error display
- Comprehensive validation rules

### System Errors
- Exception logging
- User-friendly error messages
- Graceful error handling
- Debug information (development)

## 📱 User Experience

### Form Handling
- Real-time validation
- Error highlighting
- Success confirmations
- Smooth transitions

### Navigation Flow
- Intuitive user paths
- Clear success/error states
- Consistent messaging
- Responsive design

---

**Note**: All authentication flows include proper validation, error handling, and security measures to ensure system integrity and user data protection.