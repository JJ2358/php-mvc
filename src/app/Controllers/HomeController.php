<?php

namespace App\Controllers;

use App\Models\Job;

class HomeController extends Controller
{
    public function index(): void {
        $jobModel = new Job();
        $jobs = $jobModel->getAll(); // Ensure this method is implemented in Job model
        $this->render('home.twig', ['jobs' => $jobs]);
    }
}
