NEXORA Compatibility Checker Integration

Based on user-uploaded Nexora(3).zip. Original 3D JS and assets preserved.
Changed files:
- Model/CompatibilityChecker.php (new)
- Controllers/compatibility.php (new)
- View/Pages/builder.php
- View/js/builder.js
- View/css/builder.css

REQUIRED: Import product_specs SQL into the same nexora database first.
The existing Config/DBconnection.php class Db is used unchanged.
Start XAMPP Apache + MySQL, open builder via existing navigation.
Select and install CPU, Motherboard, RAM, etc. Check result appears in Your build.
The status intentionally remains unknown unless there is an incompatibility, because full hardware verification requires more specifications.
No modifications to builder-3d.js, 3D models, carts or orders.
Do not upload .env credentials or .git repository folders; this archive excludes .git.
