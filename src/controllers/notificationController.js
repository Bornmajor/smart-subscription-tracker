const { sendToAllDevices } = require('../services/notificationService'); // Imports the function that pushes a notification to every registered phone.

async function sendTestNotification(req, res, next) { // Defines the controller that sends any notification right now, for testing.
  try { // Begins error handling for the asynchronous send.
    const { title, body, data } = req.body ?? {}; // Reads the notification text and optional data from the request.

    if (!title || !body) { // Requires both lines of text, because the phone needs something to show.
      return res.status(400).json({ message: 'title and body are required.' }); // Explains how to fix the request.
    } // Ends the validation.

    const result = await sendToAllDevices({ title, body, data }); // Sends to every registered device through FCM.

    res.status(200).json({ // Sets the successful HTTP status and starts the JSON response.
      message: `Sent to ${result.successCount} of ${result.deviceCount} device(s).`, // Summarises the result.
      ...result, // Includes the counts and any dead tokens that were removed.
    }); // Ends and sends the response.
  } catch (error) { // Receives configuration, Firebase or database errors.
    next(error); // Passes the error to the central error middleware (503 when Firebase isn't configured).
  } // Ends the error-handling block.
} // Ends the test-notification controller.

module.exports = { sendTestNotification }; // Exports the controller for the route file.
