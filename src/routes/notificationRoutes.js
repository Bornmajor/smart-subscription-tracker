const express = require('express'); // Imports Express so this file can create a focused group of routes.
const apiKeyMiddleware = require('../middleware/apiKeyMiddleware'); // Imports the shared API-key protection.
const { sendTestNotification } = require('../controllers/notificationController'); // Imports the notification controller.

const router = express.Router(); // Creates a router used only for notification-related URLs.

router.use(apiKeyMiddleware); // Requires a valid x-api-key header, because anyone able to call this could notify every user.

router.post('/test', sendTestNotification); // Handles POST /api/notifications/test: sends a notification to every registered phone now.

module.exports = router; // Makes this notification router available to app.js.
