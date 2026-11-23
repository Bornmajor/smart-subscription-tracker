const Device = require('../models/Device'); // Imports the model used to read and change registered devices.

async function registerDevice(req, res, next) { // Defines the controller that registers a phone, or updates it if it is already known.
  try { // Begins error handling for the asynchronous database operation.
    const { token, platform, reminderTiming } = req.body ?? {}; // Reads only the fields a device may set.

    const existing = await Device.findOne({ token }); // Looks for a device that already uses this token.

    if (!existing) { // Handles a token the server has never seen: a new install, or a token Firebase just rotated.
      const created = await Device.create({ token, platform, reminderTiming }); // Validates and saves the new device.
      return res.status(201).json({ // Sets the Created HTTP status and starts the JSON response.
        message: 'Device registered successfully.', // Gives the app a clear result message.
        device: created, // Returns the stored device.
      }); // Ends and sends the create response.
    } // Ends the new-device branch.

    if (platform !== undefined) existing.platform = platform; // Updates the platform only when the app sends one.
    if (reminderTiming !== undefined) existing.reminderTiming = reminderTiming; // Updates the reminder setting only when the app sends one.
    existing.lastSeenAt = new Date(); // Records that the app is still installed and using this token.
    await existing.save(); // Validates the changed device and stores it.

    res.status(200).json({ // Sets the successful HTTP status and starts the JSON response.
      message: 'Device updated successfully.', // Gives the app a clear result message.
      device: existing, // Returns the stored device.
    }); // Ends and sends the update response.
  } catch (error) { // Receives validation or database errors.
    next(error); // Passes the error to the central error middleware.
  } // Ends the error-handling block.
} // Ends the register-device controller.

async function unregisterDevice(req, res, next) { // Defines the controller that removes a phone so it stops receiving notifications.
  try { // Begins error handling for the asynchronous database operation.
    await Device.deleteOne({ token: req.params.token }); // Removes the device with this token, if it exists.
    res.status(200).json({ message: 'Device unregistered successfully.' }); // Succeeds even if the device was already gone, so retries are safe.
  } catch (error) { // Receives a database error.
    next(error); // Passes the error to the central error middleware.
  } // Ends the error-handling block.
} // Ends the unregister-device controller.

async function getDevices(req, res, next) { // Defines the controller that lists registered devices (for the dashboard and testing).
  try { // Begins error handling for the asynchronous database operation.
    const devices = await Device.find().sort({ lastSeenAt: -1 }); // Reads every device, most recently active first.
    res.status(200).json({ devices }); // Returns the list.
  } catch (error) { // Receives a database error.
    next(error); // Passes the error to the central error middleware.
  } // Ends the error-handling block.
} // Ends the get-devices controller.

module.exports = { registerDevice, unregisterDevice, getDevices }; // Exports the device controllers for the route file.
