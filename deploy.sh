#!/bin/bash

# BloodLink Local Deployment Script
# Usage: ./deploy.sh [environment]

set -e  # Exit on any error

ENVIRONMENT=${1:-local}
echo "🚀 Starting deployment to $ENVIRONMENT environment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    print_error "Docker is not running. Please start Docker first."
    exit 1
fi

print_status "Docker is running"

# Pull latest changes
echo "📥 Pulling latest changes..."
git pull origin main

# Build and start containers
echo "🐳 Building and starting containers..."
docker-compose down
docker-compose build --no-cache
docker-compose up -d

# Wait for database to be ready
echo "⏳ Waiting for database to be ready..."
sleep 30

# Run migrations
echo "🗃️ Running database migrations..."
docker-compose exec app php artisan migrate --force

# Clear caches
echo "🧹 Clearing application caches..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Check application health
echo "🏥 Checking application health..."
if curl -f http://localhost:8000/api/test > /dev/null 2>&1; then
    print_status "Application is healthy and responding"
else
    print_error "Application health check failed"
    exit 1
fi

# Show running containers
echo "📊 Running containers:"
docker-compose ps

print_status "Local deployment completed successfully!"

echo ""
echo "🌐 Application URLs:"
echo "   Frontend: http://localhost:8000"
echo "   API: http://localhost:8000/api"
echo "   Test: http://localhost:8000/api/test"

echo ""
echo "🔧 Useful commands:"
echo "   View logs: docker-compose logs -f"
echo "   Stop app: docker-compose down"
echo "   Restart: docker-compose restart"
echo "   Access shell: docker-compose exec app bash"
echo "   Run tests: docker-compose exec app php artisan test"
