from database import get_open_jobs

jobs = get_open_jobs()

print("Available Open Jobs:")

for job in jobs:
    print("-------------------------")
    print("Job ID:", job["job_id"])
    print("Job Title:", job["job_title"])
    print("Description:", job["job_description"])
    print("Eligibility:", job["eligibility"])

print("-------------------------")
print("Total Jobs Found:", len(jobs))