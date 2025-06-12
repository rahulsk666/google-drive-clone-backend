# Google Drive–like RESTful API in Laravel – Design Document

## 1. Overview

This project is a RESTful API for a Google Drive–like file storage system, built using the Laravel PHP framework and containerized with Docker. The API allows users to:

- Register and authenticate
- Create folders (with nesting)
- Upload files (per user)
- Rename folders
- Soft-delete (trash/restore) folders and files
- Share folders with other users

The system leverages Laravel's features for security, scalability, and developer productivity, making it suitable for both learning and production use.

---

## 2. Authentication Flow

- **OAuth 2.0:** The API uses OAuth 2.0 for authentication, typically implemented in Laravel using Laravel Passport.
- **Login:** Users authenticate via an OAuth provider (e.g., Google) or via password grant (if enabled).
- **Token Generation:** On successful authentication, an access token (and optionally a refresh token) is issued.
- **Token Usage:** The access token must be sent in the `Authorization: Bearer <token>` header for all protected endpoints.
- **Logout/Revocation:** Tokens can be revoked via the OAuth server.

**Flow:**

1. User authenticates via OAuth (authorization code or password grant).
2. OAuth server issues an access token (and refresh token if applicable).
3. User includes the access token in future requests.
4. Laravel middleware validates the token on each request.
5. Tokens can be revoked or expired for logout.

---

## 3. Database Schema

### `users`

| Field      | Type       | Description                |
| ---------- | ---------- | -------------------------- |
| id         | BIGINT, PK | User ID                    |
| email      | STRING     | Unique email               |
| password   | STRING     | Hashed password (if local) |
| created_at | TIMESTAMP  | Registration date          |
| updated_at | TIMESTAMP  | Last update                |

### `oauth_access_tokens` (Passport)

| Field      | Type       | Description         |
| ---------- | ---------- | ------------------- |
| id         | BIGINT, PK | Token ID            |
| user_id    | BIGINT, FK | Linked user         |
| client_id  | BIGINT, FK | OAuth client        |
| name       | STRING     | Token name          |
| scopes     | TEXT       | Token scopes        |
| revoked    | BOOLEAN    | Revoked flag        |
| expires_at | TIMESTAMP  | Expiry time         |
| created_at | TIMESTAMP  | Token creation time |
| updated_at | TIMESTAMP  | Last update         |

### `folders`

| Field      | Type       | Description              |
| ---------- | ---------- | ------------------------ |
| id         | BIGINT, PK | Folder ID                |
| user_id    | BIGINT, FK | Owner                    |
| name       | STRING     | Folder name              |
| parent_id  | BIGINT, FK | Parent folder (nullable) |
| deleted_at | TIMESTAMP  | Soft delete timestamp    |
| created_at | TIMESTAMP  | Creation time            |
| updated_at | TIMESTAMP  | Last update              |

### `files`

| Field      | Type       | Description           |
| ---------- | ---------- | --------------------- |
| id         | BIGINT, PK | File ID               |
| user_id    | BIGINT, FK | Owner                 |
| folder_id  | BIGINT, FK | Parent folder         |
| name       | STRING     | File name             |
| path       | STRING     | Filesystem path       |
| size       | BIGINT     | File size (bytes)     |
| deleted_at | TIMESTAMP  | Soft delete timestamp |
| created_at | TIMESTAMP  | Upload time           |
| updated_at | TIMESTAMP  | Last update           |

### `folder_user`

| Field      | Type       | Description      |
| ---------- | ---------- | ---------------- |
| id         | BIGINT, PK | Share ID         |
| folder_id  | BIGINT, FK | Shared folder    |
| user_id    | BIGINT, FK | User with access |
| created_at | TIMESTAMP  | Share time       |
| updated_at | TIMESTAMP  | Last update      |

### `file_user`

| Field      | Type       | Description      |
| ---------- | ---------- | ---------------- |
| id         | BIGINT, PK | Share ID         |
| file_id    | BIGINT, FK | Shared file      |
| user_id    | BIGINT, FK | User with access |
| created_at | TIMESTAMP  | Share time       |
| updated_at | TIMESTAMP  | Last update      |

**Relationships:**

- Users own folders and files.
- Folders can be nested via `parent_id`.
- Folders and Files can be shared with other users via the `folder_user` and `file_user`.

---

## 4. Folder Structure Design

- **Nesting:** Each folder has a `parent_id`. Root folders have `parent_id = NULL`.
- **Breadcrumbs/Pathing:** To build a folder's path, recursively fetch parent folders up to the root using Eloquent relationships.
- **Example:**
  - `/Documents/Work/Reports`
    - `Reports` → parent: `Work`
    - `Work` → parent: `Documents`
    - `Documents` → parent: `NULL`

---

## 5. Endpoints List

### Authentication

- **GET /auth/google**

  - Description: Get Google OAuth redirect URL.
  - Response: `{ "url": "https://accounts.google.com/o/oauth2/auth?..." }`
  - Auth: No

- **GET /auth/google/callback**

  - Description: Google OAuth callback. Handles the callback from Google OAuth and returns access tokens.
  - Query Parameters: `code` (string, required)
  - Response: `{ "access_token": "...", "refresh_token": "...", "token_type": "...", "expires_in": ... }`
  - Auth: No

- **POST /auth/revoke**
  - Description: Logout and revoke token. Revokes the user's access token and logs them out.
  - Header: `Authorization: Bearer <token>`
  - Response: `200 OK`
  - Auth: Yes

---

### Folders

- **GET /folders/{id}**

  - Get folder details (with children)
  - Auth: Yes

- **POST /folders**

  - Create folder
  - Request: `{ "name": "...", "parent_id": 2 }`
  - Response: Folder object
  - Auth: Yes

- **PUT /folders/{id}**

  - Rename folder
  - Request: `{ "name": "New Name" }`
  - Auth: Yes

- **DELETE /folders/{id}**

  - Soft-delete folder (move to trash)
  - Auth: Yes

- **POST /folders/{id}/share**
  - Share folder with another user
  - Request: `{ "email": ["user@example.com"] }`
  - Auth: Yes

---

### Files

- **POST /files**

  - Upload file
  - Form-data: `file`, `folder_id`
  - Response: File object
  - Auth: Yes

- **GET /files/{id}/download**

  - Download file
  - Auth: Yes

- **DELETE /files/{id}**
  - Soft-delete file
  - Auth: Yes

---

### Trash

- **GET /trash**

  - List all trashed files/folders
  - Auth: Yes

- **POST /trash/restore**
  - Restore a trashed file or folder
  - Request: `[{ "id": 123, "type": "file" | "folder" }]`
  - Auth: Yes

---

### Users

- **GET /users**
  - List users with optional search filter
  - Query Parameter: `search` (optional, string) — filter users by name or email
  - Response: `[ { "id": 1, "name": "...", "email": "...", "created_at": "..." } ]`
  - Auth: Yes
