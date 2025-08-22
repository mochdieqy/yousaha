# Authentication Sequence Diagrams

## Sign Up

![Sign Up Sequence Diagram](images/Sign%20Up.png)
```
title Sign Up

User->Application: Open sign up page
Application->User: Show sign up page
User->Application: Input email, password, name
Application->DB: Get user by email
DB->Application: Return user

alt Email exist
    Application->User: Send error message
else Email not exist
    Application->DB: Begin transaction
    Application->DB: Insert user
    DB->Application: Return status
    Application->DB: Insert email verification
    DB->Application: Return status
    
    alt Transaction failed
        Application->DB: Rollback transaction
        Application->User: Send error message
    else Transaction success
        Application->DB: Commit transaction
        Application->SMTP Server: Request to send verification email
        SMTP Server->Application: Return status
        
        alt Request failed
            Application->User: Send error message
        else Request success
            SMTP Server->User Email: Send verification email
            Application->User: Send successful sign up message
            User->User Email: Click verify email
            User Email->Application: Redirect for verify email
            Application->DB: Get email verification by token
            DB->Application: Return email verification
            
            alt Email verification not exist
                Application->User: Show error message
            else Email verification exist
                Application->DB: Update user status to verified by email
                DB->Application: Return status
                
                alt Update failed
                    Application->User: Show error message
                else Update success
                    Application->User: Show successful verification message
                end
            end
        end
    end
end
```
## Sign In

![Sign In Sequence Diagram](images/Sign%20In.png)
```
title Sign In

User->Application: Open sign up page
Application->User: Show sign up page
User->Application: Input email, password
Application->DB: Get user by email
DB->Application: Return user

alt Email not exist
    Application->User: Show error message
else Email exist
    Application->Application: Compare hash password
    
    alt Hash password different
        Application->User: Show error message
    else Hash password same
        Application->Application: Check user verification
        
        alt User not verified
            Application->User: Show error message
        else User verified
            Application->DB: Insert session
            DB->Application: Return status
            
            alt Insert failed
                Application->User: Show error message
            else Insert success
                Application->User: Show home page
            end
        end
    end
end
```
## Forgot Password

![Forgot Password Sequence Diagram](images/Forgot%20Password.png)
```
title Forgot Password

User->Application: Open sign in page
Application->User: Show sign in page
User->Application: Click forgot password
Application->User: Show forgot password page
User->Application: Input email
Application->DB: Get user by email
DB->Application: Return user

alt Email not exist
    Application->User: Show error message
else Email exist
    Application->DB: Insert into password reset token table
    DB->Application: Return status
    
    alt Insert failed
        Application->User: Show error message
    else Insert success
        Application->SMTP Server: Request to send reset password email
        SMTP Server->Application: Return status
        
        alt Request failed
            Application->User: Show error message
        else Request success
            SMTP Server->User Email: Send reset password email
            Application->User: Send successful forgot password message
            User->User Email: Click reset password
            User Email->Application: Redirect for reset password
            Application->DB: Get password reset token by token
            
            alt Token not exist
                Application->User: Show error message
            else Token exist
                Application->User: Show reset password page
                User->Application: Input new password
                Application->DB: Update user
                DB->Application: Return status
                
                alt Update failed
                    Application->User: Show error message
                else Update success
                    Application->User: Show successful reset password message
                end
            end
        end
    end
end
```