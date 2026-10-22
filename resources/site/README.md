# Integrated GenZ website

This is the editable React frontend used by the Laravel homepage. The original
frontend folder remains separate.

## Build the website

From `E:\genz\source-code\resources\site`:

```powershell
npm ci
npm run build
```

The production build goes to `E:\genz\source-code\public\site`. Vite writes its
entry and CSS names to `public/site/.vite/manifest.json`. Open the Laravel
homepage, usually `http://127.0.0.1:8000/`, to use the connected website.

Node is only needed when building frontend code. The normal Laravel server
serves the resulting files; a separate Vite development server is unnecessary.

## Content and data

- `content/*.json`: default CMS content and editor field structure.
- `src/content.ts`: reads the Laravel `window.__GENZ_SITE__` bootstrap payload.
- The admin website editor stores overrides in the database. Content edits do
  not require another frontend build.
- Services, destinations, published news/events, and testimonials are supplied
  by the existing backend catalog.
- `public/assets`: frontend-only images and decorative assets.
- `src/assets/models`: the airplane model bundled by Vite.

Frontend CSS and files use `/site/` and are isolated from the backend assets.

## Existing student flows

- `/student-registration`: detailed student registration/inquiry.
- `/register`: create a student account.
- `/login`: existing account login and role-based dashboard.

The homepage inquiry and newsletter forms submit to Laravel with CSRF
protection. Form success is shown only after a successful JSON response.
