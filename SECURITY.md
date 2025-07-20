# Security Policy

## Supported Versions

We actively maintain and provide security updates for the following versions:

| Version | Supported          |
| ------- | ------------------ |
| 1.x.x   | :white_check_mark: |

## Reporting a Vulnerability

We take security vulnerabilities seriously. If you discover a security issue in this project, please follow these steps:

### **DO NOT** create a public GitHub issue for security vulnerabilities.

### **DO** report security vulnerabilities privately to:

**Email:** support@callcentervillage.com

### What to Include in Your Report

Please provide as much detail as possible:

- **Description** of the vulnerability
- **Steps to reproduce** the issue
- **Potential impact** of the vulnerability
- **Suggested fix** (if you have one)
- **Your contact information** (if you'd like to be credited)

### Response Timeline

- **Initial Response:** Within 48 hours
- **Status Update:** Within 1 week
- **Resolution:** As quickly as possible, typically within 30 days

### Responsible Disclosure

We follow responsible disclosure practices:

1. **Private reporting** to prevent public exploitation
2. **Timely response** and regular updates
3. **Credit acknowledgment** for security researchers
4. **Public disclosure** after fixes are available or 90 days

### Security Best Practices

When using this project:

- **Keep dependencies updated** - Run `composer update` regularly
- **Review generated files** - Check compiled output for sensitive data
- **Use secure environments** - Don't run in production without proper security review
- **Monitor logs** - Watch for unusual activity in your ElevenLabs integration

### Security Considerations

This project:
- **Does not store sensitive data** - All files are configuration and documentation
- **Uses standard PHP libraries** - Leverages well-maintained Symfony components
- **Generates static files** - No runtime vulnerabilities in generated output
- **Requires manual deployment** - No automatic deployment that could be exploited

Thank you for helping keep this project secure! 