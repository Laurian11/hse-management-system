# Running Training Module Migrations

## Issue
The error `no such table: training_needs_analyses` indicates the migrations haven't been run yet.

## Solution

### Option 1: Run All Migrations (Recommended)
```bash
php artisan migrate
```

This will run all pending migrations including the 12 new training module migrations.

### Option 2: Run Only Training Migrations
If you want to run only the training migrations:

```bash
php artisan migrate --path=database/migrations/2025_12_04_000001_create_job_competency_matrices_table.php
php artisan migrate --path=database/migrations/2025_12_04_000002_create_training_needs_analyses_table.php
php artisan migrate --path=database/migrations/2025_12_04_000003_create_training_plans_table.php
php artisan migrate --path=database/migrations/2025_12_04_000004_create_training_materials_table.php
php artisan migrate --path=database/migrations/2025_12_04_000005_create_training_sessions_table.php
php artisan migrate --path=database/migrations/2025_12_04_000006_create_training_attendances_table.php
php artisan migrate --path=database/migrations/2025_12_04_000007_create_competency_assessments_table.php
php artisan migrate --path=database/migrations/2025_12_04_000008_create_training_records_table.php
php artisan migrate --path=database/migrations/2025_12_04_000009_create_training_certificates_table.php
php artisan migrate --path=database/migrations/2025_12_04_000010_create_training_effectiveness_evaluations_table.php
php artisan migrate --path=database/migrations/2025_12_04_000011_add_training_integration_fields_to_existing_tables.php
php artisan migrate --path=database/migrations/2025_12_04_000012_add_certificate_foreign_key_to_training_records.php
php artisan migrate --path=database/migrations/2025_12_04_000013_add_job_matrix_foreign_key_to_training_needs.php
```

### Option 3: Check Migration Status
```bash
php artisan migrate:status
```

This will show which migrations have been run and which are pending.

## Migration Order
The migrations are designed to run in this order:
1. `000001` - job_competency_matrices (base table)
2. `000002` - training_needs_analyses (references job_competency_matrices)
3. `000003` - training_plans (references training_needs_analyses)
4. `000004` - training_materials
5. `000005` - training_sessions (references training_plans)
6. `000006` - training_attendances (references training_sessions)
7. `000007` - competency_assessments (references training_sessions)
8. `000008` - training_records (references multiple tables)
9. `000009` - training_certificates (references training_records)
10. `000010` - training_effectiveness_evaluations
11. `000011` - Integration fields (adds to existing tables)
12. `000012` - Certificate foreign key fix
13. `000013` - Job matrix foreign key fix

## After Running Migrations

Once migrations are complete, you should be able to:
1. Access `/training/training-needs` without errors
2. Create training needs
3. Use all training module features

## Troubleshooting

If you encounter foreign key constraint errors:
- Make sure all migrations run in order
- Check that existing tables (users, companies, etc.) exist
- Verify database connection is working

If migrations fail:
- Check the error message
- Verify database permissions
- Ensure all required tables exist (users, companies, departments, etc.)
