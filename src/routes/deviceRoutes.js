const express = require('express'); // Imports Express so this file can create a focused group of routes.
const apiKeyMiddleware = require('../middleware/apiKeyMiddleware'); // Imports the shared API-key protection.
const { registerDevice, unregisterDevice, getDevices } = require('../controllers/deviceController'); // Imports the device controllers.

const router = express.Router(); // Creates a router used only for device-related URLs.

router.use(apiKeyMiddleware); // Requires a valid x-api-key header for every route defined below.

router.post('/', registerDevice); // Handles POST /api/devices: registers a phone's push token or updates its settings.
router.get('/', getDevices); // Handles GET /api/devices: lists registered phones.
router.delete('/:token', unregisterDevice); // Handles DELETE /api/devices/:token: stops sending notifications to that phone.

module.exports = router; // Makes this device router available to app.js.
