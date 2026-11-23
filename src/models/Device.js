const mongoose = require('mongoose'); // Imports Mongoose so this file can define a MongoDB schema and model.

const REMINDER_TIMINGS = ['one_day_before', 'on_due_date']; // Lists when a device wants to be reminded about a subscription that is about to renew.

const deviceSchema = new mongoose.Schema( // Creates rules for the shape of every registered device (one phone with the app installed).
  {
    token: { // Stores the FCM registration token: the phone's "push address" that notifications are sent to.
      type: String, // Requires the token to be text.
      required: [true, 'Device token is required.'], // Rejects a device without a token, because nothing could be sent to it.
      unique: true, // Allows each token only once, so re-registering updates the same device instead of duplicating it.
      trim: true, // Removes accidental whitespace around the token.
    }, // Ends the token field configuration.
    platform: { // Records which operating system the phone runs.
      type: String, // Requires the platform to be text.
      enum: { // Limits the platform to the ones the mobile app supports.
        values: ['android', 'ios'], // Allows only Android and iOS.
        message: 'Platform must be android or ios.', // Explains why Mongoose rejects any other platform.
      }, // Ends the allowed platform values.
      default: 'android', // Assumes Android when the app does not say.
    }, // Ends the platform field configuration.
    reminderTiming: { // Stores the user's reminder setting from the app.
      type: String, // Requires the setting to be text.
      enum: { // Limits the setting to the options the app offers.
        values: REMINDER_TIMINGS, // Allows "one day before" and "on the due date".
        message: 'Reminder timing must be one_day_before or on_due_date.', // Explains why Mongoose rejects any other value.
      }, // Ends the allowed reminder-timing values.
      default: 'one_day_before', // Reminds one day before renewal unless the user chooses otherwise.
    }, // Ends the reminderTiming field configuration.
    lastSeenAt: { // Records when the app last registered this token, which helps spot devices that stopped using the app.
      type: Date, // Stores the time as a date.
      default: Date.now, // Uses the current time for new devices.
    }, // Ends the lastSeenAt field configuration.
  }, // Ends the fields stored for each device.
  {
    timestamps: true, // Makes Mongoose maintain createdAt and updatedAt automatically.
  }, // Ends the schema options.
); // Ends the Device schema definition.

const Device = mongoose.model('Device', deviceSchema); // Creates the model used to query and save devices.

module.exports = Device; // Makes the Device model available to controllers.
module.exports.REMINDER_TIMINGS = REMINDER_TIMINGS; // Shares the allowed reminder timings with other files and tests.
