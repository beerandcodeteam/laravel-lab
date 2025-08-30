![logo-v2-laravel-lab.svg](public/logo-v2-laravel-lab.svg)

# Laravel Lab - AI-Powered English Learning Platform

An Laravel application that combines real-time voice calls with AI-powered English tutoring. This system integrates Twilio for phone calls, OpenAI's Realtime API for conversational AI, n8n for WhatsApp messaging automation, multiagent AI systems, and WebSocket streaming for seamless audio communication.

## 🚀 Features

### Core Functionality
- **Real-time Voice Calls**: Twilio integration for phone-based learning sessions
- **AI-Powered Conversations**: OpenAI Realtime API for intelligent English tutoring
- **WhatsApp Integration**: n8n automation for WhatsApp messaging workflows
- **Multiagent AI Systems**: Coordinated AI agents for enhanced learning experiences
- **WebSocket Streaming**: Bidirectional audio streaming between calls and AI
- **Progress Tracking**: Comprehensive learning journey monitoring
- **Assessment System**: Placement tests and lesson evaluations
- **CEFR Level Management**: Standard European Framework proficiency tracking

### Technical Highlights
- **Async WebSocket Server**: Built with Amphp for high-performance concurrent connections
- **Audio Processing**: G.711 μ-law format support for telephony integration
- **n8n Workflow Automation**: Advanced message routing and WhatsApp integration
- **Multiagent Architecture**: Distributed AI agents for specialized learning tasks
- **Real-time Transcription**: Automatic speech-to-text for both user and AI responses
- **Sanctum Authentication**: Secure API token management
- **pgsql Database**: Lightweight, embedded database solution

## 🏗️ Architecture

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Phone Call    │ -> │  Twilio Service  │ -> │  Laravel API    │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                                                         │
┌─────────────────┐    ┌──────────────────┐            │
│  WhatsApp       │ -> │  n8n Workflows   │ -----------┘
│  Messages       │    │  Automation      │
└─────────────────┘    └──────────────────┘
                                                         │
┌─────────────────┐    ┌──────────────────┐            │
│  WebSocket      │ <- │  Amphp Server    │ <----------┘
│  (Port 1337)    │    │  (Async)         │
└─────────────────┘    └──────────────────┘
         │                                              │
         v                                              │
┌─────────────────┐    ┌──────────────────┐            │
│  OpenAI         │ <- │  Realtime API    │            │
│  Realtime API   │    │  Service         │            │
└─────────────────┘    └──────────────────┘            │
         │                                              │
         v                                              │
┌─────────────────┐    ┌──────────────────┐            │
│  Multiagent     │    │  AI Agent        │            │
│  System         │    │  Coordination    │            │
└─────────────────┘    └──────────────────┘            │
                                                        │
                       ┌──────────────────┐            │
                       │  pgsql Database │ <----------┘
                       │  Progress &      │
                       │  Assessment Data │
                       └──────────────────┘
```

## 📋 Requirements

- **PHP 8.4+**
- **Composer**
- **Node.js & NPM**
- **pgsql** (included with PHP)
- **OpenAI API Key** (with Realtime API access)
- **Twilio Account** (with phone number)
- **n8n Instance** (for WhatsApp workflow automation)
- **WhatsApp Business API** (optional, for enhanced messaging)

## 🛠️ Installation

### 1. Clone Repository
```bash
git clone <repository-url>
cd laravel-lab
```

### 2. Install Dependencies

#### Option A: Using Local Environment
```bash
# PHP dependencies
composer install

# Node.js dependencies (for Vite)
npm install
```

#### Option B: Using Laravel Sail (Docker)
```bash
# Install Composer dependencies via Docker (for fresh clone without Composer locally)
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs

# Start Sail services
./vendor/bin/sail up -d

# Install Node.js dependencies within Sail
./vendor/bin/sail npm install
```

### 3. Environment Setup

#### Local Environment
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create pgsql database
touch database/database.pgsql
```

#### With Laravel Sail
```bash
# Copy environment file
cp .env.example .env

# Generate application key using Sail
./vendor/bin/sail artisan key:generate

# Create pgsql database (Sail will handle permissions)
./vendor/bin/sail exec laravel.test touch database/database.pgsql
```

### 4. Configure Environment Variables
Edit `.env` file with your API credentials:

```env
# Application
APP_NAME="Laravel English Lab"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=pgsql

# Twilio Configuration
TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token  
TWILIO_PHONE_NUMBER=your_twilio_number
TWILIO_WS_URL=ws://your-server:1337/call/
TWILIO_WHATSAPP_ENDPOINT=whatsapp_webhook_url

# OpenAI Configuration  
OPENAI_API_KEY=your_openai_api_key
OPENAI_ORGANIZATION=your_org_id
OPENAI_PROJECT=your_project_id
OPEN_AI_REALTIME_URL=wss://api.openai.com/v1/realtime?model=gpt-4o-realtime-preview-2024-10-01

# n8n Configuration
N8N_HOST=your_n8n_instance_url
N8N_API_KEY=your_n8n_api_key
N8N_WEBHOOK_URL=your_n8n_webhook_url

# Multiagent System
MULTIAGENT_ENABLED=true
MULTIAGENT_COORDINATOR_URL=your_coordinator_endpoint
```

