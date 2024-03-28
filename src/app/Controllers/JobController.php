<?php

namespace App\Controllers;

use App\Models\Job;
/**
 * Handles job-related actions such as listing and showing job details.
 */
class JobController extends Controller {

    /**
     * Lists jobs available.
     *
     * This method fetches all jobs and renders them on the home page.
     * It implements basic error handling by rendering an error message
     * if the fetching process fails.
     */
    public function listJobs(): void {
        try {
            $jobModel = new Job();
            $jobs = $jobModel->getAll();

            // Check if jobs are available and pass a flag to the view
            $this->render('home.twig', [
                'jobs' => $jobs,
                'jobs_available' => !empty($jobs) // true if jobs are available, false otherwise
            ]);
        } catch (\Exception $e) {
            // Log the exception and render an error page or output an error message
            error_log($e->getMessage());
            $this->render('error.twig', ['errorMessage' => 'Error fetching job listings.']);
        }
    }
    /**
     * Shows the details of a specific job.
     *
     * @param int $id The ID of the job to display.
     */
    public function showJob(int $id): void {
        $jobModel = new Job();
        $job = $jobModel->findById($id);

        if ($job) {
            $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
            $flashMessage = '';
            if (isset($_SESSION['flash_message'])) {
                $flashMessage = $_SESSION['flash_message'];
                unset($_SESSION['flash_message']); // Clear the message after displaying
            }
            // Render the job detail page
            $this->render('job_detail.twig', [
                'job' => $job,
                'is_admin' => $isAdmin,
                'flash_message' => $flashMessage
            ]);
        } else {
            // Job not found, set HTTP status to 404 and render a user-friendly 404 page
            http_response_code(404);
            $this->render('404.twig', ['message' => 'The job you are looking for does not exist.']);
        }
    }
    /**
     * Handles the application process for a job.
     *
     * Validates user input and, if valid, sends an application email.
     * Displays a success message upon successful application.
     *
     * @param mixed $id The ID of the job being applied for.
     */
    public function applyForJob($id) {
        $jobModel = new Job();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate name and email fields
            $name = trim($_POST['name'] ?? '');
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $resume = $_FILES['resume'] ?? null;
            $errors = [];

            // Name is required
            if (empty($name)) {
                $errors['name'] = 'Your name is required.';
            }

            // Email is required and must be valid
            if (!$email) {
                $errors['email'] = 'A valid email is required.';
            }

            // Resume file handling
            if (!$resume || $resume['error'] !== UPLOAD_ERR_OK) {
                $errors['resume'] = 'An error occurred with the file upload.';
            } else if ($resume['size'] > 4000000) { // Check for file size
                $errors['resume'] = 'The resume must be less than 4MB.';
            } else {
                $allowedExtensions = ['pdf', 'doc', 'docx'];
                $fileExt = strtolower(pathinfo($resume['name'], PATHINFO_EXTENSION));
                if (!in_array($fileExt, $allowedExtensions)) {
                    $errors['resume'] = 'Only PDF, DOC, and DOCX files are allowed.';
                }
            }

            // If there are no errors, proceed with file upload and email
            if (empty($errors)) {
                $uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads';
                $uniqueName = uniqid('resume_', true) . '.' . $fileExt;
                $resumePath = $uploadPath . '/' . $uniqueName;

                if (move_uploaded_file($resume['tmp_name'], $resumePath)) {
                    $job = $jobModel->findById($id);

                    if ($job) {
                        // Set up the email
                        $to = $job['contact_email'];
                        $subject = "Job Application for {$job['title']}";
                        $resumeLink = "http://{$_SERVER['HTTP_HOST']}/uploads/$uniqueName"; // Adjust with actual URL

                        // Construct the HTML message
                        $message = "<html><body>";
                        $message .= "<p>You received an application from PHP Job Board</p>";
                        $message .= "<p>Job Title: {$job['title']}</p>";
                        $message .= "<p>Applicant Name: {$name}</p>";
                        $message .= "<p>Applicant Email: {$email}</p>";
                        $message .= "<p>Applicant Resume: <a href='{$resumeLink}'>Download Resume</a></p>";
                        $message .= "</body></html>";

                        // Headers for HTML email
                        $headers = "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                        $headers .= "From: {$email}\r\n";
                        $headers .= "Reply-To: {$email}\r\n";
                        $headers .= "X-Mailer: PHP/" . phpversion();

                        if (mail($to, $subject, $message, $headers)) {
                            $_SESSION['flash_message'] = 'Application sent successfully. We will contact you soon!';
                        } else {
                            $errors['mail'] = 'Unable to send application email.';
                        }
                    } else {
                        $errors['job'] = 'The job does not exist.';
                    }
                } else {
                    $errors['upload'] = 'Failed to move the uploaded file.';
                }
            }
        } else {
            // Redirect to job listing if not a POST request
            $this->redirect('/jobs');
            return;
        }

        // Render the form with errors or success message
        if (!empty($errors)) {
            $this->render('job_detail.twig', [
                'job' => $jobModel->findById($id),
                'errors' => $errors
            ]);
        } else {
            $this->redirect("/jobs/$id");
        }
    }

    /**
     * Redirects to a specified URL.
     *
     * @param string $url The URL to redirect to.
     */
    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }

}
