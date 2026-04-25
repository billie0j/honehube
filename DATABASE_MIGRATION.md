# Database Schema Migration Guide

## Table Name Changes

| Old Name | New Name |
|----------|----------|
| `listings` | `accessories` |
| (kept) | `users` |
| (kept) | `purchase_requests` |

## Column Name Changes

### Users Table
| Old Column | New Column |
|------------|------------|
| `id` | `user_id` |
| `name` | `full_name` |
| `role` ('user'/'admin') | `role` ('student'/'admin') |
| `student_id` | (removed) |
| `updated_at` | (removed) |
| `last_login` | (removed) |
| `is_active` | (removed) |

### Accessories Table (formerly Listings)
| Old Column | New Column |
|------------|------------|
| `id` | `item_id` |
| `user_id` | `posted_by` |
| `title` | `item_name` |
| `price` | `original_price` |
| `status` ('active'/'sold'/'inactive') | `status` ('available'/'sold') |
| `condition_type` | (removed) |
| `updated_at` | (removed) |

### Purchase Requests Table
| Old Column | New Column |
|------------|------------|
| `id` | `request_id` |
| `listing_id` | `item_id` |
| `buyer_id` | `student_id` |
| `status` | `status` (same values) |
| `denial_reason` | (removed) |
| `updated_at` | (removed) |

## API Updates Required

All API files need to be updated to use new table and column names:
- `api/auth.php` - Update user table references
- `api/listings.php` - Rename to `api/accessories.php` and update all references
- `api/requests.php` - Update column names
- `api/users.php` - Update column names

## Frontend Updates Required

JavaScript files need to be updated:
- `js/api.js` - Update API endpoint names and response field names
- `js/store.js` - Update localStorage field names
- All HTML files - Update field references

## Migration Steps

1. ✅ Update `database/schema.sql` with new structure
2. ⏳ Update API files with new table/column names
3. ⏳ Update JavaScript files with new field names
4. ⏳ Update HTML files with new references
5. ⏳ Test all functionality
6. ⏳ Run `install.php` to recreate database

## Backward Compatibility

The system will need to be reinstalled with the new schema. Existing data will be lost unless migrated manually.
