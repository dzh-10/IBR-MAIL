# Project Specification: Messagerie

## Core Functional Requirements
1. **Internal Messaging**: Realtime internal messaging.
2. **External Email Messaging**: Send/receive via SMTP/IMAP, save locally.
3. **Unified Messaging Interface**: A single UI showing both.
4. **Multilingual UI**: English & French.
5. **Admin Settings**: System configuration, branding.
6. **Company Contacts List**: Unified internal/external user directory.

## Design
- Custom Vanilla CSS layout representing a modern mail client.
- Left sidebar, center list panel, right reading pane.
- Rich Aesthetics (shadows, transitions, premium color variables).

## Tech Stack
- Laravel 11
- PHP 8.2+
- WebSockets via Reverb
- DB: MySQL
- Job backend: Redis
