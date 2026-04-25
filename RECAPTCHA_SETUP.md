# Google reCAPTCHA Setup Instructions

## Current Status
The application is currently using **Google's test reCAPTCHA keys** which always pass validation. These are for development/testing purposes only.

**Test Site Key (currently in use):** `6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI`

## ⚠️ IMPORTANT: Replace with Production Keys

Before deploying to production, you **MUST** replace the test keys with your own reCAPTCHA keys.

## How to Get Your Own reCAPTCHA Keys

### Step 1: Go to Google reCAPTCHA Admin
Visit: https://www.google.com/recaptcha/admin/create

### Step 2: Register Your Site
1. **Label**: Enter a name for your site (e.g., "Honehube")
2. **reCAPTCHA type**: Select **reCAPTCHA v2** → "I'm not a robot" Checkbox
3. **Domains**: Add your domain(s)
   - For local testing: `localhost`
   - For production: `yourdomain.com` (without http://)
4. **Accept the reCAPTCHA Terms of Service**
5. Click **Submit**

### Step 3: Get Your Keys
After registration, you'll receive:
- **Site Key** (public key - goes in HTML)
- **Secret Key** (private key - goes in backend/server)

### Step 4: Update Your Code

#### In `login.html` (line ~93):
```html
<!-- Replace this line -->
<div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>

<!-- With your actual site key -->
<div class="g-recaptcha" data-sitekey="YOUR_ACTUAL_SITE_KEY_HERE"></div>
```

#### In `register.html` (line ~120):
```html
<!-- Replace this line -->
<div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>

<!-- With your actual site key -->
<div class="g-recaptcha" data-sitekey="YOUR_ACTUAL_SITE_KEY_HERE"></div>
```

### Step 5: Backend Verification (IMPORTANT!)
Currently, the reCAPTCHA is only validated on the client-side. For production, you **MUST** verify the reCAPTCHA response on your backend server.

**Backend verification example (PHP):**
```php
<?php
$recaptcha_secret = "YOUR_SECRET_KEY_HERE";
$recaptcha_response = $_POST['g-recaptcha-response'];

$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
$response = json_decode($verify);

if ($response->success) {
    // reCAPTCHA passed - proceed with login/registration
} else {
    // reCAPTCHA failed - show error
    echo "Please complete the reCAPTCHA verification.";
}
?>
```

## Security Notes

✅ **What's Protected:**
- Bot registration/login attempts
- Automated form submissions
- Brute force attacks

⚠️ **Remember:**
- Test keys work everywhere but provide NO actual protection
- Production keys only work on registered domains
- Always verify reCAPTCHA on the server-side, not just client-side
- Keep your Secret Key private (never expose in HTML/JavaScript)

## Testing

### With Test Keys (Current):
- reCAPTCHA will always pass
- Good for development/testing

### With Production Keys:
- reCAPTCHA will require user interaction
- Provides actual bot protection
- Only works on registered domains

## Additional Resources
- [reCAPTCHA Documentation](https://developers.google.com/recaptcha/docs/display)
- [reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin)
- [Server-side Verification Guide](https://developers.google.com/recaptcha/docs/verify)
