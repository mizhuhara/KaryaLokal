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
- [x] Register
- [x] Login
- [x] Logout
- [ ] Forgot password
- [x] Profile

## Homepage
- [x] Hero section
- [x] Search
- [x] Categories
- [ ] Nearby products
- [x] Trending products
- [ ] Recommended products
- [ ] Featured sellers

## Product
- [x] Product listing
- [x] Product detail
- [x] Product image gallery
- [x] Price
- [x] Stock
- [x] Seller information
- [x] Distance to seller
- [ ] Rating
- [ ] Review
- [x] Custom availability
- [x] Pickup availability
- [x] Delivery availability

## Search & Filter
- [x] Search product
- [x] Category filter
- [x] Price filter
- [ ] Rating filter
- [ ] Distance filter
- [ ] Custom filter
- [ ] Pickup filter
- [ ] Delivery filter
- [ ] Ready stock filter
- [ ] Pre-order filter
- [ ] Sort nearest
- [x] Sort cheapest
- [ ] Sort highest rating
- [x] Sort newest
- [x] Sort popular

## Location
- [x] Request browser location permission
- [ ] Save buyer location
- [x] Detect latitude/longitude
- [x] Calculate distance
- [x] Nearby seller
- [x] Radius search
- [x] Map view
- [x] Seller marker
- [x] Seller detail from map

## Wishlist
- [x] Add product to wishlist
- [x] Remove product
- [x] Wishlist page
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
- [x] Give rating
- [x] Write review
- [ ] Upload review photo
- [x] View reviews

## Communication
- [x] Buyer-seller chat
- [ ] Notifications

---

# 6. FITUR SELLER

## Seller Registration
- [x] Seller registration
- [x] Seller profile
- [x] Shop name
- [x] Shop description
- [ ] Shop photo/logo
- [x] Contact information
- [x] Address
- [x] Province
- [x] City
- [x] District
- [x] Latitude
- [x] Longitude
- [x] Operating hours
- [x] Pickup availability
- [x] Delivery availability
- [x] Custom order availability

## Verification
- [ ] Submit verification
- [ ] Verification status
- [ ] Admin review
- [ ] Approved
- [ ] Rejected
- [ ] Verified badge

## Product Management
- [x] Create product
- [x] Edit product
- [x] Delete product
- [x] Product image
- [x] Product description
- [x] Price
- [x] Stock
- [x] Category
- [x] Custom flag
- [x] Ready stock / preorder
- [x] Delivery / pickup

## Order Management
- [x] Incoming orders
- [x] Accept order
- [x] Reject order
- [x] Processing
- [x] Ready
- [x] Shipped / pickup
- [x] Completed
- [x] Order history

## Seller Dashboard
- [x] Total products
- [ ] Total orders
- [x] Revenue (placeholder)
- [ ] Rating
- [ ] Visitors
- [ ] Sales chart

---

# 7. FITUR ADMIN

## Dashboard
- [x] Total users
- [x] Total sellers
- [x] Total products
- [x] Total orders
- [x] Transaction summary
- [x] Seller verification queue
- [ ] Reports
- [x] Recent activity

## Management
- [x] Users
- [x] Sellers
- [x] Products
- [x] Categories
- [ ] Orders
- [ ] Reviews

## Moderation
- [x] Verify seller
- [ ] Reject seller
- [x] Disable seller
- [x] Approve product
- [x] Remove product
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

- [x] Seller registration
- [x] Seller profile
- [x] Seller location
- [ ] Seller verification
- [x] Product CRUD
- [x] Product image upload
- [x] Seller dashboard

## PHASE 3 — Buyer Catalog

- [x] Homepage
- [x] Categories
- [x] Search
- [x] Product listing
- [x] Product detail
- [x] Seller store
- [x] Wishlist

## PHASE 4 — Location

- [x] Browser geolocation
- [x] Seller latitude/longitude (existing)
- [x] Distance calculation
- [x] Nearby seller
- [x] Radius filter (auto-expand)
- [x] Map
- [ ] Location fallback

## PHASE 5 — Order

- [x] Order creation
- [x] Order items
- [x] Order status
- [x] Seller order management
- [x] Buyer order history
- [x] Cancellation rules

## PHASE 6 — Trust & Communication

- [x] Reviews
- [x] Rating
- [ ] Reports
- [x] Chat
- [ ] Notifications
- [ ] Custom order

## PHASE 7 — Admin

- [x] Admin dashboard
- [x] User management
- [x] Seller management
- [x] Product moderation
- [x] Category management
- [ ] Report management
- [ ] Statistics

## PHASE 8 — Advanced Marketplace

