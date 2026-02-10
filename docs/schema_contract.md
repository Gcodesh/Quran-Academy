# Schema Contract (Production)

**Version:** 1.1.0 (Updated from text dump)
**Status:** Frozen (Stabilization Phase)
**Source:** TiDB Production (diagnose_schema.php output)

---

## 1. Tables Overview
| Table Name | Status | Notes |
|------------|--------|-------|
| `users` | Active | Core user data + Gamification |
| `courses` | Active | Learning content root |
| `course_modules` | Active | Sections/Chapters (Aliases: `course_sections` exists?) |
| `lessons` | Active | Learning units |
| `user_progress` | Active | Tracking completion (Aliases: `progress`, `lesson_progress_detailed` exist?) |
| `certificates` | Active | Completion proof |
| `enrollments` | Active | User-Course relation |
| `categories` | Active | Course taxonomy |
| `payments` | Active | Financial records |
| `reviews` | Active | User feedback |
| `messages` | Active | Internal comms |
| `notifications` | Active | User alerts |
| `audit_logs` | Active | Security/Activity tracking |

---

## 2. Detailed Schema

### `users`
| Column | Type | Null | Key | Default | Notes |
|--------|------|------|-----|---------|-------|
| `id` | int | NO | PRI | AI | |
| `full_name` | varchar(255) | NO | | NULL | |
| `email` | varchar(255) | NO | UNI | NULL | |
| `password_hash` | varchar(255) | NO | | NULL | |
| `role` | enum | NO | | 'student' | Values: student, teacher, admin |
| `status` | enum | NO | | 'active' | Values: active, suspended, banned, pending |
| `phone` | varchar(20) | YES | | NULL | |
| `country` | varchar(100) | YES | | NULL | |
| `city` | varchar(100) | YES | | NULL | |
| `age` | int | YES | | NULL | |
| `avatar` | varchar(255) | YES | | NULL | |
| `id_card_path` | varchar(255) | YES | | NULL | |
| `points` | int | YES | | 0 | **Gamification** |
| `rank` | varchar(50) | YES | | 'mubtadi' | **Gamification** |
| `total_points` | int | YES | | 0 | **Gamification** |
| `created_at` | timestamp | YES | | CURRENT | |
| `updated_at` | timestamp | YES | | CURRENT | |

### `courses`
| Column | Type | Null | Key | Default | Notes |
|--------|------|------|-----|---------|-------|
| `id` | int | NO | PRI | AI | |
| `teacher_id` | int | YES | | NULL | FK? |
| `category_id` | int | YES | | NULL | FK? |
| `title` | varchar(255) | NO | | NULL | |
| `description` | text | YES | | NULL | |
| `thumbnail` | varchar(255) | YES | | NULL | |
| `image` | varchar(255) | YES | | NULL | Redundant with thumbnail? |
| `price` | decimal(10,2) | YES | | 0.00 | |
| `status` | enum | NO | | 'draft' | Values: draft, pending, published, rejected |
| `created_at` | timestamp | YES | | CURRENT | |
| `updated_at` | timestamp | YES | | CURRENT | |

### `lessons`
| Column | Type | Null | Key | Default | Notes |
|--------|------|------|-----|---------|-------|
| `id` | int | NO | PRI | AI | |
| `course_id` | int | YES | | NULL | **Direct Link?** |
| `section_id` | int | YES | MUL | NULL | FK -> course_sections? |
| `title` | varchar(255) | NO | | NULL | |
| `lesson_type` | enum | YES | | 'lecture' | video/quiz/assignment... |
| `content` | text | YES | | NULL | |
| `video_url` | varchar(255) | YES | | NULL | |
| `audio_url` | varchar(255) | YES | | NULL | |
| `pdf_url` | varchar(255) | YES | | NULL | |
| `media_url` | varchar(255) | YES | | NULL | **Consolidated media?** |
| `media_type` | enum | YES | | 'text' | |
| `media_provider` | enum | YES | | 'local' | |
| `order_number` | int | YES | | 0 | |
| `status` | enum | YES | | 'draft' | |
| `duration` | int | YES | | 0 | |

---

## 3. Discrepancy Analysis (Critical)

1.  **Duplicate Tables?**
    *   `course_modules` vs `course_sections` -> Both exist. Code likely uses one, DB has both.
    *   `user_progress` vs `progress` vs `lesson_progress_detailed` -> Three tables for progress!
    *   `user_profiles` vs `users` -> Profile data split?

2.  **Redundant Columns**
    *   `courses.thumbnail` vs `courses.image`
    *   `lessons.video_url` / `audio_url` / `pdf_url` vs `media_url`

3.  **Missing FK Constraints**
    *   `DESCRIBE` output doesn't show FKs explicitly (Key=MUL hints at it), need to verify referential integrity in Repository layer.

## 4. Action Plan (Phase 2 Preparation)
*   **Consolidate Progress:** Choose `user_progress` (simplest) or `lesson_progress_detailed` (richest). Mark others as deprecate.
*   **Consolidate Modules:** Choose `course_modules` or `course_sections`.
*   **Repo Layer:** Must handle the `media_url` vs `video_url` logic until consolidation.
