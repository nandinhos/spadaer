# Project Overview: spadaer

## Purpose
A Laravel 12 web application, likely for document management (SPADAER - Sistema de Processamento e Armazenamento de Documentos do AER...).

## Tech Stack
- **Backend**: PHP 8.2+, Laravel 12, Livewire.
- **Frontend**: AlpineJS, Tailwind CSS, Vite.
- **Database**: MySQL 8.0 (running in Docker via Sail).
- **Architecture**: Service Layer, Actions, and Repository patterns are encouraged.
- **Governing System**: Integrated with "AI Dev Superpowers" for agentic development.

## Key Directories
- `app/Http/Controllers`: Controllers (thin).
- `app/Models`: Eloquent Models.
- `app/Services`: Business logic.
- `app/Livewire`: Livewire components.
- `resources/views`: Blade templates.
- `tests/`: Feature and Unit tests (TDD is core).
- `.aidev/`: Configuration for AI agents and rules.
