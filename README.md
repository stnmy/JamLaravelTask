Daily Mood Tracker Web App
This is a simple web app where user can log their moods throughout the days and get visuals about how their mood logging looks like.

Features
• User Authentication: user registration and login by custom auth logic.
• Mood Logging (CRUD):
o Create: Create mood(happy, sad, angry, excited) with optional notes
o Read: View all the moods logged till date.
o Update: Edit existing moods
o Delete (Soft): Soft-delete mood entries, which can be restored
o Restore: Recover soft-deleted mood entries.
• Mood History Filtering: Filter mood entries by a specific date range.
• Mood Streak Tracker: Displays mood logging streak badge if user logged for 3 or more days consecutively.
• Mood of the Month: Displays the most frequently selected mood for lasst30 days.
• Weekly Mood Summary: Shows count of each mood logged for the current week From Monday to Sunday using an bar chart from Chart.js.
• Export Mood Log as PDF: Users can export all of their logged moods to a pdf file.

Technologies Used
• Backend: Laravel 12.0
• Database: MySQL
• Frontend: HTML, Bootstrap 5, ChartJS
• PDF Generation: barryvdh/laravel-dompdf

Setup Instructions

1. Clone repository
2. git clone
3. cd DailyMood
4. Install Composer dependencies:
5. composer install
6. Copy the environment file:
7. cp .env.example .env
8. Generate an application key:
9. php artisan key:generate

10. Configure your database:
    Create jamtechnologiesassignment database in mysql workbench first.
    Open the .env file and update the database credentials:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jamtechnologiesassignment
DB_USERNAME=root
DB_PASSWORD=qwerty

11. Run database migrations:
    php artisan migrate
12. Install barryvdh/laravel-dompdf for PDF export:
    composer require barryvdh/laravel-dompdf
    php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider" --tag="config"
    
14. Start the Laravel development server:
    php artisan serve


User Interface:
![Login_Page](https://github.com/stnmy/JamLaravelTask/blob/34b77cc0c6c35566da860ee744537a791aac4008/DailyMood/1.png)
