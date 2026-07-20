# KidsTrack Database Schema Reference

## Tables

### users
| Column | Type | Description |
|---|---|---|
| id | PK | Auto-increment |
| name | string | Full name |
| age | string? | Age |
| email | string (unique) | Login email |
| password | string | Hashed |
| phone_number | string? | Phone |
| address | text? | Home address |
| photo | string? | Photo path |
| role | string | admin, parent1, parent2, guardian |
| verified | boolean | Identity verified |

### guardianships
Replaces: parents, second_parents, guardians (ALL MERGED)
| Column | Type | Description |
|---|---|---|
| id | PK | Auto-increment |
| user_id | FK→users | The user (parent/guardian) |
| child_id | FK→children | The child |
| relationship | enum | main_parent, second_parent, guardian |
| is_emergency_contact | boolean | Emergency contact flag |
| UNIQUE(user_id, child_id, relationship) | | One role per user per child |

### children
| Column | Type | Description |
|---|---|---|
| id | PK | Auto-increment |
| name | string | Child name |
| age | int | Age in years |
| ic_number | string | IC/Birth Cert |
| dob | date? | Date of birth |
| address | text | Home address |
| photo | string? | Photo path |
| classroom_id | FK→classrooms? | Assigned classroom |
| medical_notes | text? | Medical info |
| dietary | text? | Dietary needs |
| is_active | boolean | Active status |
| enrollment_date | datetime | Enrollment date |
| qr_code | string | Unique QR code |
| qr_code_url | string? | QR URL |

### attendance
| Column | Type | Description |
|---|---|---|
| id | PK | Auto-increment |
| child_id | FK→children | Child |
| user_id | FK→users? | User who did the action |
| date | date | Date (Y-m-d) |
| status | string | checkin, checkout, late, absent, etc. |
| checkin_time | datetime? | Check-in time |
| checkout_time | datetime? | Check-out time |
| drop_off_by | string? | Who dropped off |
| pickup_by | string? | Who picked up |
| is_verified | boolean | Verified by system |
| notes | text? | Additional notes |

### classrooms
| Column | Type | Description |
|---|---|---|
| id | PK | Auto-increment |
| name | string | Classroom name |
| code | string | Short code |
| age_group | string | Age group label |
| min_age | int | Minimum age |
| max_age | int | Maximum age |
| capacity | int | Max children |
| color | string | Display color |

### timer_settings
| Column | Type | Description |
|---|---|---|
| id | PK | Auto-increment |
| day_name | string | "Isnin (Monday)" etc |
| morning_start | time | Check-in window start |
| morning_end | time | Check-in window end |
| evening_start | time | Check-out window start |
| evening_end | time | Check-out window end |

## Key Relationships

```php
// Child → Main Parent User
$child->parent           // hasOneThrough via guardianships (main_parent)
$child->secondParent     // hasOneThrough via guardianships (second_parent)
$child->guardian         // hasOneThrough via guardianships (guardian)
$child->linkedUsers      // belongsToMany via guardianships

// User → Children
$user->children          // belongsToMany via guardianships
$user->guardianships     // hasMany Guardianship

// Attendance
$attendance->child       // belongsTo Child
$attendance->user        // belongsTo User (who did action)
```

## Migration Command
```bash
# Fresh install:
php artisan migrate
php artisan db:seed

# Reset:
php artisan migrate:fresh --seed
```
