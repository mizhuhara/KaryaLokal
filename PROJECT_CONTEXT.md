# HANDMADE INDONESIA — AI PROJECT CONTEXT & TASK TRACKER

> **Purpose:** This file is the single source of truth for AI coding assistants working on this project.
> Read this file BEFORE making changes.
> Update this file AFTER every meaningful implementation, fix, decision, or scope change.

---

# 1. PROJECT IDENTITY

**Project Name:** KaryaLokal  
**Project Type:** Location-based handmade marketplace / katalog kerajinan  
**Target Market:** Seluruh Indonesia  
**Main Concept:** Platform yang mempertemukan pembeli dengan pengrajin/penjual handmade lokal.

### Core Value

> **Temukan. Pesan. Dukung Pengrajin Lokal.**

Platform bukan hanya untuk bucket bunga. Bucket bunga adalah salah satu kategori awal. Platform harus dapat berkembang menjadi marketplace kerajinan handmade Indonesia.

Contoh kategori:
- Bucket bunga
- Bouquet wisuda
- Bucket uang
- Bucket snack
- Bouquet boneka
- Hampers
- Gift box
- Crochet / rajutan
- Macrame
- Resin
- Clay
- Kerajinan kayu
- Dekorasi
- Souvenir
- Wedding handmade
- Custom handmade
- Kategori handmade lainnya

---

# 2. MASALAH YANG INGIN DISELESAIKAN

Banyak pengrajin/UMKM handmade menjual produk melalui Instagram, WhatsApp, atau marketplace umum.

Masalah:
1. Sulit menemukan pengrajin handmade di sekitar lokasi pembeli.
2. Produk handmade lokal tersebar di banyak platform.
3. Pembeli sulit membandingkan produk dan seller berdasarkan jarak.
4. Seller kecil membutuhkan tempat untuk membuat katalog/toko digital.
5. Produk handmade sering membutuhkan komunikasi dan custom order.

### SOLUSI

Membangun satu platform yang:
- Mengumpulkan katalog produk handmade.
- Menyediakan toko digital untuk seller.
- Menampilkan seller berdasarkan lokasi.
- Menghitung jarak buyer ke seller.
- Menyediakan pencarian dan filter.
- Mendukung custom order.
- Menyediakan rating/review.
- Mempertemukan buyer dan seller.

---

# 3. PERAN PENGGUNA

## BUYER

Buyer dapat:
- Register / login.
- Melihat homepage.
- Melihat kategori.
- Mencari produk.
- Filter produk.
- Melihat seller.
- Melihat seller terdekat.
- Menggunakan peta.
- Melihat detail produk.
- Wishlist.
- Chat seller.
- Mengajukan custom order.
- Membuat order.
- Melihat status order.
- Memberikan rating/review.
- Melaporkan produk/seller.

## SELLER

Seller dapat:
- Register sebagai seller.
- Membuat profil toko.
- Mengatur alamat toko.
- Menentukan lokasi toko.
- Mengelola produk.
- Upload foto produk.
- Mengatur harga.
- Mengatur stok.
- Menentukan custom order.
- Menentukan pickup/delivery.
- Mengelola order.
- Membalas chat.
- Melihat review.
- Melihat dashboard penjualan.

## ADMIN

Admin dapat:
- Login admin.
- Dashboard.
- User management.
- Seller management.
- Seller verification.
- Category management.
- Product moderation.
- Order monitoring.
- Report management.
- Review moderation.
- Statistik platform.

---

# 4. FITUR UTAMA / CORE FEATURES

Fitur berikut adalah identitas utama project:

## A. Handmade Marketplace
Buyer dapat melihat katalog produk handmade dari berbagai seller.

## B. Nearby Seller
Sistem mencari seller terdekat berdasarkan lokasi buyer.

Contoh:
- Radius 1 km
- Radius 5 km
- Radius 10 km
- Radius 25 km
- Radius 50 km
- Seluruh area

