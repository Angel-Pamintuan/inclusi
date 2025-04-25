<?php
// schedule_interview.php
include 'database.php';

date_default_timezone_set('Asia/Manila');

// Default datetime (used when form is not submitted)
$default_datetime = date('Y-m-d\TH:i');

$applicant_email = 'pamintuanangel91@gmail.com';
// Set from form or fallback to default
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicant_email = $_POST['applicant_email'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
} else {
    $applicant_email = 'pamintuanangel91@gmail.com';
    $start_time = $default_datetime;
    $end_time = $default_datetime;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Google Calendar API Integration</title>
    <meta charset="utf-8" />
</head>

<body>
    <h2>Schedule Interview</h2>

    <form method="POST" action="">
        <label for="applicant_email">Applicant Email:</label>
        <input type="email" id="applicant_email" name="applicant_email"
            value="<?= htmlspecialchars($applicant_email) ?>" required>

        <label for="start_time">Start Time:</label>
        <input type="datetime-local" id="start_time" name="start_time" value="<?= htmlspecialchars($start_time) ?>"
            required>

        <label for="end_time">End Time:</label>
        <input type="datetime-local" id="end_time" name="end_time" value="<?= htmlspecialchars($end_time) ?>" required>

        <input type="submit" value="Schedule Interview">
    </form>

    <p>Scheduling interview for: <?= htmlspecialchars($applicant_email) ?></p>

    <button id="authorize_button" onclick="handleAuthClick()">Authorize & Create Event</button>
    <button id="signout_button" onclick="handleSignoutClick()" style="visibility: hidden;">Sign Out</button>

    <pre id="content" style="white-space: pre-wrap;"></pre>

    <script>
        const rawStart = "<?= $start_time ?>";
        const rawEnd = "<?= $end_time ?>";
        const applicantEmail = "<?= $applicant_email ?>";

        function toISOWithTimezone(localTime) {
            const date = new Date(localTime);
            date.setHours(date.getHours() + 8); // Adjust for Asia/Manila
            return date.toISOString();
        }

        const startTime = toISOWithTimezone(rawStart);
        const endTime = toISOWithTimezone(rawEnd);

        const CLIENT_ID = '914973752910-1v40enbgd16j6t7rmmnk6oi45h57q3t4.apps.googleusercontent.com';
        const API_KEY = 'AIzaSyANqyes2xllFl76ntKOOZv96rNdMzD79qg';
        const DISCOVERY_DOC = 'https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest';
        const SCOPES = 'https://www.googleapis.com/auth/calendar.events';

        let tokenClient;
        let gapiInited = false;
        let gisInited = false;

        document.getElementById('authorize_button').style.visibility = 'hidden';
        document.getElementById('signout_button').style.visibility = 'hidden';

        function gapiLoaded() {
            gapi.load('client', initializeGapiClient);
        }

        async function initializeGapiClient() {
            await gapi.client.init({
                apiKey: API_KEY,
                discoveryDocs: [DISCOVERY_DOC],
            });
            gapiInited = true;
            maybeEnableButtons();
        }

        function gisLoaded() {
            tokenClient = google.accounts.oauth2.initTokenClient({
                client_id: CLIENT_ID,
                scope: SCOPES,
                callback: async (resp) => {
                    if (resp.error !== undefined) {
                        document.getElementById('content').innerText = 'Authorization error: ' + JSON.stringify(resp);
                        return;
                    }
                    document.getElementById('signout_button').style.visibility = 'visible';
                    document.getElementById('authorize_button').innerText = 'Refresh';
                    await createEvent();
                },
            });
            gisInited = true;
            maybeEnableButtons();
        }

        function maybeEnableButtons() {
            if (gapiInited && gisInited) {
                document.getElementById('authorize_button').style.visibility = 'visible';
            }
        }

        function handleAuthClick() {
            tokenClient.requestAccessToken({ prompt: '' });
        }

        function handleSignoutClick() {
            const token = gapi.client.getToken();
            if (token !== null) {
                google.accounts.oauth2.revoke(token.access_token);
                gapi.client.setToken('');
                document.getElementById('content').innerText = '';
                document.getElementById('authorize_button').innerText = 'Authorize';
                document.getElementById('signout_button').style.visibility = 'hidden';
            }
        }

        async function createEvent() {
            const event = {
                summary: 'Interview Schedule',
                location: 'Online (Google Meet)',
                description: 'Scheduled interview with applicant.',
                start: {
                    dateTime: startTime,
                    timeZone: 'Asia/Manila'
                },
                end: {
                    dateTime: endTime,
                    timeZone: 'Asia/Manila'
                },
                attendees: [
                    { email: applicantEmail }
                ],
                reminders: {
                    useDefault: false,
                    overrides: [
                        { method: 'email', minutes: 60 },
                        { method: 'popup', minutes: 10 }
                    ]
                }
            };

            try {
                const response = await gapi.client.calendar.events.insert({
                    calendarId: 'primary',
                    resource: event,
                    sendUpdates: 'all' // ✅ Sends email invite
                });
                const link = response.result.htmlLink;
                document.getElementById('content').innerHTML = `✅ Event created: <a href="${link}" target="_blank">${link}</a>`;
            } catch (err) {
                console.error("❌ Error while creating event: ", err);
                document.getElementById('content').innerText = 'Error: ' + (err.message || JSON.stringify(err));
            }
        }
    </script>

    <!-- Google API scripts -->
    <script async defer src="https://apis.google.com/js/api.js" onload="gapiLoaded()"></script>
    <script async defer src="https://accounts.google.com/gsi/client" onload="gisLoaded()"></script>
</body>

</html>