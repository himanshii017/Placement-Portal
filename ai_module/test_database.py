
from database import get_database_connection

try:
    connection = get_database_connection()

    if connection.is_connected():
        print("Python connected to MySQL successfully!")

    connection.close()

except Exception as error:
    print("Database connection failed:", error)