## C. Interactive Map
Menampilkan seller pada peta.

## D. Location Distance
Sistem menggunakan latitude dan longitude untuk menghitung jarak buyer ke seller.

## E. Custom Order
Buyer dapat meminta produk custom dan mengirim referensi gambar.

## F. Seller Store
Setiap seller memiliki halaman toko sendiri.

## G. Trust System
- Verified seller
- Rating
- Review
- Report

---

# 5. FITUR BUYER

## Authentication
- [ ] Register
- [ ] Login
- [ ] Logout
- [ ] Forgot password
- [ ] Profile

## Homepage
- [ ] Hero section
- [ ] Search
- [ ] Categories
- [ ] Nearby products
- [ ] Trending products
- [ ] Recommended products
- [ ] Featured sellers

## Product
- [ ] Product listing
- [ ] Product detail
- [ ] Product image gallery
- [ ] Price
- [ ] Stock
- [ ] Seller information
- [ ] Distance to seller
- [ ] Rating
- [ ] Review
- [ ] Custom availability
- [ ] Pickup availability
- [ ] Delivery availability

## Search & Filter
- [ ] Search product
- [ ] Category filter
- [ ] Price filter
- [ ] Rating filter
- [ ] Distance filter
- [ ] Custom filter
- [ ] Pickup filter
- [ ] Delivery filter
- [ ] Ready stock filter
- [ ] Pre-order filter
- [ ] Sort nearest
- [ ] Sort cheapest
- [ ] Sort highest rating
- [ ] Sort newest
- [ ] Sort popular

## Location
- [ ] Request browser location permission
- [ ] Save buyer location
- [ ] Detect latitude/longitude
- [ ] Calculate distance
- [ ] Nearby seller
- [ ] Radius search
- [ ] Map view
- [ ] Seller marker
- [ ] Seller detail from map

## Wishlist
- [ ] Add product to wishlist
- [ ] Remove product
- [ ] Wishlist page
- [ ] Favorite seller

## Order
- [ ] Add to order/cart strategy
- [ ] Create order
- [ ] Order detail
- [ ] Order status
- [ ] Order history
- [ ] Cancel order where allowed
- [ ] Complete order

## Review
- [ ] Give rating
- [ ] Write review
- [ ] Upload review photo
- [ ] View reviews

## Communication
- [ ] Buyer-seller chat
- [ ] Notifications

---

# 6. FITUR SELLER

## Seller Registration
- [ ] Seller registration
- [ ] Seller profile
- [ ] Shop name
- [ ] Shop description
- [ ] Shop photo/logo
- [ ] Contact information
- [ ] Address
- [ ] Province
- [ ] City
- [ ] District
- [ ] Latitude
- [ ] Longitude
- [ ] Operating hours
- [ ] Pickup availability
- [ ] Delivery availability
- [ ] Custom order availability

## Verification
- [ ] Submit verification
- [ ] Verification status
- [ ] Admin review
- [ ] Approved
- [ ] Rejected
- [ ] Verified badge

## Product Management
- [ ] Create product
- [ ] Edit product
- [ ] Delete product
- [ ] Product image
- [ ] Product description
- [ ] Price
- [ ] Stock
- [ ] Category
- [ ] Custom flag
- [ ] Ready stock / preorder
- [ ] Delivery / pickup

## Order Management
- [ ] Incoming orders
- [ ] Accept order
- [ ] Reject order
- [ ] Processing
- [ ] Ready
- [ ] Shipped / pickup
- [ ] Completed
- [ ] Order history

## Seller Dashboard
- [ ] Total products
- [ ] Total orders
- [ ] Revenue
- [ ] Rating
- [ ] Visitors
- [ ] Sales chart

---

# 7. FITUR ADMIN

## Dashboard
- [ ] Total users
- [ ] Total sellers
- [ ] Total products
- [ ] Total orders
- [ ] Transaction summary
- [ ] Seller verification queue
- [ ] Reports
- [ ] Recent activity

