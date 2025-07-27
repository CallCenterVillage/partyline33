# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Symfony-based PHP application that builds an AI conversational agent for the DEF CON 33 conference's Call Center Village party telephone line. The agent runs on ElevenLabs Conversational AI integrated with Twilio to answer phone calls and provide information about DEF CON 33 parties, meetups, and events.

## Commands

### Building the Agent
```bash
# Build complete agent package (runs all steps)
php bin/console app:build-agent

# Individual commands
php bin/console app:generate-keywords                  # Extract keywords from kb/ folder
php bin/console app:generate-pronunciation-dictionary  # Generate pronunciation guide
php bin/console app:compile-agent                      # Compile all files into system prompt
```

### Development
```bash
# Install dependencies
composer install

# List all available commands
php bin/console list
```

## Architecture

The application follows a modular architecture where agent configuration, knowledge base, and compilation logic are separated:

- **Agent Configuration** (`agent/`): Modular files defining personality, tone, guardrails, environment, goals, and tools
- **Knowledge Base** (`kb/`): Markdown files containing event information (Events.md, Meetups.md, Parties.md)
- **Compilation Commands** (`src/Command/`): Symfony console commands that process and combine all inputs
- **Output** (`dist/`): Generated files ready for upload to ElevenLabs platform

The build process:
1. Extract keywords from knowledge base files for better speech recognition
2. Generates pronunciation dictionary in W3C PLS format for technical terms
3. Compiles all agent configuration and knowledge base into a single system prompt
4. Copies necessary files to the dist/ folder for deployment

## Key Implementation Details

- **No test suite or linting tools** - Manual testing and code review are the primary quality controls
- **PHP 8.1+ with Symfony 6.x** - Uses Symfony Console component for command-line operations
- **Event Date Validation** - Agent is aware of dates from August 4-10, 2025 and validates event queries
- **Security Focus** - Designed for public interaction, encourages prompt injection testing (see SECURITY.md)