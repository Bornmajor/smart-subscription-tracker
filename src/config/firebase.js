const fs = require('node:fs'); // Imports Node.js file helpers to read the service-account key file.
const path = require('node:path'); // Imports path helpers so relative key paths work from the project folder.
const { initializeApp, cert, getApps } = require('firebase-admin/app'); // Imports the Firebase Admin helpers that start an authenticated connection.
const { getMessaging } = require('firebase-admin/messaging'); // Imports the Firebase Cloud Messaging (FCM) client used to send push notifications.

function isFirebaseConfigured() { // Tells whether the server has a service-account key, which it needs before it can send anything.
  return Boolean(process.env.FIREBASE_SERVICE_ACCOUNT_PATH); // Treats a set FIREBASE_SERVICE_ACCOUNT_PATH as configured.
} // Ends the configuration check.

function getFirebaseMessaging() { // Returns the FCM client, connecting to Firebase the first time it is needed.
  if (!isFirebaseConfigured()) { // Stops early when no key is configured, with an explanation instead of a crash.
    const error = new Error('Push notifications are not configured: set FIREBASE_SERVICE_ACCOUNT_PATH in .env.'); // Explains what is missing.
    error.statusCode = 503; // Marks it as "service unavailable" so the API responds with 503.
    throw error; // Lets the caller (a controller) pass it to the error middleware.
  } // Ends the missing-configuration check.

  if (getApps().length === 0) { // Connects only once; later calls reuse the same Firebase app.
    const keyPath = path.resolve(process.env.FIREBASE_SERVICE_ACCOUNT_PATH); // Turns the configured path into an absolute path.
    const serviceAccount = JSON.parse(fs.readFileSync(keyPath, 'utf8')); // Reads the secret key file downloaded from the Firebase console.
    initializeApp({ credential: cert(serviceAccount) }); // Starts the Firebase app authenticated as the service account (allowed to send messages).
  } // Ends the one-time connection.

  return getMessaging(); // Returns the FCM client of the connected Firebase app.
} // Ends the messaging getter.

module.exports = { isFirebaseConfigured, getFirebaseMessaging }; // Makes the Firebase helpers available to services.