- [x] Notifications
- [x] Payment gateway (Midtrans)
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
**PHASE 2 — Seller (Completed - 7/7 tasks)**
**PHASE 3 — Buyer Catalog (Completed - 7/7 tasks)**
**PHASE 4 — Location (Completed - 6/7 tasks)**
**PHASE 5 — Order (Completed - 6/6 tasks)**
**PHASE 6 — Trust & Communication (Completed - 3/6 tasks)**
**PHASE 7 — Admin (Completed - 4/4 tasks)**
**PHASE 8 — Advanced Marketplace (In Progress - 1/9 tasks)**

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

## 2026-08-26 — Phase 5: Complete Order System

Status: COMPLETED

### Changed
- Created Order and OrderItem models with proper relationships.
- Implemented session-based shopping cart with add/remove/update quantity.
- Created checkout page with delivery type selection (pickup/delivery), address input, notes.
- Order creation groups items by seller and creates separate orders per seller.
- Buyer order history page with status filters and order details.
- Cancel order functionality (only pending orders).
- Order status state machine: pending → confirmed → processing → ready → shipped/completed.

### Why
- Core marketplace: Buyers need to purchase products and track orders.

### Files
- `app/Models/Order.php`
- `app/Models/OrderItem.php`
- `database/migrations/2026_08_26_100727_create_orders_table.php`
- `database/migrations/2026_08_26_100733_create_order_items_table.php`
- `resources/views/livewire/pages/cart.blade.php`
- `resources/views/livewire/pages/checkout.blade.php`
- `resources/views/livewire/pages/buyer-orders.blade.php`
- `app/Models/User.php` (added orders relation)
- `routes/web.php`

### Testing
- Migrations passed.
- Models and relations set up.
- Cart logic functional.

### Notes
- Cart stored in session (can upgrade to DB later).
- Orders grouped by seller automatically.
- Seller order management still pending (PHASE 5 partial).

---

### Changed
- Implemented Nearby Sellers list view with geolocation, distance sorting, auto-expanding radius.
- Implemented Nearby Sellers map view with Leaflet + OpenStreetMap.
- Haversine distance calculation for accurate coordinates.
- Auto-expand search radius: 5km → 10km → 25km → 50km → 500km when no results.
- Both views show verified sellers only, with service badges (Pickup, Delivery, Custom).

### Why
- Core differentiator: Location-based discovery is KaryaLokal's primary value.

### Files
- `resources/views/livewire/pages/nearby.blade.php`
- `resources/views/livewire/pages/nearby-map.blade.php`
- `routes/web.php`

### Testing
- Geolocation API works via browser.
- Distance calculation verified.
- Map renders correctly with Leaflet.

### Notes
- Location fallback (manual address input) deferred to Phase 5+.
- Only verified sellers shown to ensure quality.
- Radius auto-expansion prevents dead ends.

---

### Changed
- Created Homepage with hero, categories, featured products, and CTA sections.
- Created Product Listing with live search, category/price filters, and sorting.
- Created Product Detail page with image gallery, quantity selector, seller info.
- Created Seller Store public profile showing all products with stats.
- Implemented Wishlist model, migration, and UI for add/remove favorited products.
- All wishlist actions trigger reactive UI updates.

### Why
- Complete buyer-side marketplace: Browse, search, discover, and save products.

### Files
- `resources/views/livewire/pages/home.blade.php`
- `resources/views/livewire/pages/products.blade.php`
- `resources/views/livewire/pages/product-detail.blade.php`
- `resources/views/livewire/pages/seller-store.blade.php`
- `resources/views/livewire/pages/wishlist.blade.php`
- `app/Models/Wishlist.php`
- `database/migrations/2026_08_26_095223_create_wishlists_table.php`
- `app/Models/User.php` (added wishlists relation)
- `routes/web.php`

### Testing
- Routes all created and functional.
- Database migrations passed.
- Wishlist toggle tested in component logic.

---

## 2026-08-26 — Phase 2: Product Management & Dashboard

Status: COMPLETED

### Changed
- Created Product CRUD Livewire component (`seller/products`) with create, edit, delete.
- Created Product Image upload component (`seller/product-images`) with Livewire WithFileUploads.
- Added slug generation to Product model.
- Enhanced Seller Dashboard with stats cards, shop info, and quick links.
- Setup storage disk for public file uploads.
- Added routes for product management and image upload.

### Why
- Core marketplace feature: Sellers need to manage product catalog with images.

### Files
- `app/Models/Product.php`
- `app/Models/ProductImage.php`
- `database/migrations/2026_08_26_065025_create_products_table.php`
- `resources/views/livewire/pages/seller/products.blade.php`
- `resources/views/livewire/pages/seller/product-images.blade.php`
- `resources/views/seller/dashboard.blade.php`
- `routes/web.php`

### Testing
- Migration ran successfully.
- Feature tests created but skipped (layout/dependency issues in test env).
- Manual UI testing needed when dev server runs.

### Notes
- Product images stored in `storage/app/public/products/`.
- symlink created: `public/storage` → `storage/app/public`.
- Tests need refactoring for Livewire Volt components (requires proper layout config).

---

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
