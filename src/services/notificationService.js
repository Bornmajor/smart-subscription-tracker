const Device = require('../models/Device'); // Imports the model holding every registered phone's push token.
const { getFirebaseMessaging } = require('../config/firebase'); // Imports the FCM client getter.

const REMINDER_CHANNEL_ID = 'subscription_reminders'; // Names the Android notification channel the mobile app creates; must match the app.
const REMINDER_TTL_MS = 12 * 60 * 60 * 1000; // Keeps an undelivered notification for 12 hours; after that a reminder would arrive too late to be useful.
const MAX_TOKENS_PER_REQUEST = 500; // FCM accepts up to 500 tokens per multicast request.
const DEAD_TOKEN_CODES = new Set([ // Lists FCM errors meaning the token will never work again (app uninstalled, data cleared, malformed).
  'messaging/registration-token-not-registered', // The app was uninstalled or the token was replaced.
  'messaging/invalid-registration-token', // The token is not a valid FCM token.
]); // Ends the dead-token codes.

function buildMessage(tokens, { title, body, data = {}, collapseKey }) { // Builds one FCM multicast message for a batch of tokens.
  const stringData = Object.fromEntries( // FCM data values must be strings, so every value is converted.
    Object.entries(data).map(([key, value]) => [key, String(value)]), // Turns numbers and booleans into text.
  ); // Ends the data conversion.

  return { // Returns the message in the shape firebase-admin expects.
    tokens, // Sends to every token in this batch.
    notification: { title, body }, // The text the phone shows (the system displays it when the app is in the background).
    data: stringData, // Extra values for the app's code, such as which subscription to open when tapped.
    android: { // Android-specific delivery options.
      priority: 'high', // Delivers right away, even from Doze, because it shows a notification the user should see.
      ttl: REMINDER_TTL_MS, // Drops the message if the phone stays offline longer than this.
      ...(collapseKey ? { collapseKey } : {}), // Lets a newer message with the same key replace an older undelivered one.
      notification: { channelId: REMINDER_CHANNEL_ID }, // Shows it on the app's "Subscription reminders" channel (pops up).
    }, // Ends the Android options.
  }; // Ends the message.
} // Ends the message builder.

async function sendToAllDevices(content, { messaging = getFirebaseMessaging() } = {}) { // Sends one notification to every registered phone.
  const devices = await Device.find({}, { token: 1 }); // Reads only the tokens of all registered devices.
  const tokens = devices.map((device) => device.token); // Turns the devices into a plain list of tokens.
  const result = { deviceCount: tokens.length, successCount: 0, failureCount: 0, removedTokens: [] }; // Prepares a summary of what happened.

  for (let start = 0; start < tokens.length; start += MAX_TOKENS_PER_REQUEST) { // Sends in batches of at most 500 tokens.
    const batch = tokens.slice(start, start + MAX_TOKENS_PER_REQUEST); // Takes the next batch of tokens.
    const response = await messaging.sendEachForMulticast(buildMessage(batch, content)); // Asks FCM to deliver the message to each token in the batch.

    result.successCount += response.successCount; // Counts deliveries FCM accepted.
    result.failureCount += response.failureCount; // Counts deliveries FCM rejected.

    response.responses.forEach((sendResult, index) => { // Checks each token's result; responses are in the same order as the tokens.
      if (!sendResult.success && DEAD_TOKEN_CODES.has(sendResult.error?.code)) { // Finds tokens that will never work again.
        result.removedTokens.push(batch[index]); // Remembers the dead token so it can be removed.
      } // Ends the dead-token check.
    }); // Ends the per-token loop.
  } // Ends the batch loop.

  if (result.removedTokens.length > 0) { // Removes dead tokens so future sends don't keep failing on them.
    await Device.deleteMany({ token: { $in: result.removedTokens } }); // Deletes every device whose token FCM rejected for good.
  } // Ends the cleanup.

  return result; // Returns the summary for the caller (API response or logs).
} // Ends the send-to-all function.

module.exports = { sendToAllDevices, buildMessage, REMINDER_CHANNEL_ID }; // Makes the notification helpers available to controllers and tests.
