# CI Pipeline for BloodLink

This repository uses GitHub Actions for continuous integration and automated testing.

## 🚀 Pipeline Overview

### 1. Test Stage
- **Runs on**: Every push and pull request
- **Environment**: Ubuntu Latest
- **Database**: MySQL 8.0
- **PHP Version**: 8.2
- **Coverage**: Xdebug with Codecov integration

### 2. Build Stage
- **Runs on**: Main branch only
- **Docker**: Multi-stage build
- **Registry**: Docker Hub
- **Tags**: Latest and commit SHA

### 3. Security Scan Stage
- **Runs on**: Main branch only
- **Scanner**: Trivy vulnerability scanner
- **Results**: Uploaded to GitHub Security tab

## 🔧 Required Secrets

Add these secrets to your GitHub repository (optional for Docker builds):

### Docker Hub (Optional)
- `DOCKER_USERNAME`: Your Docker Hub username
- `DOCKER_PASSWORD`: Your Docker Hub access token

> **Note**: You can run the complete pipeline without Docker Hub - only the build stage will be skipped.

## 🧪 Testing

### Running Tests Locally
```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test --filter AuthServiceTest
```

### Test Structure
```
tests/
├── Unit/           # Unit tests for individual classes
│   └── AuthServiceTest.php
└── Feature/        # Feature tests for endpoints
    └── AuthControllerTest.php
```

### Coverage Requirements
- **Minimum Coverage**: 80%
- **Report Format**: Clover XML
- **Upload**: Codecov (optional)

## 🐳 Docker Configuration

### Build Process
1. **Multi-stage build** with PHP and Nginx
2. **Optimized layers** for faster builds
3. **Security scanning** with Trivy
4. **Version tagging** for releases

### Local Development
```bash
# Start the application locally
docker-compose up -d

# Run local deployment script
./deploy.sh local

# Access the application
http://localhost:8000
```

## 📊 Monitoring

### Pipeline Status
- ✅ **Green**: All tests passed, image built successfully
- ⚠️ **Yellow**: Tests passed, build warnings
- ❌ **Red**: Tests failed, build blocked

### Notifications
- **Pull Requests**: Status comments
- **GitHub Security**: Vulnerability reports
- **Codecov**: Coverage reports (if configured)

## 🔍 Debugging

### Failed Tests
1. Check the **Actions** tab in GitHub
2. Review **test logs** for specific errors
3. Check **coverage reports** for missing tests

### Build Issues
1. Verify **Dockerfile** syntax
2. Check **Docker Hub** credentials (if building)
3. Review **build logs** for errors

## 🚀 Quick Start

### First Time Setup
```bash
# 1. Fork this repository
# 2. Clone locally
git clone <your-fork>
cd bloodlink

# 3. Start Docker
docker-compose up -d

# 4. Run tests
php artisan test

# 5. Push to your fork
git add .
git commit -m "Initial setup"
git push origin main
```

### Feature Development
```bash
# 1. Create feature branch
git checkout -b feature/new-feature

# 2. Write code and tests
# 3. Run local tests
php artisan test

# 4. Commit and push
git add .
git commit -m "Add new feature"
git push origin feature/new-feature

# 5. Create pull request
# 6. Review pipeline results
```

## 📝 Best Practices

### Code Quality
- ✅ **PSR-12** coding standards
- ✅ **Type hints** for all methods
- ✅ **Documentation** for complex logic
- ✅ **Test coverage** > 80%

### Git Workflow
- ✅ **Feature branches** for development
- ✅ **Pull requests** for review
- ✅ **Squash merges** for clean history
- ✅ **Semantic versioning** for releases

### Security
- ✅ **Secret scanning** enabled
- ✅ **Dependency updates** automated
- ✅ **Vulnerability scanning** in pipeline
- ✅ **Access control** with RBAC

## 🆘 Troubleshooting

### Common Issues
1. **Database connection errors** - Check MySQL service in Docker
2. **Missing secrets** - Only needed for Docker builds
3. **Docker build failures** - Check Dockerfile syntax
4. **Test failures** - Run tests locally first

### Getting Help
- 📖 **Documentation**: Check this README first
- 🐛 **Issues**: Create GitHub issue with details
- 💬 **Discussions**: Ask questions in community forum

## 🎯 Perfect for Development

This CI/CD setup is designed for **development workflows**:

- ✅ **No production server required**
- ✅ **Local development** with Docker
- ✅ **Automated testing** on every push
- ✅ **Optional Docker builds** for sharing
- ✅ **Security scanning** for safety
- ✅ **Easy local setup** with one command

---

**Happy Coding! 🎉**
