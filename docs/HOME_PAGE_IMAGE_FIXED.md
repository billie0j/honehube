# Home Page Background Image Fixed

**Date:** 2024  
**Status:** ✅ Complete

## Issue
The home page (`frontend/pages/home.html`) was not displaying the `landing.png` background image. The user reported: "the image is not there".

## Root Cause
The home page was using an inline CSS style with `background: url()` which can be unreliable:
```html
<section class="home-hero" style="background: linear-gradient(...), url('../assets/images/landing.png') center/cover no-repeat;">
```

This approach differs from the working login and register pages, which use a separate `<img>` tag structure.

## Solution
Restructured the home page hero section to match the proven pattern from login/register pages:

### New Structure
1. **Wrapper div** (`home-hero-wrapper`) - Contains all layers
2. **Background image layer** (`home-hero-background`) - Separate `<img>` tag with `landing.png`
3. **Gradient overlay** (`home-hero-overlay`) - Purple gradient (85% opacity)
4. **Hero content** (`home-hero`) - Text and buttons on top

### CSS Layers (z-index)
- Background image: `z-index: 1`
- Gradient overlay: `z-index: 2`
- Hero content: `z-index: 3`

### Code Changes
```html
<!-- Hero with Background Image -->
<div class="home-hero-wrapper">
  <!-- Background Image -->
  <div class="home-hero-background">
    <img src="../assets/images/landing.png" alt="Evelyn Hone College Campus" />
  </div>
  
  <!-- Gradient overlay -->
  <div class="home-hero-overlay"></div>
  
  <!-- Hero Content -->
  <section class="home-hero">
    <!-- Content here -->
  </section>
</div>
```

## Verification
✅ Image file exists: `frontend/assets/images/landing.png` (122KB)  
✅ Structure matches working login/register pages  
✅ Proper z-index layering ensures visibility  
✅ Gradient overlay reduced to 85% opacity (from 95%) for better image visibility

## Design Consistency
All three pages now use the same background image pattern:
- **Login page**: `building.png` with 50% dark overlay
- **Register page**: `register.png` with 50% dark overlay
- **Home page**: `landing.png` with 85% purple gradient overlay

## Files Modified
- `frontend/pages/home.html` - Restructured hero section with proper image layers

## Testing
To verify the fix:
1. Open `http://localhost:8080/honehube/frontend/pages/home.html`
2. The landing page should now display the campus background image
3. Purple gradient overlay should be visible over the image
4. Hero content (text and buttons) should be clearly readable on top

## Notes
- The `<img>` tag approach is more reliable than CSS `background: url()` for ensuring images load
- Separate layers allow better control over opacity and z-index stacking
- This pattern is now consistent across all authentication and landing pages
