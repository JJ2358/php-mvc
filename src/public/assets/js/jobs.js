// jobs.js
function fetchApiJobs() {
  fetch('http://p2api.ryanmclaren.ca/api/job-postings')
      .then(response => response.json())
      .then(data => displayApiJobs(data))
      .catch(error => console.error('Error fetching jobs:', error));
}

function displayApiJobs(data) {
  const jobs = data.data; // Access the 'data' key from the response
  const apiJobsContainer = document.getElementById('api-jobs');
  apiJobsContainer.innerHTML = ''; // Clear existing content

  jobs.forEach(job => {
      const jobElement = document.createElement('div');
      jobElement.classList.add('job-item', 'mb-6');
      jobElement.innerHTML = `
        <h2 class="text-xl font-semibold">${job.title}</h2>
        <p class="text-gray-600">Location: ${job.location}</p>
        <p class="text-gray-600">Start Date: ${job.start_date}</p>
        <p class="text-gray-600">${job.description.substring(0, 150)}...</p>
        <a href="/jobs/${job.id}" class="text-blue-500 hover:underline">View</a>
      `;
      apiJobsContainer.appendChild(jobElement);
  });
}

document.addEventListener('DOMContentLoaded', fetchApiJobs);
