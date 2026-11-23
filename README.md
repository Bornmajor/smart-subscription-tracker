# Subscription Tracker REST API

[![CI](https://github.com/Bornmajor/subscription-tracker-app/actions/workflows/ci.yml/badge.svg)](https://github.com/Bornmajor/subscription-tracker-app/actions/workflows/ci.yml)

## Project goal

Build a small REST API with Express.js for managing subscriptions. The API will use one shared API key to protect every subscription endpoint. There are no user accounts, registration, login, passwords, or JWTs.

## Scope

The API will support only these subscription operations:

| Method | Endpoint | Purpose |
| --- | --- | --- |
| `POST` | `/api/subscriptions` | Create a subscription (server generates a UUID) |
| `GET` | `/api/subscriptions` | Fetch all subscriptions that are not deleted, plus `serverTime` |
| `GET` | `/api/subscriptions?updatedSince=<ISO time>` | Delta pull: every change since the cursor, including deletions |
| `GET` | `/api/subscriptions/:id` | Fetch one subscription |
| `PUT` | `/api/subscriptions/:id` | Create or update a subscription by ID (last-write-wins) |
| `DELETE` | `/api/subscriptions/:id` | Soft-delete a subscription |
| `POST` | `/api/devices` | Register a phone's push token (or update its reminder setting) |
| `GET` | `/api/devices` | List registered phones |
| `DELETE` | `/api/devices/:token` | Unregister a phone (token URL-encoded) |

All endpoints will require the shared API key.

## Push notification devices

The mobile app registers each phone's **FCM registration token** (its push
address) so the server can send "subscription about to renew" reminders.

```http
POST /api/devices
Content-Type: application/json
x-api-key: YOUR_API_KEY

{
  "token": "<FCM registration token>",
  "platform": "android",
  "reminderTiming": "one_day_before"
}
```

| Field | Values | Default |
| --- | --- | --- |
| `token` | FCM registration token (required, unique) | — |
| `platform` | `android`, `ios` | `android` |
| `reminderTiming` | `one_day_before`, `on_due_date` | `one_day_before` |

Posting the same token again **updates** that device (new setting, fresh
`lastSeenAt`) instead of creating a duplicate — the app re-registers on every
start and whenever Firebase gives it a new token.

## Send a push notification (test)

The server sends notifications through **Firebase Cloud Messaging (FCM)**
using `firebase-admin`. It needs a **service-account key**, a secret that can
send to every user, so it is never committed.

1. Firebase console → ⚙ Project settings → **Service accounts** →
   **Generate new private key**.
2. Save the downloaded file in the project folder as
   `firebase-service-account.json` (git-ignored).
3. In `.env`: `FIREBASE_SERVICE_ACCOUNT_PATH=./firebase-service-account.json`,
   then restart the server.

Send a notification to **every registered device**, right now:

```http
POST /api/notifications/test
Content-Type: application/json
x-api-key: YOUR_API_KEY

{
  "title": "Netflix renews tomorrow",
  "body": "KES 1,100 will be charged",
  "data": { "subscriptionId": "42" }
}
```

```json
{ "message": "Sent to 1 of 1 device(s).", "deviceCount": 1, "successCount": 1, "failureCount": 0, "removedTokens": [] }
```

Each message uses the app's `subscription_reminders` Android channel, `high`
priority (delivered at once, even in Doze) and a 12-hour TTL (dropped if the
phone stays offline longer). Tokens FCM reports as permanently dead
(`registration-token-not-registered`, `invalid-registration-token`) are
deleted automatically. Without a key configured, the endpoint returns `503`.

## Offline sync support

The API is designed for an offline-first mobile app that changes data while
offline and synchronizes later.

| Rule | How the API implements it |
| --- | --- |
| Clients create IDs offline | `_id` is a UUID string. `PUT /:id` with an unknown ID creates the subscription under that ID. |
| Retries are safe | Sending the same `PUT` or `DELETE` twice gives the same result; a retry after a lost response never creates a duplicate. |
| Last write wins | Clients send `updatedAt` (when they made the change). A change older than the stored one is not applied; the response has `applied: false` and the newer server version. Requests without `updatedAt` (the dashboard) count as changed now. |
| Deletions reach every device | `DELETE` sets `deletedAt` (a tombstone) instead of removing the document. Normal reads hide tombstones; delta pulls include them. |
| Deletes win | An edit to a deleted subscription is not applied; the response returns the tombstone so the client deletes its copy too. |
| Pulls don't depend on device clocks | `serverUpdatedAt` records the server's time of every stored change. `?updatedSince=` filters on it. |

**Sync flow for a client:**

1. **Push** each local change: `PUT /api/subscriptions/:id` with the fields and
   `updatedAt`, or `DELETE /api/subscriptions/:id` with `{ "updatedAt": … }`.
   Treat `404` on `DELETE` as already gone. If `applied` is `false`, replace the
   local copy with the returned `subscription` (or delete it when it has
   `deletedAt`).
2. **Pull** changes: `GET /api/subscriptions?updatedSince=<last serverTime>`
   (omit the parameter on the first sync). Upsert each returned subscription
   locally, delete those with `deletedAt`, then store the response's
   `serverTime` for the next pull.

Known limitation: last-write-wins trusts client clocks for `updatedAt`, so a
device with a clock far in the future would win conflicts.

## Run the API locally

1. Install and start the MongoDB Community Server service.
2. Install project packages:

   ```bash
   npm install
   ```

3. Create your local configuration file:

   ```powershell
   Copy-Item .env.example .env
   ```

4. Set a long, private value for `API_KEY` in `.env`. Keep `MONGODB_URI` unchanged when using the default local MongoDB setup.
5. Start the server:

   ```bash
   npm run dev
   ```

6. Open `http://localhost:5000` to use the admin dashboard. Enter the API key from `.env` and use the form and table to create, view, edit, and delete subscriptions.
7. Confirm that the API is running by opening `http://localhost:5000/api/health`. It should return:

   ```json
   { "message": "Subscription Tracker API is running." }
   ```

## Run automated tests

The project uses Node.js's built-in test runner, so no additional testing package is required.

Before testing:

1. Make sure the MongoDB service is running.
2. Make sure `.env` contains both `MONGODB_URI` and `MONGODB_TEST_URI`.
3. Keep `MONGODB_TEST_URI` different from `MONGODB_URI`.

The test suite uses `MONGODB_TEST_URI`, which points to the separate `subscription-tracker-test` database. It deletes that test database before and after the tests run, so **never** set this value to your normal `subscription-tracker` database.

Run all authorization, model, API, CRUD, and error-handling tests with:

```bash
npm test
```

The command runs test files sequentially to keep database cleanup reliable.

| Test file | What it tests |
| --- | --- |
| `test/apiKeyMiddleware.test.js` | A missing key returns `401`, an invalid key returns `401`, and the configured key allows the request to continue. |
| `test/subscriptionModel.test.js` | Valid subscription data passes schema validation; all four billing cycles are accepted; IDs are UUID strings and client-supplied IDs are kept; invalid prices, billing cycles, and dates are rejected. |
| `test/subscriptionApi.test.js` | The protected HTTP API supports create, fetch all, fetch one, update, delete, `404` for absent subscriptions and unknown IDs, `400` for invalid data, and `404` for unknown URLs. |
| `test/deviceApi.test.js` | Device registration: API key required, create, default setting, re-registering updates instead of duplicating, validation, listing, and idempotent unregistering. |
| `test/notificationService.test.js` | Sending: FCM message shape (channel, priority, TTL, string data), sending to all devices, removing dead tokens, the test endpoint (validation, API key, 503 without Firebase). |
| `test/subscriptionSync.test.js` | Offline sync: `PUT` creates under the client ID, retries don't duplicate, last-write-wins, protected fields are ignored, soft deletes and tombstones, deletes win over later edits, `serverUpdatedAt`, and delta pulls with `updatedSince`. |
| `test/errorMiddleware.test.js` | Mongoose client-data errors produce `400`; unexpected server errors produce `500`. |
| `test-support/database.js` | Shared helpers connect to, clean, and disconnect from the dedicated test database. |

When every test passes, the output ends with:

```text
ℹ pass 45
ℹ fail 0
```

## Continuous integration

The project uses a GitHub Actions workflow, [`.github/workflows/ci.yml`](.github/workflows/ci.yml), to run the test suite automatically on every push and pull request to `main`.

| Step | What it does |
| --- | --- |
| Start MongoDB | Runs a MongoDB 7 service container so the integration tests use a real database. |
| Node.js matrix | Runs the tests on Node.js 20, 22, and 24 in parallel. |
| Install dependencies | Runs `npm ci` with npm caching for fast, reproducible installs. |
| Run tests | Runs `npm test` with `MONGODB_TEST_URI` and a placeholder `API_KEY` set in the workflow, so no repository secrets are required. |

The CI badge at the top of this README shows the result of the latest run. Results for each run appear in the repository's **Actions** tab and on pull requests.

## Tools

| Tool | Purpose |
| --- | --- |
| Node.js | Runs the JavaScript application |
| Express | Creates routes, middleware, and HTTP responses |
| MongoDB | Stores subscription data |
| Mongoose | Defines the subscription schema and communicates with MongoDB |
| dotenv | Loads local configuration and secrets from `.env` |
| nodemon | Restarts the development server after code changes |
| Postman or Thunder Client | Sends requests to test the API |

## Authorization

The application will use a single API key stored in the `.env` file:

```env
API_KEY=replace-with-a-long-random-value
```

Each request must send this value in the `x-api-key` header:

```http
x-api-key: replace-with-a-long-random-value
```

An authorization middleware will compare the request header with `API_KEY`. If it is missing or invalid, it will return `401 Unauthorized`; otherwise, it will allow the request to reach the subscription route.

This provides simple shared protection for learning purposes. It is not a replacement for individual user authentication in a multi-user production application.

## Architecture

```text
API client (Postman, Thunder Client, or frontend)
                    |
                    v
             Express route
                    |
                    v
       API-key authorization middleware
                    |
                    v
        Subscription controller logic
                    |
                    v
        Mongoose subscription model
                    |
                    v
                 MongoDB
```

## Project organization

```text
subscription-tracker-app/
├── src/
│   ├── config/
│   │   ├── db.js                      # MongoDB connection
│   │   └── firebase.js                # FCM client (service-account key)
│   ├── controllers/
│   │   └── subscriptionController.js  # CRUD request logic
│   ├── middleware/
│   │   ├── apiKeyMiddleware.js        # Shared API-key protection
│   │   ├── errorMiddleware.js         # Central error responses
│   │   └── notFoundMiddleware.js      # Unknown-route response
│   ├── models/
│   │   └── Subscription.js            # Database schema
│   ├── services/
│   │   └── notificationService.js     # Sends pushes, removes dead tokens
│   ├── routes/
│   │   └── subscriptionRoutes.js      # Endpoint definitions
│   ├── app.js                         # Express configuration
│   └── server.js                      # Application startup
├── public/
│   ├── index.html                      # Admin dashboard page
│   ├── styles.css                      # Dashboard visual styles
│   └── app.js                          # Dashboard CRUD behavior
├── .env                               # Local secrets; never commit
├── .env.example                       # Safe environment-variable template
├── .gitignore
├── package.json
└── README.md
```

## Subscription data

Each subscription will use the following fields:

```js
{
  _id: String,              // UUID; generated by the server or supplied by an offline client
  name: String,             // For example: Netflix
  price: Number,            // Must be greater than zero
  billingCycle: String,     // daily, weekly, monthly, or yearly
  nextPaymentDate: Date,
  category: String,         // For example: entertainment
  createdAt: Date,          // Set automatically when the document is first stored
  updatedAt: Date,          // When the latest change was made (client time); used for last-write-wins
  deletedAt: Date | null,   // Set when soft-deleted (tombstone); null otherwise
  serverUpdatedAt: Date     // When the server stored the latest change; used for delta pulls
}
```

Only `name`, `price`, `billingCycle`, `nextPaymentDate`, and `category` can be
set from request bodies; `updatedAt` is read as the change time, and all other
fields are controlled by the server.

## Sequential implementation plan

### 1. Initialize the Node.js project

- Create `package.json`.
- Install Express, Mongoose, dotenv, and nodemon.
- Add development and start scripts.
- Create `.gitignore` so `.env` and `node_modules` are excluded.

### 2. Configure environment variables

- Create `.env` with `PORT`, `MONGODB_URI`, and `API_KEY`.
- Create `.env.example` with the same variable names but no real values.
- Load the variables through dotenv when the server starts.

### 3. Build the Express application

- Create `app.js` and `server.js`.
- Add Express JSON parsing so the API can accept JSON request bodies.
- Add a simple health endpoint to confirm that the server runs.
- Add not-found and central error middleware.

### 4. Connect to MongoDB

- Create `src/config/db.js`.
- Connect Mongoose using `MONGODB_URI`.
- Start the server only after the database connection succeeds.

### 5. Create the Subscription model

- Create `src/models/Subscription.js`.
- Define the fields shown in the subscription data section.
- Add schema validation for required values, a positive price, valid billing cycles, and valid dates.
- Enable Mongoose timestamps for `createdAt` and `updatedAt`.

### 6. Add shared API-key middleware

- Create `src/middleware/apiKeyMiddleware.js`.
- Read `x-api-key` from each request.
- Compare it to `API_KEY`.
- Return `401 Unauthorized` if it is absent or does not match.
- Apply the middleware to every `/api/subscriptions` route.

### 7. Implement subscription CRUD controllers

Create `src/controllers/subscriptionController.js` with:

1. `createSubscription` to validate and save a new subscription.
2. `getSubscriptions` to return all saved subscriptions.
3. `getSubscriptionById` to return one subscription by its ID.
4. `updateSubscription` to find and update a subscription by its ID.
5. `deleteSubscription` to remove a subscription by its ID.

The controllers will return consistent JSON responses and appropriate HTTP status codes:

| Situation | Status |
| --- | --- |
| Subscription created | `201 Created` |
| Subscription fetched, updated, or deleted | `200 OK` |
| Invalid request data | `400 Bad Request` |
| Missing or invalid API key | `401 Unauthorized` |
| Subscription does not exist | `404 Not Found` |
| Unexpected server/database error | `500 Internal Server Error` |

### 8. Define routes

- Create `src/routes/subscriptionRoutes.js`.
- Map the five API endpoints to their controller functions.
- Apply API-key middleware before each controller runs.
- Mount the routes from `app.js`.

### 9. Test each endpoint

Test with Postman or Thunder Client in this order:

1. Run the health endpoint without an API key.
2. Call `GET /api/subscriptions` without an API key and confirm it returns `401`.
3. Call it again with a valid `x-api-key` header.
4. Create a subscription.
5. Fetch subscriptions and find the created record.
6. Fetch that record by its ID.
7. Update that record using its ID.
8. Delete that record using its ID.
9. Try invalid request bodies, invalid IDs, missing IDs, and invalid API keys.

## Request examples

Replace `YOUR_API_KEY` with the private value from your `.env` file. Replace `SUBSCRIPTION_ID` with the `_id` returned after creating a subscription.

### Check the server

This endpoint is public, so it does not need the API key:

```bash
curl http://localhost:5000/api/health
```

### Create a subscription

```http
POST /api/subscriptions
Content-Type: application/json
x-api-key: YOUR_API_KEY

{
  "name": "Netflix",
  "price": 15.49,
  "billingCycle": "monthly",
  "nextPaymentDate": "2026-10-07",
  "category": "entertainment"
}
```

### Fetch all subscriptions

```http
GET /api/subscriptions
x-api-key: YOUR_API_KEY
```

### Fetch one subscription

```http
GET /api/subscriptions/SUBSCRIPTION_ID
x-api-key: YOUR_API_KEY
```

### Update a subscription

You only need to provide the fields you want to change:

```http
PUT /api/subscriptions/SUBSCRIPTION_ID
Content-Type: application/json
x-api-key: YOUR_API_KEY

{
  "price": 17.99
}
```

### Create or update from an offline client

The client chooses the ID (a UUID) and sends when it made the change. If the ID
is new, the response is `201` and the subscription is created; otherwise it is
updated unless the server already has a newer change (`"applied": false`).

```http
PUT /api/subscriptions/3f2b8c1e-6a4d-4f0e-9b7a-2c5d8e1f0a3b
Content-Type: application/json
x-api-key: YOUR_API_KEY

{
  "name": "Netflix",
  "price": 15.49,
  "billingCycle": "monthly",
  "nextPaymentDate": "2026-10-07",
  "category": "entertainment",
  "updatedAt": "2026-10-05T09:30:00.000Z"
}
```

### Delete a subscription

The subscription is soft-deleted (kept as a tombstone with `deletedAt`).
Offline clients may send when they deleted it:

```http
DELETE /api/subscriptions/SUBSCRIPTION_ID
Content-Type: application/json
x-api-key: YOUR_API_KEY

{
  "updatedAt": "2026-10-05T09:45:00.000Z"
}
```

### Pull changes since the last sync

Use the `serverTime` from the previous response as the cursor. The response
includes deleted subscriptions (with `deletedAt`) and a new `serverTime`.

```http
GET /api/subscriptions?updatedSince=2026-10-05T09:00:00.000Z
x-api-key: YOUR_API_KEY
```