## Management
- [ ] Users
- [ ] Sellers
- [ ] Products
- [ ] Categories
- [ ] Orders
- [ ] Reviews
- [ ] Reports

## Moderation
- [ ] Verify seller
- [ ] Reject seller
- [ ] Disable seller
- [ ] Approve product
- [ ] Remove product
- [ ] Handle reports
- [ ] Moderate reviews

---

# 8. DATABASE / DATA MODEL

Target entities:

- [ ] users
- [ ] roles
- [ ] seller_profiles
- [ ] categories
- [ ] products
- [ ] product_images
- [ ] product_variants
- [ ] addresses
- [ ] locations
- [ ] orders
- [ ] order_items
- [ ] payments
- [ ] reviews
- [ ] wishlists
- [ ] wishlist_items
- [ ] favorites
- [ ] messages
- [ ] notifications
- [ ] reports
- [ ] seller_verifications
- [ ] custom_orders

### Seller location minimum data

```text
seller_profiles
- id
- user_id
- shop_name
- description
- address
- province
- city
- district
- latitude
- longitude
- is_verified
```

### Product minimum data

```text
products
- id
- seller_id
- category_id
- name
- description
- price
- stock
- status
- is_custom
- created_at
- updated_at
```

---

# 9. LOCATION SYSTEM

Location is a CORE feature.

Do not fake or hardcode distance.

The system must use:
- latitude
- longitude
- distance calculation

### Basic flow

```text
Buyer opens nearby feature
        ↓
Request location permission
        ↓
Get buyer latitude/longitude
        ↓
Query seller locations
        ↓
Calculate distance
        ↓
Filter by radius
        ↓
Sort by nearest
        ↓
Show list + map
```

### Important rules

1. Never hardcode seller distance.
2. Never claim a seller is nearby without calculating it.
3. If location permission is denied, provide manual location/search fallback.
4. Do not permanently store exact buyer location unless required and clearly justified.
5. Seller location must be validated.
6. Distance should be calculated from coordinates, not text addresses.

---

# 10. MAP SYSTEM

Preferred behavior:

```text
[Seller List]              [Map]

Seller A 1.2 km             📍
Seller B 2.8 km       📍
Seller C 4.1 km                  📍
```

Map requirements:
- [ ] Show buyer location when permission is granted
- [ ] Show seller markers
- [ ] Click marker → seller preview
- [ ] Open seller detail
- [ ] Search by radius
- [ ] Handle no-result state
- [ ] Handle location permission denied

Do not lock the architecture to one map provider unless explicitly decided.

---

# 11. ORDER STATUS

Use a clear state machine:

```text
PENDING
   ↓
CONFIRMED
   ↓
PROCESSING
   ↓
READY
   ↓
SHIPPED / PICKUP
   ↓
COMPLETED
```

Possible cancellation/rejection states:

```text
CANCELLED
REJECTED
```

Do not randomly invent additional statuses without updating this document.

---

# 12. SELLER DISCOVERY

Default nearby logic:

```text
0–5 km
    ↓ if no/insufficient results
5–10 km
    ↓
10–25 km
    ↓
25–50 km
    ↓
larger area / city / province
```

The UI should explain when the search radius is expanded.

---

# 13. CUSTOM ORDER

Custom order is important because handmade products are often personalized.

Basic flow:

```text
Buyer
 ↓
Custom Request
 ↓
Description
 ↓
Reference Image
 ↓
Seller Review
 ↓
Seller Gives Price / Estimate
 ↓
Buyer Approves
 ↓
Order Created
```

Data to consider:
- description
- reference images
- requested date
- budget
- quantity
- seller response
- quoted price
- status

---

# 14. SECURITY & VALIDATION

Every implementation must consider:
- Authentication
- Authorization
- Role-based access
- Server-side validation
- File upload validation
- Image type/size validation
- Ownership checks
- CSRF protection
- Rate limiting where relevant
- SQL injection protection
- XSS protection
- Secure password handling

