require('dotenv').config({ quiet: true }); // Loads API_KEY and MONGODB_TEST_URI before the app and database helpers are used.

const assert = require('node:assert/strict'); // Imports strict assertion helpers that fail a test when values do not match.
const { once } = require('node:events'); // Imports a helper that waits for the HTTP server's listening event.
const test = require('node:test'); // Imports Node.js's built-in test runner.
const app = require('../src/app'); // Imports the fully configured Express application with routes and middleware.
const Device = require('../src/models/Device'); // Imports the model so tests can create and inspect devices.
const { sendToAllDevices, buildMessage } = require('../src/services/notificationService'); // Imports the code under test.
const { // Starts importing the database lifecycle helpers used by this integration-test suite.
  connectTestDatabase, // Imports the helper that connects to the dedicated test database.
  clearTestDatabase, // Imports the helper that removes temporary test data.
  disconnectTestDatabase, // Imports the helper that closes the test database connection.
} = require('../test-support/database'); // Ends the database-helper import.

let server; // Holds the temporary HTTP server created for these tests.
let baseUrl; // Holds the temporary server URL, including the operating-system-selected port.

function fakeMessaging(failures = {}) { // Builds a stand-in for FCM that records messages instead of sending them.
  const sent = []; // Collects every message "sent", so tests can inspect it.
  return { // Returns an object with the one method the service uses.
    sent, // Exposes the recorded messages.
    async sendEachForMulticast(message) { // Mimics firebase-admin's response: one result per token, in order.
      sent.push(message); // Records the message.
      const responses = message.tokens.map((token) => (failures[token] // Fails tokens the test listed.
        ? { success: false, error: { code: failures[token] } } // Mimics an FCM error for that token.
        : { success: true, messageId: `id-${token}` })); // Mimics a successful delivery.
      const successCount = responses.filter((r) => r.success).length; // Counts successes like FCM does.
      return { responses, successCount, failureCount: responses.length - successCount }; // Returns a BatchResponse-like object.
    }, // Ends the fake send method.
  }; // Ends the fake messaging object.
} // Ends the fake builder.

test.before(async () => { // Runs once before this file's tests begin.
  await connectTestDatabase(); // Connects models to the dedicated test database.
  await Device.init(); // Builds the unique token index, as in production.
  server = app.listen(0); // Starts Express on an automatically selected unused port.
  await once(server, 'listening'); // Waits until the temporary server is ready.
  baseUrl = `http://127.0.0.1:${server.address().port}`; // Builds the base address used by requests.
}); // Ends the before-test hook.

test.beforeEach(async () => { // Runs before every test in this file.
  await Device.deleteMany({}); // Removes devices so tests cannot affect each other.
}); // Ends the before-each hook.

test.after(async () => { // Runs once after this file's tests finish, even when a test fails.
  await new Promise((resolve, reject) => server.close((error) => (error ? reject(error) : resolve()))); // Stops the temporary HTTP server.
  await clearTestDatabase(); // Removes every document created by these tests.
  await disconnectTestDatabase(); // Closes Mongoose so the test process can finish.
}); // Ends the after-test hook.

test('buildMessage uses the reminder channel, high priority, a 12h TTL and string data', () => { // Checks the FCM message shape.
  const message = buildMessage(['t1'], { title: 'Netflix renews tomorrow', body: 'KES 1,100', data: { subscriptionId: 42 } }); // Builds a message.

  assert.deepEqual(message.tokens, ['t1']); // Sends to the given token.
  assert.deepEqual(message.notification, { title: 'Netflix renews tomorrow', body: 'KES 1,100' }); // Shows the given text.
  assert.deepEqual(message.data, { subscriptionId: '42' }); // Converts data values to strings, as FCM requires.
  assert.equal(message.android.priority, 'high'); // Delivers right away.
  assert.equal(message.android.ttl, 12 * 60 * 60 * 1000); // Expires after 12 hours.
  assert.equal(message.android.notification.channelId, 'subscription_reminders'); // Matches the app's channel.
}); // Ends the message-shape test.

test('sendToAllDevices sends to every device and removes dead tokens', async () => { // Checks sending and cleanup.
  await Device.create([{ token: 'good' }, { token: 'uninstalled' }, { token: 'temporary-error' }]); // Registers three phones.
  const messaging = fakeMessaging({ // Makes FCM reject two tokens.
    uninstalled: 'messaging/registration-token-not-registered', // Dead for good: must be removed.
    'temporary-error': 'messaging/internal-error', // Temporary: must be kept for the next try.
  }); // Ends the fake setup.

  const result = await sendToAllDevices({ title: 'Hi', body: 'Test' }, { messaging }); // Sends through the fake.

  assert.equal(messaging.sent.length, 1); // Uses one multicast request for three tokens.
  assert.deepEqual({ ...result }, { deviceCount: 3, successCount: 1, failureCount: 2, removedTokens: ['uninstalled'] }); // Summarises correctly.
  const remaining = (await Device.find().sort({ token: 1 })).map((device) => device.token); // Reads the devices left.
  assert.deepEqual(remaining, ['good', 'temporary-error']); // Removed only the dead token.
}); // Ends the send-and-cleanup test.

test('sendToAllDevices with no devices sends nothing', async () => { // Checks the empty case.
  const messaging = fakeMessaging(); // Creates a fake FCM.

  const result = await sendToAllDevices({ title: 'Hi', body: 'Test' }, { messaging }); // Sends to nobody.

  assert.equal(messaging.sent.length, 0); // Did not call FCM at all.
  assert.equal(result.deviceCount, 0); // Reports zero devices.
}); // Ends the empty test.

test('POST /api/notifications/test needs title and body', async () => { // Checks validation on the test endpoint.
  const response = await fetch(`${baseUrl}/api/notifications/test`, { // Sends a request without text.
    method: 'POST', // Uses the endpoint's method.
    headers: { 'Content-Type': 'application/json', 'x-api-key': process.env.API_KEY }, // Supplies JSON and the API key.
    body: JSON.stringify({ title: 'Only a title' }), // Leaves out the body text.
  }); // Ends the request.

  assert.equal(response.status, 400); // Rejects the incomplete request.
}); // Ends the validation test.

test('POST /api/notifications/test returns 503 when Firebase is not configured', async () => { // Checks the missing-key case.
  const saved = process.env.FIREBASE_SERVICE_ACCOUNT_PATH; // Remembers the real setting.
  delete process.env.FIREBASE_SERVICE_ACCOUNT_PATH; // Pretends no key is configured.
  try { // Restores the setting even if the assertion fails.
    const response = await fetch(`${baseUrl}/api/notifications/test`, { // Sends a valid request.
      method: 'POST', // Uses the endpoint's method.
      headers: { 'Content-Type': 'application/json', 'x-api-key': process.env.API_KEY }, // Supplies JSON and the API key.
      body: JSON.stringify({ title: 'Hi', body: 'Test' }), // Supplies both texts.
    }); // Ends the request.

    assert.equal(response.status, 503); // Explains that the service isn't set up instead of crashing.
  } finally { // Runs after the test either way.
    if (saved !== undefined) process.env.FIREBASE_SERVICE_ACCOUNT_PATH = saved; // Restores the real setting.
  } // Ends the restore.
}); // Ends the 503 test.

test('POST /api/notifications/test requires the API key', async () => { // Checks the endpoint is protected.
  const response = await fetch(`${baseUrl}/api/notifications/test`, { method: 'POST' }); // Sends without a key.

  assert.equal(response.status, 401); // Rejects it, because anyone could otherwise notify every user.
}); // Ends the API-key test.
