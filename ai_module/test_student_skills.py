from database import get_student_skills

student_id = 1

skills = get_student_skills(student_id)

print("Student ID:", student_id)
print("Student Skills:", skills)