A seller must never be able to edit another seller's products/orders.

A buyer must never be able to access another buyer's private order data.

Admin-only actions must be protected.

---

# 15. UI / UX PRINCIPLES

Do not make the UI look like a generic admin template.

The product should feel:
- Handmade
- Warm
- Modern
- Trustworthy
- Premium but accessible
- Mobile friendly

Important pages:
- Home
- Search
- Category
- Nearby
- Map
- Product detail
- Seller store
- Cart/order
- Checkout/order detail
- Wishlist
- Chat
- Buyer profile
- Seller dashboard
- Admin dashboard

### Design rule

Do not redesign unrelated existing screens when implementing a new feature.

Reuse established:
- typography
- spacing
- components
- buttons
- cards
- navbar
- footer
- colors
- responsive behavior

If a design change is necessary, document it in CHANGELOG.

---

# 16. PROJECT DEVELOPMENT PHASES

## PHASE 0 — Planning

- [ ] Finalize project name
- [ ] Finalize branding
- [ ] Finalize requirements
- [ ] Finalize roles
- [ ] Finalize database ERD
- [ ] Finalize user flow
- [ ] Decide tech stack
- [ ] Decide map/geocoding provider

## PHASE 1 — Foundation

- [x] Project setup
- [x] Environment configuration
- [x] Database connection
- [x] Authentication
- [x] Role system
- [x] Base layout
- [x] Base UI components

## PHASE 2 — Seller

- [ ] Seller registration
- [ ] Seller profile
- [ ] Seller location
- [ ] Seller verification
- [ ] Product CRUD
- [ ] Product image upload
- [ ] Seller dashboard

## PHASE 3 — Buyer Catalog

- [ ] Homepage
- [ ] Categories
- [ ] Search
- [ ] Product listing
- [ ] Product detail
- [ ] Seller store
- [ ] Wishlist

## PHASE 4 — Location

- [ ] Browser geolocation
- [ ] Seller latitude/longitude
- [ ] Distance calculation
- [ ] Nearby seller
- [ ] Radius filter
- [ ] Map
- [ ] Location fallback

## PHASE 5 — Order

- [ ] Order creation
- [ ] Order items
- [ ] Order status
- [ ] Seller order management
- [ ] Buyer order history
- [ ] Cancellation rules

## PHASE 6 — Trust & Communication

- [ ] Reviews
- [ ] Rating
- [ ] Reports
- [ ] Chat
- [ ] Notifications
- [ ] Custom order

## PHASE 7 — Admin

- [ ] Admin dashboard
- [ ] User management
- [ ] Seller management
- [ ] Product moderation
- [ ] Category management
- [ ] Report management
- [ ] Statistics

## PHASE 8 — Advanced Marketplace

- [ ] Payment gateway
- [ ] Commission
- [ ] Voucher
- [ ] Promotion
- [ ] Seller subscription
- [ ] Delivery integration
- [ ] Recommendation system
- [ ] Advanced analytics

---

# 17. CURRENT STATUS

> IMPORTANT: This section must always represent the REAL current state.
> Never mark a task complete only because code was generated.
> Mark it complete only after implementation has been checked/tested.

### Current Phase

**PHASE 1 — Foundation (Completed)**
**PHASE 2 — Seller (Next)**

### Overall Status

- [x] Project concept defined
- [x] Target market defined
- [x] Buyer role defined
- [x] Seller role defined
- [x] Admin role defined
- [x] Nearby seller concept defined
- [x] Custom order concept defined
- [x] Core feature list defined
- [x] Final project name
- [x] Final branding
- [x] Final UI design system
- [x] Final ERD
- [x] Final tech stack
- [x] Map/geocoding provider
- [x] Phase 1: Foundation (Auth, Roles, Base Layout)

---

# 18. TASK STATUS RULES

Use these markers consistently:

