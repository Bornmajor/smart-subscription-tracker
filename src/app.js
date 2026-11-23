const express = require('express'); // Imports the Express library used to create the API.
const path = require('path'); // Imports Node.js path helpers for safely locating the public dashboard folder.
const subscriptionRoutes = require('./routes/subscriptionRoutes'); // Imports the protected subscription route group.
const deviceRoutes = require('./routes/deviceRoutes'); // Imports the protected device route group (push notification tokens).
const notificationRoutes = require('./routes/notificationRoutes'); // Imports the protected notification route group (sending pushes).
const notFoundMiddleware = require('./middleware/notFoundMiddleware'); // Imports the handler for URLs that do not match a route.
const errorMiddleware = require('./middleware/errorMiddleware'); // Imports the handler for errors passed through Express.

const app = express(); // Creates one Express application that will hold middleware and routes.

app.use(express.json()); // Lets Express convert JSON request bodies into JavaScript values on req.body.
app.use(express.static(path.join(__dirname, '../public'))); // Serves the dashboard files when a browser requests the application root URL.

app.get('/api/health', (req, res) => { // Registers a public GET endpoint at /api/health.
  res.status(200).json({ message: 'Subscription Tracker API is running.' }); // Sends a successful JSON response to the client.
}); // Ends the health-route handler.

app.use('/api/subscriptions', subscriptionRoutes); // Mounts every subscription route below the shared /api/subscriptions URL prefix.
app.use('/api/devices', deviceRoutes); // Mounts every device route below the /api/devices URL prefix.
app.use('/api/notifications', notificationRoutes); // Mounts every notification route below the /api/notifications URL prefix.

app.use(notFoundMiddleware); // Handles requests only after Express cannot find a matching route above.
app.use(errorMiddleware); // Handles errors after routes and other middleware have had a chance to run.

module.exports = app; // Makes this configured Express app available to server.js.
