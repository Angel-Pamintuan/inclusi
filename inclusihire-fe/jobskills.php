<?php
session_start();
$accessibility = isset($_SESSION['accessibility']) ? $_SESSION['accessibility'] : null;

include 'database.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['status'] = "Please complete registration first.";
    header("Location: applicantregister.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job_type_id = $_POST['job_type'];
    $skills = isset($_POST['skills']) ? $_POST['skills'] : [];

    // Save job type
    $update_query = "UPDATE tbl_user SET job_type_id = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ii", $job_type_id, $user_id);
    $stmt->execute();

    // Clear old skills to avoid duplicates
    $conn->query("DELETE FROM tbl_skill WHERE user_id = $user_id");

    // Save selected skills
    $insert_skill = $conn->prepare("INSERT INTO tbl_skill (user_id, job_skills_id) VALUES (?, ?)");
    foreach ($skills as $skill_id) {
        $insert_skill->bind_param("ii", $user_id, $skill_id);
        $insert_skill->execute();
    }

    $_SESSION['status'] = "Registration complete!";
    header("Location: home.php");
    exit();
}

// Fetch job skills
$skills = [];
$sql = "SELECT job_skills_id, job_skills_name FROM tbl_job_skills";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $skills[] = $row;
}

// Fetch job types
$jobTypes = [];
$sql = "SELECT job_type_id, job_type_name FROM tbl_job_type";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $jobTypes[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessibility</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .title {
            width: 362px;
            padding-left: 30px;
            font-weight: 600;
            font-size: 24px;
            line-height: 140%;
            letter-spacing: -0.015em;
            color: #0D0D26;
            margin: 40px auto 10px;
        }

        .see-all {
            cursor: pointer;
            color: #95969D;
            font-size: 13px;
            text-align: right;
            margin-right: 30px;
        }

        .skills-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 0 30px 20px 30px;
            max-height: 100px;
            overflow: hidden;
            transition: max-height 0.5s ease;
        }

        .skills {
            box-sizing: border-box;
            display: inline-flex;
            align-items: center;
            padding: 8px 20px;
            gap: 8px;
            font-size: 14px;
            border: 1px solid #95969D;
            border-radius: 97px;
            color: #0D0D26;
            white-space: nowrap;
            background-color: white;
            cursor: pointer;
        }

        .expand {
            max-height: 500px;
        }

        .save-button {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 16px 48px;
            background: #1E4461;
            color: white;
            border: none;
            border-radius: 5px;
            width: 100%;
            max-width: 327px;
            margin: 20px auto;
        }

        .skills.selected {
            background-color: #1E4461;
            color: white;
        }
    </style>
</head>

<body>

    <form method="POST" action="jobskills.php">
        <div class="container">

            <div class="title">Select Job Roles</div>
            <div class="see-all" onclick="toggleSkills('jobRoles')">See all</div>
            <div class="skills-container" id="jobRoles">
                <?php foreach ($skills as $skill): ?>
                    <div class="skills" onclick="toggleSkill(this, <?= $skill['job_skills_id'] ?>)">
                        <?= htmlspecialchars($skill['job_skills_name']) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="title">Select Job Type</div>
            <div class="skills-container" id="jobTypes">
                <?php foreach ($jobTypes as $jobType): ?>
                    <div class="skills job-type" onclick="selectJobType(this, <?= $jobType['job_type_id'] ?>)">
                        <?= htmlspecialchars($jobType['job_type_name']) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <input type="hidden" name="job_type" id="selectedJobType" required>
            <div id="selectedSkillsContainer"></div>

            <button type="submit" class="save-button">Save</button>
        </div>
    </form>

    <?php
    include 'access.php';  // Include your accessibility logic if needed
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let selectedSkills = new Set();

        function toggleSkills(sectionId) {
            const container = document.getElementById(sectionId);
            container.classList.toggle('expand');
        }

        function toggleSkill(element, skillId) {
            if (selectedSkills.has(skillId)) {
                selectedSkills.delete(skillId);
                element.classList.remove('selected');
            } else {
                selectedSkills.add(skillId);
                element.classList.add('selected');
            }
            updateSkillInputs();
        }

        function updateSkillInputs() {
            const container = document.getElementById('selectedSkillsContainer');
            container.innerHTML = '';  // Clear existing inputs
            selectedSkills.forEach(skillId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'skills[]';
                input.value = skillId;
                container.appendChild(input);
            });
        }

        function selectJobType(element, jobTypeId) {
            document.querySelectorAll('.job-type').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById('selectedJobType').value = jobTypeId;
        }

        // OPTIONAL: Only attach speech button if the button exists (fix error)
        const readButton = document.getElementById("readButton");
        if (readButton) {
            readButton.addEventListener("click", function () {
                speechSynthesis.cancel();
                const text = document.body.innerText;
                const speech = new SpeechSynthesisUtterance(text);
                speech.lang = "en-US";
                speech.rate = 1;
                speechSynthesis.speak(speech);
            });
        }
    </script>

</body>

</html>