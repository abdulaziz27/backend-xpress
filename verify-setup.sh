#!/bin/bash

echo "🚀 POS Xpress Backend Setup Verification"
echo "========================================"

# Check PHP version
echo "📋 Checking PHP version..."
php --version | head -1

# Check Laravel version
echo "📋 Checking Laravel version..."
php artisan --version

# Check database connection
echo "📋 Checking database connection..."
php artisan migrate:status | head -5

# Check installed packages
echo "📋 Checking key packages..."
composer show | grep -E "(laravel/sanctum|spatie/laravel-permission|filament/filament)" | head -3

# Run basic tests
echo "📋 Running basic tests..."
php artisan test --filter=AuthTest --quiet

# Check API health
echo "📋 Testing API health endpoint..."
php artisan serve --host=127.0.0.1 --port=8001 > /dev/null 2>&1 &
SERVER_PID=$!
sleep 3

HEALTH_RESPONSE=$(curl -s http://127.0.0.1:8001/api/v1/health)
if echo "$HEALTH_RESPONSE" | grep -q "healthy"; then
    echo "✅ API health check passed"
else
    echo "❌ API health check failed"
fi

# Clean up
kill $SERVER_PID 2>/dev/null

# Check Docker configuration
echo "📋 Checking Docker configuration..."
if docker-compose config > /dev/null 2>&1; then
    echo "✅ Docker configuration is valid"
else
    echo "❌ Docker configuration has issues"
fi

echo ""
echo "🎉 Setup verification complete!"
echo ""
echo "Next steps:"
echo "1. For local development: php artisan serve"
echo "2. For Docker development: make start"
echo "3. Access admin panel: http://localhost/admin"
echo "4. API documentation: http://localhost/api/v1/health"
echo ""
echo "Default credentials:"
echo "- System Admin: admin@posxpress.com / password"
echo "- Store Owner: aziz@xpress.com / password"