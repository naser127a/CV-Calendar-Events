# CV Calendar Events API

Laravel 12 REST API for the CV Calendar Events application.

## Stack

- PHP `^8.2`, Laravel `^12.0`
- Laravel Sanctum personal access tokens
- Spatie Laravel Permission for all role and permission assignments
- Spatie Activity Log
- MySQL or SQLite, depending on the deployment environment

There is no `role_id` column on `users`. Users can have multiple roles through Spatie's `roles` relationship. Role and permission changes should use Spatie's APIs; do not add a second role-assignment mechanism.

## Local setup

1. Install PHP dependencies with `composer install`.
2. Copy `.env.example` to `.env`, set the database connection, and generate an application key with `php artisan key:generate`.
3. Run `php artisan migrate` against a development database. Back up databases before running migrations in any shared or production environment.
4. Seed the database if development roles and permissions are needed: `php artisan db:seed`.
5. Run the application with `php artisan serve`.

Run the test suite with `php artisan test`. PHPUnit forces SQLite in-memory and the array cache/queue drivers so tests do not use the database configured in `.env`.

## API conventions

All endpoints are under `/api/v1`. Login and registration are public; other endpoints require a Sanctum bearer token:

```http
Authorization: Bearer <token>
Accept: application/json
```

Successful non-paginated responses use:

```json
{
  "success": true,
  "message": "…",
  "data": {}
}
```

Paginated responses return the resource array in `data` and pagination details in `meta` (`current_page`, `last_page`, `per_page`, `total`, `from`, `to`). Validation failures use HTTP 422 and Laravel's JSON validation-error response. Unauthenticated and unauthorized requests return HTTP 401 and 403, respectively.

User and profile responses expose `roles` as an array of role resources. Role resources contain `id`, `name`, `display_name`, `description`, `is_active`, and loaded `permissions`; permission resources contain `id`, `name`, `display_name`, `description`, `is_active`, and `guard_name`. Do not send or expect `role_id` on users.

## Endpoints

| Method | Path | Description |
| --- | --- | --- |
| `POST` | `/login` | Authenticate; returns user and Sanctum token |
| `POST` | `/register` | Register a user (`name`, `email`, `password`, `password_confirmation`; optional `phone`, `avatar`) |
| `POST` | `/logout` | Revoke the current token |
| `POST` | `/logout-all` | Revoke all tokens for the current user |
| `GET` | `/me` | Current user and roles |
| `PATCH` | `/changePassword-password` | Change current user's password |
| `GET`, `PATCH` | `/profile` | Read or update the current user's profile |
| `GET`, `POST` | `/users` | List or create users |
| `GET`, `PUT/PATCH`, `DELETE` | `/users/{user}` | Read, update, or soft-delete a user |
| `PATCH` | `/users/{user}/activate` | Activate a user |
| `PATCH` | `/users/{user}/deactivate` | Deactivate a user |
| `PATCH` | `/users/{user}/change-role` | Assign a role by `roleName` |
| `GET` | `/users/trashed` | List soft-deleted users |
| `DELETE` | `/users/{id}/soft-delete` | Soft-delete a user |
| `POST` | `/users/{id}/restore` | Restore a soft-deleted user |
| `DELETE` | `/users/{id}/force-delete` | Permanently delete a soft-deleted user |
| `GET`, `POST` | `/roles` | List or create roles |
| `GET`, `PUT`, `DELETE` | `/roles/{role}` | Read, update, or delete a role |
| `GET` | `/roles/{role}/permissions` | List a role's permissions |
| `POST` | `/roles/{role}/permissions` or `/roles/{role}/permissions/sync` | Replace role permissions |
| `POST` | `/roles/{role}/permissions/attach` | Attach permissions |
| `DELETE` | `/roles/{role}/permissions/detach` | Detach permissions |
| `GET`, `POST` | `/permissions` | List or create permissions |
| `GET`, `PUT`, `DELETE` | `/permissions/{permission}` | Read, update, or delete a permission |
| `GET`, `POST` | `/events` | List or create calendar events |
| `GET`, `PUT/PATCH`, `DELETE` | `/events/{event}` | Read, update, or delete an event |
| `PATCH` | `/events/{event}/activate` | Activate an event |
| `PATCH` | `/events/{event}/deactivate` | Deactivate an event |
| `GET`, `POST` | `/news` | List or create news |
| `GET`, `PUT/PATCH`, `DELETE` | `/news/{news}` | Read, update, or delete news |
| `PATCH` | `/news/{news}/toggle-status` | Toggle news status |
| `PATCH` | `/news/{news}/toggle-breaking` | Toggle breaking-news status |
| `GET` | `/activity-logs` | Search activity logs |
| `GET` | `/activity-logs/{activity}` | Read one activity log |
| `GET` | `/notifications` | Current user's notifications |
| `GET` | `/notifications/unread` | Current user's unread notifications |
| `GET` | `/notifications/unread-count` | Count unread notifications |
| `PATCH` | `/notifications/{id}/read` | Mark one owned notification as read |
| `PATCH` | `/notifications/read-all` | Mark all current user's notifications as read |
| `DELETE` | `/notifications/{id}` | Delete one owned notification |

These paths are relative to `/api/v1`. The live route table can be inspected with `php artisan route:list --path=api/v1`.

## Filtering and pagination

List endpoints accept bounded pagination; `per_page` must be an integer from 1 to 100 where exposed. News supports `search`, `type`, `source_type`, `status`, `is_breaking`, `created_by`, `published_from`, `published_to`, `sort_by`, `sort_order`, `page`, and `per_page`. Sort fields are allow-listed. Calendar events and activity logs also validate their supported filters and sort fields.

Activity logs are scoped to the authenticated user's activity unless that user has the `view_all_activity_logs` permission. Calendar event visibility and cross-owner management similarly require the explicit `view_all_calendar_events` and `manage_all_calendar_events` permissions.

## Authorization

Policies and Spatie permissions protect the API resources. Relevant permission names include:

- Users: `view_users`, `create_users`, `edit_users`, `delete_users`, `activate_users`, `deactivate_users`
- Privileged role assignment: `assign_privileged_roles`
- Roles: `view_roles`, `create_roles`, `edit_roles`, `delete_roles`, `assign_role_permissions`
- Permissions: `view_permissions`, `create_permissions`, `edit_permissions`, `delete_permissions`
- News: `view_news`, `view_all_news`, `create_news`, `edit_news`, `delete_news`, `activate_news`, `deactivate_news`
- Calendar: `view_calendar`, `view_all_calendar_events`, `create_calendar`, `edit_calendar`, `manage_all_calendar_events`, `delete_calendar`, `activate_calendar`, `deactivate_calendar`
- Activity logs: `view_activity_log`, `view_all_activity_logs`

The server does not trust a role name or user ID supplied by the client as authorization. A role manager can only grant permissions that they already hold. Granting privileged roles is separately protected; a user who can manage users cannot thereby assign themselves or others a privileged role.
