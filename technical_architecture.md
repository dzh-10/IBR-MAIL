# Technical Architecture

## 1. Directory Structure & Overrides
- **App\Models**: Clean, typed properties for `User`, `Contact`, `ExternalMessage`, `InternalMessage`, `Attachment`.
- **App\Http\Controllers**: Segregated logic into `ContactController`, `SettingController`, etc.
- **App\Services**: The core business logic lives here (`MailSyncService`, `SmtpSendService`, `InternalMessageService`) preventing fat controllers.
- **resources/css/app.css**: Contains the fully custom Vanilla CSS framework with CSS variables for dynamic theming (Dark Mode compatible).
- **resources/views**: Modular blade components (`x-sidebar`, `x-topbar`, `x-app-layout`) for reusable UI parts.

## 2. Database Design
- **Polymorphism**: The `attachments` table uses a polymorphic relationship (`message_id`, `message_type`) to gracefully support attachments on both `InternalMessage` and `ExternalMessage`.
- **Pivot Tables**: Used for `conversation_user` and `contact_group_pivot` to allow complex N:M associations (group chats, group contacts).

## 3. Asynchronous Processes
- The email sync process is stubbed in `MailSyncService` and is designed to be dispatched to a Redis queue.
- Reverb WebSockets broadcast events from `InternalMessageService` directly to connected clients for realtime updates.

## 4. Authentication & Security
- `MailAccount` model utilizes Laravel's native encrypted casting (`'encrypted'`) for storing SMTP/IMAP passwords securely at rest.
