document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".bookmark-icon").forEach(icon => {
        icon.addEventListener("click", function () {
            let jobId = this.getAttribute("data-job-id");

            if (!jobId) {
                console.error("Error: job_id is missing"); // Debugging output
                return;
            }

            let iconElement = this;

            fetch("bookmark.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "job_id=" + encodeURIComponent(jobId)
            })
            .then(response => response.json())
            .then(data => {
                console.log("Server response:", data); // Debugging output
                if (data.status === "added") {
                    iconElement.setAttribute("name", "bookmark");
                } else if (data.status === "removed") {
                    iconElement.setAttribute("name", "bookmark-outline");
                }
            })
            .catch(error => console.error("Fetch error:", error));
        });
    });
});