- `[ ]` = NOT STARTED
- `[~]` = IN PROGRESS
- `[x]` = COMPLETED & VERIFIED
- `[!]` = BLOCKED / NEEDS DECISION
- `[-]` = CANCELLED / REMOVED FROM SCOPE

Never use `[x]` merely because:
- code exists
- AI generated code
- file was created
- migration was written

Use `[x]` only after the feature has been tested or otherwise verified.

---

# 19. AI WORK PROTOCOL

Every AI coding session MUST follow this order:

## STEP 1 — READ CONTEXT

Read:
- this file
- project files
- existing architecture
- existing routes
- existing models
- existing migrations
- existing components

Do not start coding before understanding the current state.

## STEP 2 — CHECK CURRENT STATUS

Look at:
- CURRENT STATUS
- CHANGELOG
- KNOWN ISSUES
- NEXT TASK

Determine what is actually completed.

## STEP 3 — DO NOT REBUILD EXISTING FEATURES

If a feature already exists:
- inspect it
- reuse it
- improve it if requested
- do not create a duplicate implementation

## STEP 4 — PLAN BEFORE CODING

Before making a significant change, state:
1. What will be changed.
2. Why it is needed.
3. Which files are affected.
4. Whether database changes are required.
5. Whether existing features may be affected.

## STEP 5 — IMPLEMENT

Make the smallest safe change necessary.

Avoid unrelated refactoring.

## STEP 6 — TEST

Test the affected functionality.

Examples:
- route works
- migration works
- CRUD works
- validation works
- authorization works
- responsive UI works
- location calculation works
- API response works

## STEP 7 — UPDATE THIS FILE

After implementation:
- update task checkbox
- update CURRENT STATUS
- add CHANGELOG entry
- update KNOWN ISSUES if necessary
- update NEXT TASK
- record important technical decisions

## STEP 8 — GIT

After a verified meaningful change:

```bash
git status
git diff
git add .
git commit -m "type: short description"
git push
```

Do not push broken or unverified changes.

---

# 20. CHANGELOG

Every meaningful change must be recorded here.

Format:

```text
## YYYY-MM-DD — Short title

Status: COMPLETED / IN PROGRESS / BLOCKED

### Changed
- ...

### Why
- ...

### Files
- ...

### Testing
- ...

### Notes
- ...
```

### Project Changelog

## 2026-08-25 — Phase 1 Foundation

Status: COMPLETED

### Changed
- Installed Laravel Breeze (Livewire/Volt) for authentication.
- Created `UserRole` and `OrderStatus` enums.
- Updated `User` model and migrations to support phone and roles.
- Implemented `EnsureUserHasRole` middleware and aliased it as `role`.
- Added role-based redirection to `/seller/dashboard` and `/admin/dashboard` upon login/register.
- Added `RoleAccessTest` and ensured all tests pass.
- Added `CategorySeeder` for handmade product categories.
- Scaffolded `SellerProfile` and `Category` models and migrations.

### Why
- Core platform foundation is required before building marketplace features.

### Files
- `app/Models/User.php`, `Category.php`, `SellerProfile.php`
- `app/Enums/UserRole.php`, `OrderStatus.php`
- `app/Http/Middleware/EnsureUserHasRole.php`
- `database/migrations/*`
- `database/factories/UserFactory.php`
- `database/seeders/CategorySeeder.php`
- `resources/views/livewire/pages/auth/*`
- `routes/web.php`, `bootstrap/app.php`
- `tests/Feature/RoleAccessTest.php`

### Testing
- `php artisan test` - All 31 tests passed.

---

## 2026-08-25 — Initial Project Definition

Status: COMPLETED

### Changed
- Defined Handmade Indonesia marketplace concept.
- Defined buyer, seller, and admin roles.
- Defined location-based seller discovery.
- Defined nearby seller and map concepts.
- Defined custom order.
- Defined rating/review/trust system.
- Defined phased roadmap.

### Why
- Establish a single source of truth before implementation.

### Testing
- Planning only; no application code has been implemented yet.

---

