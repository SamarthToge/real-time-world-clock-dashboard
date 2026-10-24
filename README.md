# Meridian — World Clock Console

An advanced, real-time world clock dashboard. Built with **React** on the
frontend (analog + digital clocks, timezone management, alarms, dynamic UI
controls) and a lightweight **PHP** REST API on the backend for persisting
alarms and timezone preferences.

![status](https://img.shields.io/badge/status-active-35D0A0)

## Features

- **Live analog + digital clocks** for any number of world timezones, all driven by a single shared heartbeat (`useClock`) instead of one timer per card — keeps the dashboard smooth even with many clocks on screen.
- **Timezone management** — search and add any of 20 curated world regions, remove any non-home zone, day/night indicator per card, UTC offset label.
- **Alarms** — create alarms against any timezone, one-off / daily / weekdays repeat, enable/disable toggle, WebAudio chime + browser notification when an alarm fires, persisted server-side via the PHP API.
- **Dynamic UI controls** — dark/light theme toggle, 12h/24h format toggle, both persisted in `localStorage`.
- **PHP backend** — JSON-file backed REST API (`/api/alarms.php`, `/api/timezones.php`) with validation, no database setup required.

## Project structure

```
world-clock-dashboard/
├── frontend/                  React app (Vite)
│   ├── index.html
│   ├── package.json
│   ├── vite.config.js
│   └── src/
│       ├── main.jsx
│       ├── App.jsx / App.css
│       ├── index.css          design tokens (colors, type)
│       ├── components/
│       │   ├── Header.jsx
│       │   ├── AnalogClock.jsx
│       │   ├── DigitalClock.jsx
│       │   ├── TimezoneCard.jsx
│       │   ├── TimezoneSelector.jsx
│       │   ├── AlarmPanel.jsx
│       │   └── AlarmModal.jsx
│       ├── hooks/
│       │   ├── useClock.jsx   shared ticking heartbeat (Context)
│       │   └── useAlarms.js   alarm CRUD + trigger detection
│       └── utils/
│           ├── timezones.js   timezone catalogue + Intl helpers
│           └── api.js         fetch client for the PHP backend
└── backend/                   PHP REST API
    ├── config.php             JSON-file storage helpers
    ├── index.php
    ├── .htaccess
    ├── api/
    │   ├── alarms.php
    │   └── timezones.php
    └── data/                  runtime JSON storage (gitignored)
```

## Getting started

### 1. Backend (PHP)

Requires PHP 8.0+. No database or Composer dependencies needed.

```bash
cd backend
php -S localhost:8000
```

The API is now live at `http://localhost:8000/api/alarms.php` and
`http://localhost:8000/api/timezones.php`.

### 2. Frontend (React)

Requires Node.js 18+.

```bash
cd frontend
npm install
npm run dev
```

Open `http://localhost:5173`. The Vite dev server proxies `/api/*` requests
to `http://localhost:8000` (see `vite.config.js`), so the two servers work
together with no CORS configuration needed in development.

### 3. Production build

```bash
cd frontend
npm run build
```

Deploy the contents of `frontend/dist/` behind any static host, and deploy
`backend/` behind Apache/Nginx + PHP-FPM (or `php -S`) on the same origin,
or set `VITE_API_BASE` at build time to point at wherever the backend is
hosted:

```bash
VITE_API_BASE=https://api.example.com/api npm run build
```

## API reference

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/alarms.php` | List all alarms |
| `POST` | `/api/alarms.php` | Create an alarm — `{ label, time, timezone, repeat, enabled }` |
| `PUT` | `/api/alarms.php?id=` | Patch an alarm |
| `DELETE` | `/api/alarms.php?id=` | Delete an alarm |
| `GET` | `/api/timezones.php` | Get the saved timezone list |
| `POST` | `/api/timezones.php` | Save the timezone list — `{ zones: [...] }` |

## Design notes

The UI takes a "chronometer instrument panel" direction: a dark console
background, an amber sweep-second accent (evoking backlit ship/aircraft
clocks), and a monospace digital readout for precision. See
`frontend/src/index.css` for the full token system.

## License

MIT — see `LICENSE`.
