# Laravel Project – Antigravity Development Rules

## 1. CRITICAL DATABASE SAFETY

Existing database data MUST NEVER be deleted, reset, truncated, dropped, or recreated just because a module or code change is being made.

All existing records must remain safe.

Never run:

- php artisan migrate:fresh
- php artisan migrate:refresh
- php artisan migrate:reset
- php artisan db:wipe
- php artisan db:seed
- Model::truncate()
- DB::table(...)->truncate()
- TRUNCATE TABLE
- DROP TABLE
- DROP DATABASE
- DELETE without a specific and safe condition

Do not use any command that can reset or recreate the existing database.

## 2. MODULE CHANGES

When I ask you to modify any module:

- Modify only the requested module.
- Preserve all existing records.
- Preserve existing database relationships.
- Preserve existing functionality unless I explicitly ask to change it.
- Do not delete existing records.
- Do not replace the database with dummy/demo data.
- Do not reset the database after making changes.

UI changes must not affect existing database data.

Blade changes must not affect existing database data.

Controller changes must not affect existing database data.

Route changes must not affect existing database data.

Model changes must not affect existing database records.

## 3. MIGRATIONS

Only create a migration if a database structure change is genuinely required.

Before creating a migration:

1. Check the existing database structure.
2. Check existing migrations.
3. Make the migration backward-safe where possible.
4. Never drop existing data unless I explicitly approve it.

Normal:

php artisan migrate

may be used only when a new migration is genuinely required.

NEVER automatically run:

php artisan migrate:fresh

or any destructive migration command.

## 4. EXISTING DATA

Assume the database already contains important real client data.

Treat all existing records as PRODUCTION-IMPORTANT data even when working locally.

Never assume that the database is empty.

Never create code that depends on deleting all records first.

Never use seeders to overwrite existing real data.

If test data is required, use clearly isolated test data and do not overwrite existing records.

## 5. BEFORE MAKING CHANGES

Before modifying code:

- Understand the existing module.
- Check existing models.
- Check existing controllers.
- Check existing migrations.
- Check existing relationships.
- Check existing routes.
- Check existing database tables.

Do not unnecessarily rewrite existing functionality.

Do not create duplicate migrations or duplicate tables.

Do not change database configuration unless explicitly requested.

Never change:

DB_DATABASE
DB_HOST
DB_USERNAME
DB_PASSWORD

unless I explicitly ask for it.

## 6. AUTOMATIC COMMANDS

Do not automatically execute:

- migrations
- seeders
- database resets
- database imports
- database exports
- destructive SQL
- deployment database commands

If a database command is required, explain it first.

## 7. IF DATABASE STRUCTURE CHANGE IS REQUIRED

Before running any migration, clearly tell me:

- Which table will change
- Which column will change
- Whether existing records are affected
- Whether existing data will be preserved
- Exact migration command

Wait for approval if there is any possibility of existing data loss.

## 8. MODULE DEVELOPMENT RULE

For every future module request:

1. Read this ANTIGRAVITY_RULES.md file first.
2. Inspect the existing implementation.
3. Make the smallest required code changes.
4. Do not reset or delete database data.
5. Do not modify unrelated modules.
6. Do not change existing routes/data structure unnecessarily.
7. Do not remove existing functionality.
8. Test the requested functionality without destroying existing data.

## 9. FINAL CHECK

Before completing any task, verify:

- No existing database records were deleted.
- No table was dropped.
- No table was truncated.
- No database reset command was executed.
- No unrelated migration was created.
- No existing functionality was unnecessarily removed.
- Existing data relationships remain intact.

## 10. IMPORTANT

Whenever I say:

"change module"
"update module"
"modify module"
"fix module"
"redesign module"
"add functionality"

interpret it as:

"Make the requested change while preserving ALL existing database data and existing functionality."

If a requested change could potentially delete or modify existing records, STOP and explain the risk before doing it.

DO NOT make assumptions that the database can be reset.

DO NOT use database reset commands as a shortcut to solve development problems.
