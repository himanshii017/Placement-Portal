
import mysql.connector


def get_database_connection():

    connection = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="placement_portal"
    )

    return connection


def get_student_skills(student_id):

    connection = get_database_connection()
    cursor = connection.cursor(dictionary=True)

    query = """
        SELECT skills.skill_name
        FROM student_skills
        JOIN skills
        ON student_skills.skill_id = skills.skill_id
        WHERE student_skills.student_id = %s
    """

    cursor.execute(query, (student_id,))
    results = cursor.fetchall()

    cursor.close()
    connection.close()

    return [row["skill_name"] for row in results]


def get_open_jobs():

    connection = get_database_connection()
    cursor = connection.cursor(dictionary=True)

    query = """
        SELECT
            job_id,
            job_title,
            job_description,
            eligibility
        FROM jobs
        WHERE status = 'Open'
        AND (
            application_deadline IS NULL
            OR application_deadline >= CURDATE()
        )
    """

    cursor.execute(query)
    jobs = cursor.fetchall()

    cursor.close()
    connection.close()

    return jobs
