require('dotenv').config({ quiet: true }); // Loads API_KEY and MONGODB_TEST_URI before the app and database helpers are used.

const assert = require('node:assert/strict'); // Imports strict assertion helpers that fail a test when values do not match.
const { once } = require('node:events'); // Imports a helper that waits for the HTTP server's listening event.
const test = require('node:test'); // Imports Node.js's built-in test runner.
const app = require('../src/app'); // Imports the fully configured Express application with routes and middleware.
const Device = require('../src/models/Device'); // Imports the model so tests can inspect stored devices directly.
const { // Starts importing the database lifecycle helpers used by this integration-test suite.
  connectTestDatabase, // Imports the helper that connects to the dedicated test database.
  clearTestDatabase, // Imports the helper that removes temporary test data.
  disconnectTestDatabase, // Imports the helper that closes the test database connection.
} = require('../test-support/database'); // Ends the database-helper import.

let server; // Holds the temporary HTTP server created for these tests.
let baseUrl; // Holds the temporary server URL, including the operating-system-selected port.

const TOKEN = 'fcm-token-abc:APA91b_example'; // Represents an FCM registration token (real ones are long and contain ':' and '-').

async function send(method, path, body) { // Sends a request with the API key and returns the status and parsed JSON.
  const response = await fetch(`${baseUrl}/api/devices${path}`, { // Sends the request to the temporary server.
    method, // Uses the HTTP method chosen by the test.
    headers: { 'Content-Type': 'application/json', 'x-api-key': process.env.API_KEY }, // Supplies JSON content type and the API key.
    body: body === undefined ? undefined : JSON.stringify(body), // Sends a JSON body only when the test provides one.
  }); // Ends the request options.
  return { status: response.status, body: await response.json() }; // Returns the parts each test checks.
} // Ends the request helper.

test.before(async () => { // Runs once before this file's tests begin.
  await connectTestDatabase(); // Connects models to the dedicated test database.
  await Device.init(); // Builds the unique index on token, so duplicate tokens are rejected as in production.
  server = app.listen(0); // Starts Express on an automatically selected unused port.
  await once(server, 'listening'); // Waits until the temporary server is ready.
  baseUrl = `http://127.0.0.1:${server.address().port}`; // Builds the base address used by each request.
}); // Ends the before-test hook.

test.beforeEach(async () => { // Runs before every test in this file.
  await Device.deleteMany({}); // Removes devices so tests cannot affect each other (keeps the unique index).
}); // Ends the before-each hook.

test.after(async () => { // Runs once after this file's tests finish, even when a test fails.
  await new Promise((resolve, reject) => server.close((error) => (error ? reject(error) : resolve()))); // Stops the temporary HTTP server.
  await clearTestDatabase(); // Removes every document created by these tests.
  await disconnectTestDatabase(); // Closes Mongoose so the test process can finish.
}); // Ends the after-test hook.

test('device routes require the API key', async () => { // Tests that devices are protected like subscriptions.
  const response = await fetch(`${baseUrl}/api/devices`); // Sends a request without the x-api-key header.

  assert.equal(response.status, 401); // Confirms the request is rejected.
}); // Ends the API-key test.

test('POST registers a new device with its reminder setting', async () => { // Tests first-time registration.
  const { status, body } = await send('POST', '', { token: TOKEN, platform: 'android', reminderTiming: 'on_due_date' }); // Registers a phone.

  assert.equal(status, 201); // Confirms the device was created.
  assert.equal(body.device.token, TOKEN); // Confirms the token was stored.
  assert.equal(body.device.reminderTiming, 'on_due_date'); // Confirms the reminder setting was stored.
}); // Ends the registration test.

test('POST without a reminder setting defaults to one day before', async () => { // Tests the default setting.
  const { body } = await send('POST', '', { token: TOKEN }); // Registers a phone without a setting.

  assert.equal(body.device.reminderTiming, 'one_day_before'); // Confirms the default.
  assert.equal(body.device.platform, 'android'); // Confirms the default platform.
}); // Ends the default-setting test.

test('POST again with the same token updates instead of duplicating', async () => { // Tests re-registration (every app start).
  await send('POST', '', { token: TOKEN, reminderTiming: 'one_day_before' }); // Registers the phone.
  const { status, body } = await send('POST', '', { token: TOKEN, reminderTiming: 'on_due_date' }); // Changes the setting.

  assert.equal(status, 200); // Confirms it was treated as an update.
  assert.equal(body.device.reminderTiming, 'on_due_date'); // Confirms the setting changed.
  assert.equal(await Device.countDocuments(), 1); // Confirms only one device exists.
}); // Ends the update test.

test('POST rejects a missing token and invalid values', async () => { // Tests input validation.
  const noToken = await send('POST', '', { reminderTiming: 'on_due_date' }); // Sends no token.
  const badTiming = await send('POST', '', { token: TOKEN, reminderTiming: 'next_week' }); // Sends an unknown setting.
  const badPlatform = await send('POST', '', { token: 'other', platform: 'windows' }); // Sends an unknown platform.

  assert.equal(noToken.status, 400); // Confirms a token is required.
  assert.equal(badTiming.status, 400); // Confirms the setting is validated.
  assert.equal(badPlatform.status, 400); // Confirms the platform is validated.
}); // Ends the validation test.

test('GET lists registered devices', async () => { // Tests the device list used by the dashboard.
  await send('POST', '', { token: TOKEN }); // Registers one phone.

  const { status, body } = await send('GET', ''); // Fetches the list.

  assert.equal(status, 200); // Confirms the request succeeded.
  assert.deepEqual(body.devices.map((d) => d.token), [TOKEN]); // Confirms the phone is listed.
}); // Ends the list test.

test('DELETE removes a device, and deleting twice still succeeds', async () => { // Tests unregistering.
  await send('POST', '', { token: TOKEN }); // Registers the phone.

  const first = await send('DELETE', `/${encodeURIComponent(TOKEN)}`); // Removes it (tokens are URL-encoded because they contain ':').
  const second = await send('DELETE', `/${encodeURIComponent(TOKEN)}`); // Removes it again.

  assert.equal(first.status, 200); // Confirms the deletion succeeded.
  assert.equal(second.status, 200); // Confirms a repeated deletion is not an error.
  assert.equal(await Device.countDocuments(), 0); // Confirms the device is gone.
}); // Ends the delete test.
