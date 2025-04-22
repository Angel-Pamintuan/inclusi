<?php
session_start();
$accessibility = isset($_SESSION['accessibility']) ? $_SESSION['accessibility'] : null;

include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if application_id is passed in the URL
$application_id = $_GET['application_id'] ?? null;

$employer_id = $_SESSION['user_id']; // Make sure this is correct for employer accounts

$query = $conn->prepare("SELECT employer_id FROM tbl_employer_profile WHERE user_id = ?");
$query->bind_param("i", $_SESSION['user_id']);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();



if ($application_id) {
    $application_query = "
        SELECT 
            a.application_id,
            a.application_text,
            a.applied_at,
            a.cv_path,
            jp.job_title,
            jp.job_description,
            ap.profile_pic,
            u.fullname,
            u.email,
            s.apply_status
        FROM tbl_applications a
        JOIN tbl_job_post jp ON a.job_id = jp.job_id
        JOIN tbl_apply_status s ON a.apply_status_id = s.apply_status_id
        JOIN tbl_user u ON a.user_id = u.user_id
        LEFT JOIN tbl_applicant_profile ap ON a.user_id = ap.applicant_id
        WHERE a.application_id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($application_query);
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc(); // this is the $row you're using in HTML
    $applicant_email = $applicant['email'];
} else {
    $row = null;
}


