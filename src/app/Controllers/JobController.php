<?php

namespace App\Controllers;

use App\Models\Job;

class JobController extends Controller {
    public function listJobs(): void {
        try {
            $jobModel = new Job();
            $jobs = $jobModel->getAll();
            $this->render('home.twig', ['jobs' => $jobs]);
        } catch (\Exception $e) {
            // Log the exception and render an error page or output an error message
            error_log($e->getMessage());
            $this->render('error.twig', ['errorMessage' => 'Error fetching job listings.']);
        }
    }

    public function showJob(int $id): void {
        try {
            $jobModel = new Job();
            $job = $jobModel->findById($id);

            if ($job) {
                // Add the 'is_admin' key to the array passed to the view.
                $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
                $this->render('job_detail.twig', [
                    'job' => $job,
                    'is_admin' => $isAdmin
                ]);
            } else {
                throw new \Exception("Job not found"); // Use your custom exception or handle it accordingly
            }
        } catch (\Exception $e) {
            // Log the exception and render a not found or error page
            error_log($e->getMessage());
            $this->render('not_found.twig', ['errorMessage' => 'Job not found.']);
        }
    }

    public function applyForJob($id) {
        $jobModel = new Job(); // Initialize this outside so it's available throughout the method

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Input validation and file checks
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
                $uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads'; // Absolute path to the upload directory
                $uniqueName = uniqid('resume_', true) . '.' . $fileExt; // Create a unique file name
                $resumePath = $uploadPath . '/' . $uniqueName;

                if (move_uploaded_file($resume['tmp_name'], $resumePath)) {
                    $job = $jobModel->findById($id);
                    if ($job) {
                        // Set up the email
                        $to = $job['contact_email'];
                        $subject = "Job Application for {$job['title']}";
                        $resumeLink = "http://{$_SERVER['HTTP_HOST']}/uploads/$uniqueName"; // Adjust with actual URL
                        $message = "Applicant Name: $name\nApplicant Email: $email\nResume Link: $resumeLink";
                        $headers = "From: $email\r\nReply-To: $email\r\nX-Mailer: PHP/" . phpversion();

                        if (mail($to, $subject, $message, $headers)) {
                            $_SESSION['flash_message'] = 'Application sent successfully.';
                            $this->redirect("/jobs/$id");
                            return;
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

        // Render the form with errors or a success message
        if (!empty($errors)) {
            $this->render('job_detail.twig', [
                'job' => $jobModel->findById($id),
                'errors' => $errors
            ]);
        } else {
            $this->redirect("/jobs/$id");
        }
    }

    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }


    // Add any additional methods you may need
}
