<?php
include 'database.php'; // Include your database connection

$search_query = $_GET['search_query'] ?? ''; // Get search query from the URL

if ($search_query) {
    // Prepare a query to fetch jobs that match the search
    $sql = "SELECT j.*, jt.job_type_name, e.company_name, e.company_logo, e.company_address
            FROM tbl_job_post j
            LEFT JOIN tbl_employer_profile e ON j.employer_id = e.employer_id
            LEFT JOIN tbl_job_type jt ON j.job_type_id = jt.job_type_id
            WHERE j.job_title LIKE ? OR e.company_name LIKE ? 
            ORDER BY j.posted_at DESC";

    $stmt = $conn->prepare($sql);
    $search_param = "%{$search_query}%"; // Add wildcards for partial matching
    $stmt->bind_param("ss", $search_param, $search_param); // Bind the search query twice (for job_title and company_name)
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Default query if there's no search query
    $sql = "SELECT j.*, jt.job_type_name, e.company_name, e.company_logo, e.company_address
            FROM tbl_job_post j
            LEFT JOIN tbl_employer_profile e ON j.employer_id = e.employer_id
            LEFT JOIN tbl_job_type jt ON j.job_type_id = jt.job_type_id
            ORDER BY j.posted_at DESC";

    $result = mysqli_query($conn, $sql);
}

?>