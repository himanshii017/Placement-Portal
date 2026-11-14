
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity


def recommend_jobs(student_skills, jobs):
    """
    Recommend jobs by comparing student skills
    with job descriptions and required skills.
    """

    if not student_skills or not jobs:
        return []

    student_profile = " ".join(student_skills)

    job_profiles = [
        job["job_title"] + " " + job["job_description"] + " " + job["eligibility"]
        for job in jobs
    ]

    documents = [student_profile] + job_profiles

    vectorizer = TfidfVectorizer()
    tfidf_matrix = vectorizer.fit_transform(documents)

    similarity_scores = cosine_similarity(
        tfidf_matrix[0:1],
        tfidf_matrix[1:]
    )[0]

    recommendations = []

    for index, job in enumerate(jobs):
        recommendations.append({
            "job_id": job["job_id"],
            "job_title": job["job_title"],
            "similarity": round(float(similarity_scores[index]) * 100, 2)
        })

    recommendations.sort(
        key=lambda item: item["similarity"],
        reverse=True
    )

    return recommendations