## 2026-08-25 — Finalize Project Name

Status: COMPLETED

### Changed
- Project name finalized to `KaryaLokal`.

### Why
- Establish clear project identity.

### Files
- PROJECT_CONTEXT.md

### Testing
- Manual verification of PROJECT_CONTEXT.md.

### Notes
- This is the first step in Phase 0 planning.

---

## 2026-08-25 — Finalize Phase 0 Planning

Status: COMPLETED

### Changed
- Decided tech stack: Laravel (PHP) + Livewire (frontend).
- Decided map/geocoding provider: Leaflet + OpenStreetMap + Nominatim.
- Created sitemap, user flow, ERD, UI design system.

### Why
- Complete Phase 0 planning before starting Phase 1 implementation.

### Files
- docs/sitemap.md
- docs/user_flow.md
- docs/erd.md
- docs/ui_design_system.md
- PROJECT_CONTEXT.md

### Testing
- Documentation review only.

### Notes
- Phase 0 complete. Ready to begin Phase 1: Foundation (project setup, env, DB connection, auth, role system, base layout, base UI components).

---

# 21. KNOWN ISSUES

Record real issues here.

Format:

```text
### ISSUE-001 — Short title
Status: OPEN
Severity: LOW / MEDIUM / HIGH / CRITICAL
Description:
...

Expected:
...

Current behavior:
...

Possible cause:
...

Fix:
...

Resolved:
...
```

Current issues:

- None recorded yet.

---

# 22. TECHNICAL DECISIONS

Record decisions that affect architecture.

Format:

```text
### DECISION-001 — Title
Date:
Decision:
Reason:
Alternatives considered:
Impact:
```

Current decisions:

### DECISION-001 — Location Is a Core Feature
Date: 2026-08-25

Decision:
The platform will support location-based seller discovery.

Reason:
The key differentiator is helping buyers find nearby handmade sellers.

Impact:
Seller location, buyer location handling, distance calculation, and map functionality must be considered in architecture and database design.

### DECISION-002 — Handmade Is Broader Than Bucket Flowers
Date: 2026-08-25

Decision:
Bucket flowers are an initial category, not the entire platform.

Reason:
The platform should be able to grow into a nationwide handmade marketplace.

Impact:
Database and category architecture must be generic enough for many handmade product types.

---

# 23. NEXT TASK

The AI must update this section after completing each meaningful task.

Current next tasks:

1. [x] Decide final project name.
2. [x] Decide technology stack.
3. [x] Create sitemap.
4. [x] Create user flow.
5. [x] Create ERD.
6. [x] Decide map/geocoding provider.
7. [x] Define UI design system.
8. [x] Complete Phase 1 implementation.
9. [~] Begin Phase 2 implementation (Seller Registration & Profile).

Do not jump to advanced marketplace features before the foundation is stable.

---

# 24. IMPORTANT "DO NOT" RULES

AI MUST NOT:

1. Forget the location-based marketplace concept.
2. Turn the project into a simple bucket flower catalog.
3. Remove seller functionality.
4. Remove admin functionality.
5. Hardcode seller distances.
6. Invent completed features.
7. Mark untested code as completed.
8. Rewrite working features unnecessarily.
9. Change the architecture without documenting it.
10. Add major features without updating the roadmap.
11. Delete existing functionality without explicit instruction.
12. Change database structure without documenting migration impact.
13. Introduce a new package/library without explaining why.
14. Change UI design system without documenting the decision.
15. Assume a feature exists just because a route/file/component exists.
16. Push known broken code to GitHub.

---

# 25. CONTEXT PRESERVATION RULE

When the user says:

> "lanjutkan project"

AI must:
1. Read this file.
2. Check CURRENT STATUS.
3. Check CHANGELOG.
4. Check KNOWN ISSUES.
5. Check NEXT TASK.
6. Inspect the actual project state.
7. Continue from the next unfinished task.

When the user says:

> "perbaiki fitur X"

