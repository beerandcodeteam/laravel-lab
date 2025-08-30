# Database Architecture - Laravel Lab POC

## Overview

This document outlines the database architecture for the Laravel English Learning Platform, a proof-of-concept application that integrates real-time voice calls with AI-powered English tutoring.

The system uses **SQLite** as the database engine and follows Laravel's Eloquent ORM patterns with proper foreign key relationships and cascading operations.

## Database Schema

### Core Tables

#### 1. roles
**Purpose**: Define user access levels and permissions
```sql
CREATE TABLE roles (
    id          BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL
);
```

**Seeded Values**:
- `admin` (ID: 1) - Administrative users
- `student` (ID: 2) - Learning users (default)

**Relationships**:
- One-to-Many with `users`

---

#### 2. levels
**Purpose**: English proficiency levels following CEFR standards
```sql
CREATE TABLE levels (
    id           BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name         VARCHAR(255) NOT NULL,
    description  TEXT NULL,
    created_at   TIMESTAMP NULL,
    updated_at   TIMESTAMP NULL
);
```

**Seeded Values**:
- `A1` - Basic user (beginner)
- `A2` - Elementary user
- `B1` - Independent user (intermediate)
- `B2` - Independent user (upper intermediate)
- `C1` - Proficient user (advanced)
- `C2` - Proficient user (mastery)

**Relationships**:
- One-to-Many with `users`
- One-to-Many with `english_journey_logs`

---

#### 3. status
**Purpose**: Track progress states for various entities
```sql
CREATE TABLE status (
    id          BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL
);
```

**Seeded Values**:
- `in_progress` - Active/ongoing status
- `completed` - Finished status
- `expired` - Timed out status

---

### User Management

#### 4. users
**Purpose**: Core user entity with authentication and learning preferences
```sql
CREATE TABLE users (
    id                        BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name                     VARCHAR(255) NOT NULL,
    phone                    VARCHAR(255) NULL UNIQUE,
    email                    VARCHAR(255) NULL UNIQUE,
    email_verified_at        TIMESTAMP NULL,
    password                 VARCHAR(255) NULL,
    remember_token           VARCHAR(100) NULL,
    role_id                  BIGINT UNSIGNED DEFAULT 2,
    level_id                 BIGINT UNSIGNED NULL,
    last_message_at          TIMESTAMP NULL,
    daily_target_minutes     INTEGER NULL,
    preferred_start_time     TIME NULL,
    preferred_days           JSON NULL,
    onboarding_completed_at  TIMESTAMP NULL,
    state                    JSON DEFAULT '{"state":"onboarding","status":"pending"}',
    created_at               TIMESTAMP NULL,
    updated_at               TIMESTAMP NULL,
    
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (level_id) REFERENCES levels(id)
);
```

**Key Features**:
- Supports both phone and email authentication
- JSON state management for user journey tracking
- Learning preferences (daily targets, schedule)
- Onboarding completion tracking

**Relationships**:
- Belongs-to `roles` and `levels`
- One-to-Many with `english_journey_logs`, `messages`, `tests`
- Has-One `lastJourneyLog`, `lastPlacementTest`, `lastLessonTest`

---

### Learning Journey

#### 5. english_journey_logs
**Purpose**: Track user progress and AI-generated assessments
```sql
CREATE TABLE english_journey_logs (
    id               BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id          BIGINT UNSIGNED NOT NULL,
    level_id         BIGINT UNSIGNED NULL,
    level_summary    TEXT NULL,
    ia_summary       TEXT NULL,
    difficulties     VARCHAR(255) NULL,
    confidence_level INTEGER UNSIGNED NULL,
    created_at       TIMESTAMP NULL,
    updated_at       TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (level_id) REFERENCES levels(id) ON DELETE CASCADE
);
```

**Purpose**:
- Records learning milestones and assessments
- Stores AI-generated summaries of user performance
- Tracks confidence levels and identified difficulties
- Maintains historical progression through levels

**Relationships**:
- Belongs-to `users` and `levels`