$stmt = $conn->prepare($application_query);
$stmt->bind_param("i", $employer_id);
$stmt->execute();
$applications = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">



    <style>
        .header-container {
            /* Rectangle 10 */

            position: absolute;
            width: 394px;
            height: 303px;
            left: -1px;
            top: 0px;

            background: #1E4461;


        }

        .title {
            /* Headline */

            position: absolute;
            width: 254px;
            height: 21px;
            left: 21px;
            top: 61px;

            /* Medium/14 px */
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 500;
            font-size: 14px;
            line-height: 150%;
            /* identical to box height, or 21px */
            display: flex;
            align-items: center;
            letter-spacing: -0.01em;

            /* Grey / 60 */
            color: #95969D;


        }

        .heading {
            /* Headline */

            position: absolute;
            width: 254px;
            height: 26px;
            left: 21px;
            top: 84px;

            /* Bold/22 px */
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 700;
            font-size: 22px;
            line-height: 120%;
            /* or 26px */
            letter-spacing: -0.015em;

            color: #F0F0FC;


        }

        .body-container {
            /* Rectangle 11 */

            position: absolute;
            width: 395px;
            height: 183px;
            left: -2px;
            top: 152px;

            background: #FAFAFD;
            border-radius: 26px 26px 0px 0px;

        }

        .featured-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow-y: auto;
            max-height: 83vh;
            /* Makes it scrollable */
            padding: 10px;
            padding-bottom: 70px;
            width: 100%;
            margin-top: 3px;
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;

            /* IE/Edge */

        }

        .featured {
            width: 100%;
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .messages {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 12px;
            width: 350px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            margin: 10px auto;
        }

        .message-header {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .message-header img {
            border-radius: 50%;
        }

        .company-logo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }

        .header-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .username {
            font-weight: bold;
            font-size: 16px;
            color: #1e1e1e;
        }

        .date {
            font-size: 12px;
            color: #888;
            margin-top: 2px;
        }

        hr {
            margin: 12px 0;
            border: none;
            border-top: 1px solid #ddd;
        }

        .message-body {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .job-title {
            font-size: 18px;
            font-weight: 500;
            color: #333;
        }

        .job-description,
        .cv {
            font-size: 14px;
            color: #555;
        }

        .job-status button {

            font-size: 16px;
            background-color: #e0f4ff;
            color: #1E4461;
            padding: 5px 10px;
            border-radius: 12px;
            display: inline-block;
            margin-top: 4px;
        }






        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 72px;
            background: #FFFFFF;
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
            /* Soft shadow on top */
        }

        footer a {
            font-size: 28px;
            /* Make icons bigger */
            cursor: pointer;
            /* Indicate clickable */
            color: black;
        }

        footer div {
            font-size: 28px;
            /* Make icons bigger */
            cursor: pointer;
            /* Indicate clickable */
        }

        footer button {
            background: none;
            border: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header-container">
            <div class="title">Welcome to InclusiHire!</div>
            <div class="heading">Seek Employees</div>
        </div>
        <div class="body-container">
            <div class="featured-container">


                <?php if ($row): ?>



                    <div class="messages">
                        <div class="message-header">
                            <img src="<?= !empty($row['profile_pic']) ? $row['profile_pic'] : 'image/no-profile.png' ?>"
                                alt="Profile Picture" width="50" height="50">
                            <div class="header-info">
                                <div class="username"><?= htmlspecialchars($row['fullname']) ?></div>
                                <div class="date"><?= date("F j, Y, g:i A", strtotime($row['applied_at'])) ?></div>
                            </div>
                        </div>

                        <hr>

                        <div class="message-body">
                            <div class="job-title">Applying for: <?= htmlspecialchars($row['job_title']) ?></div>
                            <div class="job-description"><?= htmlspecialchars($row['job_description']) ?></div>
                            <div class="cv"> Applicant Resume:
                                <a href="<?= htmlspecialchars($row['cv_path']) ?>" target="_blank" download>
                                    CV/Resume
                                </a>
                            </div>

                            <div class="action d-flex gap-2">
                                <div class="job-status">
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal1">Application
                                        Status</button>
                                </div>
                                <div class="job-status">
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Schedule Interview
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Schedule Interview</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="interviewDate" class="form-label">Interview Date and Time</label>
                                        <input type="datetime-local" class="form-control" id="interviewDate">
                                    </div>

                                    <div class="mb-3">
                                        <label for="applicantEmail" class="form-label">Applicant Email</label>
                                        <input type="email" class="form-control" id="applicantEmail">
                                    </div>


                                    <div class="mb-3">
                                        <label for="meetLink" class="form-label">Google Meet Link</label>
                                        <input type="text" class="form-control" id="meetLink" readonly>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary"
                                        onclick="createMeetEvent()">Schedule</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="update_status.php" method="POST">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Update Application Status</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">

                                        <div class="mb-3">
                                            <label for="apply_status_id" class="form-label">Select New Status:</label>
                                            <select name="apply_status_id" id="apply_status_id" class="form-select"
                                                required>
                                                <?php
                                                $status_query = $conn->query("SELECT apply_status_id, apply_status FROM tbl_apply_status");
                                                while ($status = $status_query->fetch_assoc()) {
                                                    $selected = $status['apply_status'] === $row['apply_status'] ? 'selected' : '';
                                                    echo "<option value='{$status['apply_status_id']}' $selected>{$status['apply_status']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php else: ?>
                    <p>No application found.</p>
                <?php endif; ?>




            </div>
        </div>

        <footer>
            <button onclick="window.location.href='employerhome.php'">
                <ion-icon name="home-outline" style=" font-size: 28px"></ion-icon>
            </button>
            <button onclick="window.location.href='employermessage.php'">
                <ion-icon name="mail-outline" style="color: #1E4461; font-size: 28px"></ion-icon>
            </button>
            <button onclick="window.location.href='employerupload.php'">
                <ion-icon name="add-outline" style="font-size: 28px"></ion-icon>
            </button>
            <button onclick="window.location.href='employerprofile.php'">
                <ion-icon name="grid-outline" style="font-size: 28px"></ion-icon>
            </button>
        </footer>



        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>


        <script src="https://apis.google.com/js/api.js"></script>
        <script>

            const applicantEmail = "<?= $applicant_email ?>";

            const CLIENT_ID = '914973752910-1v40enbgd16j6t7rmmnk6oi45h57q3t4.apps.googleusercontent.com';
            const DISCOVERY_DOCS = ["https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest"];
            const SCOPES = "https://www.googleapis.com/auth/calendar.events";

            function gapiLoaded() {
                gapi.load('client:auth2', initializeGapiClient);
            }




            async function initializeGapiClient() {
                await gapi.client.init({
                    clientId: CLIENT_ID,
                    discoveryDocs: DISCOVERY_DOCS,
                    scope: SCOPES
                });

                gapi.auth2.getAuthInstance().isSignedIn.listen(updateSigninStatus);
                updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());
            }

            function updateSigninStatus(isSignedIn) {
                if (!isSignedIn) {
                    gapi.auth2.getAuthInstance().signIn();
                }
            }

            async function createMeetEvent() {
                const datetimeInput = document.getElementById('interviewDate').value;
                const emailInput = document.getElementById('applicantEmail').value;

                if (!datetimeInput || !emailInput) {
                    alert("Please provide both date/time and email.");
                    return;
                }

                const startDateTime = new Date(datetimeInput);
                const endDateTime = new Date(startDateTime.getTime() + 30 * 60000); // 30 minutes later

                const event = {
                    summary: 'Interview with Applicant',
                    description: 'Google Meet interview scheduled via InclusiHire.',
                    start: {
                        dateTime: startDateTime.toISOString(),
                        timeZone: 'Asia/Manila',
                    },
                    end: {
                        dateTime: endDateTime.toISOString(),
                        timeZone: 'Asia/Manila',
                    },
                    attendees: [
                        { email: applicantEmail }
                    ],
                    conferenceData: {
                        createRequest: {
                            requestId: "req-" + Math.random().toString(36).substring(2, 15),
                            conferenceSolutionKey: { type: "hangoutsMeet" }
                        }
                    }
                };

                try {
                    const response = await gapi.client.calendar.events.insert({
                        calendarId: 'primary',
                        resource: event,
                        conferenceDataVersion: 1,
                        sendUpdates: 'all',
                    });

                    const meetLink = response.result.hangoutLink;
                    document.getElementById("meetLink").value = meetLink;
                    alert("Interview scheduled! Meet link generated.");

                } catch (error) {
                    console.error("Error creating event:", error);
                    alert("Failed to schedule event.");
                }
            }

            window.onload = () => gapiLoaded();
        </script>





</body>

</html>