AI must:
1. Find the existing implementation.
2. Understand why it was created.
3. Preserve existing behavior unless the user requests otherwise.
4. Make the fix.
5. Test it.
6. Record the fix in CHANGELOG.
7. Record any new issue or decision.

---

# 26. CHANGE REQUEST PROTOCOL

When user requests a new feature:

```text
REQUEST
 ↓
Check existing scope
 ↓
Check conflicts
 ↓
Determine affected modules
 ↓
Implement
 ↓
Test
 ↓
Update roadmap
 ↓
Update CHANGELOG
 ↓
Update NEXT TASK
```

If the request conflicts with an existing technical decision, do not silently overwrite the decision.

Document the change.

---

# 27. DEFINITION OF DONE

A feature is DONE only when:

- [ ] Implementation exists
- [ ] No obvious runtime error
- [ ] Main flow tested
- [ ] Validation tested where applicable
- [ ] Authorization tested where applicable
- [ ] UI checked where applicable
- [ ] Database migration checked where applicable
- [ ] Existing features still work
- [ ] Documentation updated
- [ ] CHANGELOG updated
- [ ] CURRENT STATUS updated
- [ ] NEXT TASK updated

Only then change `[~]` to `[x]`.

---

# 28. GITHUB WORKFLOW

This repository should keep this file at:

```text
/docs/PROJECT_CONTEXT.md
```

Recommended repository structure:

```text
/
├── app/
├── database/
├── resources/
├── routes/
├── public/
├── tests/
├── docs/
│   └── PROJECT_CONTEXT.md
├── README.md
└── ...
```

After every verified milestone:

```bash
git status
git diff
git add .
git commit -m "type: description"
git push
```

Recommended commit types:

```text
feat: new feature
fix: bug fix
refactor: code restructuring
docs: documentation
style: UI/style change
test: tests
chore: maintenance
```

Examples:

```text
feat: add nearby seller search
feat: add seller product management
fix: correct seller distance calculation
docs: update project context
refactor: improve product query
```

---

# 29. AI RESPONSE FORMAT

After completing a task, the AI should respond using:

```text
## Task Completed

### What changed
- ...

### Files changed
- ...

### Testing
- ...

### Status
- Completed / In Progress / Blocked

### Documentation updated
- PROJECT_CONTEXT.md
- CHANGELOG

### Next task
- ...

### Git
- Commit: ...
- Push: success / not performed / failed
```

If something is not tested, explicitly say so.

Never claim GitHub push succeeded unless it was actually confirmed.

---

# 30. CURRENT PROJECT SNAPSHOT

```text
Project:
KaryaLokal

Concept:
Nationwide handmade marketplace with location-based seller discovery.

Primary users:
Buyer / Seller / Admin

Core differentiator:
Nearby handmade seller discovery using location + distance + map.

Initial product focus:
Bucket flowers and handmade gifts.

Long-term scope:
Nationwide handmade marketplace.

Current phase:
PHASE 0 — Planning

Implementation status:
NOT STARTED

Next priority:
Finalize architecture and begin foundation.
```

---

# 24. IMPORTANT "DO NOT" RULES

AI MUST NOT:

1. Forget the location-based marketplace concept.
2. Turn the project into a simple bucket flower catalog.
3. Remove seller functionality.
4. Remove admin functionality.
5. Hardcode seller distances.
6. Invent completed features.
7. Mark untested code as completed.
8. Rewrite working features unnecessarily.
9. Change the architecture without documenting it.
10. Add major features without updating the roadmap.
11. Delete existing functionality without explicit instruction.
12. Change database structure without documenting migration impact.
13. Introduce a new package/library without explaining why.
14. Change UI design system without documenting the decision.
15. Assume a feature exists just because a route/file/component exists.
16. Push known broken code to GitHub.

---

# 25. CONTEXT PRESERVATION RULE

When the user says:

> "lanjutkan project"