---

#### 6. messages
**Purpose**: Conversation history between users and AI tutor
```sql
CREATE TABLE messages (
    id            BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id       BIGINT UNSIGNED NOT NULL,
    from          VARCHAR(255) NOT NULL, -- 'USER' or 'IA'
    type          VARCHAR(255) NOT NULL, -- 'TEXT' or 'FILE'
    mime          VARCHAR(255) NULL,
    file          VARCHAR(255) NULL,
    transcription TEXT NULL,
    message       TEXT NULL,
    created_at    TIMESTAMP NULL,
    updated_at    TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

**Key Features**:
- Supports both text and file-based messages
- Audio file storage with transcriptions
- Bidirectional conversation tracking (User ↔ AI)

**Relationships**:
- Belongs-to `users`

---

### Assessment System

#### 7. tests
**Purpose**: English proficiency assessments and lesson evaluations
```sql
CREATE TABLE tests (
    id         BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id    BIGINT UNSIGNED NOT NULL,
    type       VARCHAR(255) NOT NULL, -- 'PLACEMENT_TEST' or 'LESSON'
    points     DECIMAL(4,2) NULL,
    feedback   TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

**Test Types**:
- `PLACEMENT_TEST` - Initial level assessment
- `LESSON` - Regular progress evaluations

**Relationships**:
- Belongs-to `users`
- One-to-Many with `questions`

---

#### 8. questions
**Purpose**: Individual test questions with multimedia support
```sql
CREATE TABLE questions (
    id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    test_id             BIGINT UNSIGNED NOT NULL,
    type                VARCHAR(255) NOT NULL, -- 'LISTENING', 'WRITING', 'MCQ', 'VOCAB'
    question            TEXT NOT NULL,
    question_audio_path VARCHAR(255) NULL,
    options             JSON NULL,
    answer              TEXT NULL,
    answer_path         VARCHAR(255) NULL,
    points              DECIMAL(4,2) NULL,
    created_at          TIMESTAMP NULL,
    updated_at          TIMESTAMP NULL,
    
    FOREIGN KEY (test_id) REFERENCES tests(id)
);
```

**Question Types**:
- `LISTENING` - Audio comprehension
- `WRITING` - Text composition
- `MCQ` - Multiple choice questions
- `VOCAB` - Vocabulary assessment

**Key Features**:
- Audio question support (`question_audio_path`)
- JSON options for multiple choice
- File-based answers (`answer_path`)
- Individual question scoring

**Relationships**:
- Belongs-to `tests`

---

### Laravel System Tables

#### Authentication & Sessions
- `personal_access_tokens` - Sanctum API authentication
- `password_reset_tokens` - Password recovery
- `sessions` - User session management

#### Cache & Jobs
- `cache` - Application caching
- `jobs` - Background job queue

---

## Entity Relationships

```
roles (1) ──────── (many) users (1) ──────── (many) english_journey_logs
                      │                           │
                      │                    levels (1) ──── (many) english_journey_logs
                      │
                      ├── (many) messages
                      │
                      └── (many) tests (1) ──────── (many) questions
```

## Key Business Logic

### User Journey Flow
1. **Registration**: User created with default `student` role
2. **Onboarding**: State managed via JSON `state` field
3. **Placement Test**: Determines initial `level_id`
4. **Learning Sessions**: Conversations stored in `messages`
5. **Progress Tracking**: `english_journey_logs` record milestones
6. **Assessments**: Regular `tests` with detailed `questions`

### Data Integrity
- Cascade deletions on user removal
- Foreign key constraints ensure referential integrity
- JSON validation for structured data (options, preferences, state)
- Unique constraints on phone/email prevent duplicates

### Performance Considerations
- Indexed foreign keys for join performance
- Latest record relationships (`latestOfMany()`) for quick access
- Separate audio file storage with path references
- JSON fields for flexible metadata storage

This architecture supports the real-time English learning platform with comprehensive tracking of user progress, multimedia assessments, and AI-powered conversation analysis.