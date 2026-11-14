
from flask import Flask, request, jsonify
from recommender import recommend_jobs
from database import get_student_skills, get_open_jobs

app = Flask(__name__)


@app.route("/")
def home():
    return jsonify({
        "message": "CareerConnect AI Job Recommendation API is running!"
    })


# Existing API: accepts skills and jobs manually
@app.route("/recommend", methods=["POST"])
def recommend():

    data = request.get_json()

    if not data:
        return jsonify({"error": "JSON data is required"}), 400

    student_skills = data.get("student_skills", [])
    jobs = data.get("jobs", [])

    if not isinstance(student_skills, list) or not isinstance(jobs, list):
        return jsonify({"error": "Skills and jobs must be lists"}), 400

    required_fields = [
        "job_id",
        "job_title",
        "job_description",
        "eligibility"
    ]

    for job in jobs:
        if not isinstance(job, dict) or not all(
            field in job for field in required_fields
        ):
            return jsonify({
                "error": "Each job must contain all required fields"
            }), 400

    try:
        recommendations = recommend_jobs(student_skills, jobs)

        return jsonify({
            "success": True,
            "recommendations": recommendations
        })

    except ValueError as error:
        return jsonify({"error": str(error)}), 400


# New API: fetches data directly from MySQL
@app.route("/recommend-from-database", methods=["POST"])
def recommend_from_database():

    data = request.get_json()

    if not data or "student_id" not in data:
        return jsonify({
            "error": "Student ID is required"
        }), 400

    try:
        student_id = int(data["student_id"])

        if student_id <= 0:
            return jsonify({
                "error": "Invalid student ID"
            }), 400

        student_skills = get_student_skills(student_id)
        jobs = get_open_jobs()

        if not student_skills:
            return jsonify({
                "error": "No skills found for this student"
            }), 404

        if not jobs:
            return jsonify({
                "error": "No open jobs found"
            }), 404

        recommendations = recommend_jobs(student_skills, jobs)

        return jsonify({
            "success": True,
            "student_id": student_id,
            "student_skills": student_skills,
            "total_jobs": len(jobs),
            "recommendations": recommendations
        })

    except ValueError:
        return jsonify({
            "error": "Student ID must be a valid number"
        }), 400

    except Exception as error:
        return jsonify({
            "error": str(error)
        }), 500


if __name__ == "__main__":
    app.run(debug=True, port=5000)