AI must:
1. Read this file.
2. Check CURRENT STATUS.
3. Check CHANGELOG.
4. Check KNOWN ISSUES.
5. Check NEXT TASK.
6. Inspect the actual project state.
7. Continue from the next unfinished task.

When the user says:

> "perbaiki fitur X"

AI must:
1. Find the existing implementation.
2. Understand why it was created.
3. Preserve existing behavior unless the user requests otherwise.
4. Make the fix.
5. Test it.
6. Record the fix in CHANGELOG.
7. Record any new issue or decision.

---

# 26. CHANGE REQUEST PROTOCOL

When user requests a new feature:

```text
REQUEST
 ↓
Check existing scope
 ↓
Check conflicts
 ↓
Determine affected modules
 ↓
Implement
 ↓
Test
 ↓
Update roadmap
 ↓
Update CHANGELOG
 ↓
Update NEXT TASK
```

If the request conflicts with an existing technical decision, do not silently overwrite the decision.

Document the change.

---

# 27. DEFINITION OF DONE

A feature is DONE only when:

- [ ] Implementation exists
- [ ] No obvious runtime error
- [ ] Main flow tested
- [ ] Validation tested where applicable
- [ ] Authorization tested where applicable
- [ ] UI checked where applicable
- [ ] Database migration checked where applicable
- [ ] Existing features still work
- [ ] Documentation updated
- [ ] CHANGELOG updated
- [ ] CURRENT STATUS updated
- [ ] NEXT TASK updated

Only then change `[~]` to `[x]`.

---

# 28. GITHUB WORKFLOW

This repository should keep this file at:

```text
/docs/PROJECT_CONTEXT.md
```

Recommended repository structure:

```text
/
├── app/
├── database/
├── resources/
├── routes/
├── public/
├── tests/
├── docs/
│   └── PROJECT_CONTEXT.md
├── README.md
└── ...
```

After every verified milestone:

```bash
git status
git diff
git add .
git commit -m "type: description"
git push
```

Recommended commit types:

```text
feat: new feature
fix: bug fix
refactor: code restructuring
docs: documentation
style: UI/style change
test: tests
chore: maintenance
```

Examples:

```text
feat: add nearby seller search
feat: add seller product management
fix: correct seller distance calculation
docs: update project context
refactor: improve product query
```

---

# 29. AI RESPONSE FORMAT

After completing a task, the AI should respond using:

```text
## Task Completed

### What changed
- ...

### Files changed
- ...

### Testing
- ...

### Status
- Completed / In Progress / Blocked

### Documentation updated
- PROJECT_CONTEXT.md
- CHANGELOG

### Next task
- ...

### Git
- Commit: ...
- Push: success / not performed / failed
```

If something is not tested, explicitly say so.

Never claim GitHub push succeeded unless it was actually confirmed.

---

# 30. CURRENT PROJECT SNAPSHOT

```text
Project:
KaryaLokal

Concept:
Nationwide handmade marketplace with location-based seller discovery.

Primary users:
Buyer / Seller / Admin

Core differentiator:
Nearby handmade seller discovery using location + distance + map.

Initial product focus:
Bucket flowers and handmade gifts.

Long-term scope:
Nationwide handmade marketplace.

Current phase:
PHASE 0 — Planning

Implementation status:
NOT STARTED

Next priority:
Finalize architecture and begin foundation.
```

Concept:
Nationwide handmade marketplace with location-based seller discovery.

Primary users:
Buyer / Seller / Admin

Core differentiator:
Nearby handmade seller discovery using location + distance + map.

Initial product focus:
Bucket flowers and handmade gifts.

Long-term scope:
Nationwide handmade marketplace.

Current phase:
PHASE 0 — Planning

Implementation status:
NOT STARTED

Next priority:
Finalize architecture and begin foundation.
```

---

# END OF PROJECT CONTEXT

**AI RULE:** This document is living documentation. Keep it synchronized with the actual codebase. If the code and this document disagree, inspect the code and resolve the discrepancy instead of guessing.