### 5. Database Migration
```bash
php artisan migrate --seed
```

This creates the database schema and seeds initial data:
- **Roles**: admin, student
- **Levels**: A1, A2, B1, B2, C1, C2 (CEFR standards)
- **Status**: in_progress, completed, expired

## 🚀 Running the Application

### Development Mode
```bash
# Start all services concurrently
composer run dev
```

This starts:
- Laravel server (`php artisan serve`)
- Queue worker (`php artisan queue:listen`)
- Log viewer (`php artisan pail`)
- Vite dev server (`npm run dev`)

### Individual Services
```bash
# Laravel API server
php artisan serve

# WebSocket server for call handling
php artisan app:call-web-socket

# Queue worker for background jobs
php artisan queue:work

# Frontend assets
npm run dev
```

### Production Build
```bash
npm run build
```

## 📞 Call Flow

1. **Incoming Call**: Twilio receives call and hits `/api/incoming-call`
2. **TwiML Response**: Returns XML connecting call to WebSocket
3. **WebSocket Connection**: Amphp server accepts connection on port 1337
4. **AI Integration**: OpenAI Realtime API processes conversation
5. **Audio Streaming**: Bidirectional audio between phone ↔ AI
6. **Progress Tracking**: Conversations and assessments saved to database

## 🗂️ Database Schema

The application uses 8 core tables:

- **`users`**: Student profiles with learning preferences
- **`roles`**: Access levels (admin/student)
- **`levels`**: CEFR proficiency levels (A1-C2)
- **`english_journey_logs`**: Progress tracking with AI summaries
- **`messages`**: Conversation history (text/audio)
- **`tests`**: Placement tests and lesson evaluations
- **`questions`**: Multi-type questions (listening/writing/MCQ/vocab)
- **`status`**: Progress states (in_progress/completed/expired)

See [`db-architecture.md`](db-architecture.md) for detailed schema documentation.

## 🔧 API Endpoints

### Authentication
- `POST /api/incoming-message` - Twilio message webhook
- `POST /api/incoming-call` - Twilio call webhook

### User Management
- `GET /api/user` - Get authenticated user
- `PUT /api/onboarding/user/{user}` - Update user profile
- `GET /api/onboarding/user/{user}` - Get onboarding status
- `PUT /api/onboarding/english-journey-log/{user}` - Update learning log

### Learning & Assessment
- `GET /api/messages/{user}` - Message history
- `GET /api/tests/user/{user}` - Test status
- `POST /api/tests/upload-file/question/{question}` - File upload
- `POST /api/tests/upload-file-from-twilio/question/{question}` - Twilio file upload

## 🧪 Testing

```bash
# Run PHPUnit tests
composer run test

# Or directly
php artisan test
```

## 📝 Key Components

### Services
- **`OpenAiRealTimeService`**: Manages WebSocket connection to OpenAI
- **`TwilioCallHandlerService`**: Handles incoming call WebSocket connections
- **`AuthService`**: User authentication and management
- **`MessageService`**: Conversation history management
- **`TestService`**: Assessment and evaluation logic
- **`OnboardingService`**: User journey management

### Commands
- **`CallWebSocket`**: Starts Amphp WebSocket server on port 1337

### Models
- **`User`**: Core user entity with learning progress
- **`Message`**: Conversation messages with transcriptions
- **`Test`** & **`Question`**: Assessment system
- **`EnglishJourneyLog`**: Learning milestone tracking

## 🔒 Security

- **Twilio Request Validation**: Middleware validates webhook signatures
- **Sanctum Authentication**: Token-based API security
- **Input Validation**: Form requests for data validation
- **CORS Configuration**: Proper cross-origin resource sharing
- **Environment Variables**: Sensitive data in `.env`

## 📊 Monitoring

- **Laravel Pail**: Real-time log viewing
- **Queue Jobs**: Background task processing
- **WebSocket Logging**: Connection and message tracking
- **AI Conversation Logs**: User interaction monitoring

## 🚨 Troubleshooting

### Common Issues

**WebSocket Connection Failed**
```bash
# Check if port 1337 is available
netstat -tulpn | grep :1337

# Restart WebSocket server
php artisan app:call-web-socket
```

**OpenAI API Errors**
- Verify API key has Realtime API access
- Check rate limits and billing
- Review OpenAI status page

**Twilio Webhook Issues**
- Ensure webhook URLs are publicly accessible
- Verify request validation middleware
- Check Twilio webhook logs in console

**Database Issues**
```bash
# Reset database
php artisan migrate:fresh --seed

# Check pgsql file permissions
ls -la database/database.pgsql
```

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

**Note**: This is the official Laravel Lab application for AI-powered English learning. For production deployment, ensure proper security hardening, scalability considerations, and comprehensive monitoring are implemented.
