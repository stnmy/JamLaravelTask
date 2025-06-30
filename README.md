Daily Mood Tracker Web App
This is a simple web app where user can log their moods throughout the days and get visuals about how their mood logging looks like.

Features
1. User Authentication: user registration and login by custom auth logic.
2. Mood Logging (CRUD):
3. Create: Create mood(happy, sad, angry, excited) with optional notes
4. Read: View all the moods logged till date.
5. Update: Edit existing moods
6. Delete (Soft): Soft-delete mood entries, which can be restored
7. Restore: Recover soft-deleted mood entries.
8. Mood History Filtering: Filter mood entries by a specific date range.
9. Mood Streak Tracker: Displays mood logging streak badge if user logged for 3 or more days consecutively.
10. Mood of the Month: Displays the most frequently selected mood for lasst30 days.
11. Weekly Mood Summary: Shows count of each mood logged for the current week From Monday to Sunday using an bar chart from Chart.js.
12. Export Mood Log as PDF: Users can export all of their logged moods to a pdf file.

Technologies Used
1. Backend: Laravel 12.0
2. Database: MySQL
3. Frontend: HTML, Bootstrap 5, ChartJS4. PDF Generation: barryvdh/laravel-dompdf

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

    1. DB_CONNECTION=mysql
    2. DB_HOST=127.0.0.1
    3. DB_PORT=3306
    4. DB_DATABASE=jamtechnologiesassignment
    5. DB_USERNAME=root
    6. DB_PASSWORD=qwerty

11. Run database migrations:
    php artisan migrate
12. Install barryvdh/laravel-dompdf for PDF export:
    -> composer require barryvdh/laravel-dompdf
    -> php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider" --tag="config"
    
14. Start the Laravel development server:
    -> php artisan serve


User Interface:
![Login_Page](https://github.com/stnmy/JamLaravelTask/blob/34b77cc0c6c35566da860ee744537a791aac4008/DailyMood/1.png)
![Register_Page](https://github.com/stnmy/JamLaravelTask/blob/318f527989f61f5ce83c4b7c59e31d29fe149150/DailyMood/2.png)
![Home_Page](https://github.com/stnmy/JamLaravelTask/blob/318f527989f61f5ce83c4b7c59e31d29fe149150/DailyMood/3.png)
![Home2_Page](https://github.com/stnmy/JamLaravelTask/blob/318f527989f61f5ce83c4b7c59e31d29fe149150/DailyMood/4.png)
![AddMood_Page](https://github.com/stnmy/JamLaravelTask/blob/318f527989f61f5ce83c4b7c59e31d29fe149150/DailyMood/5.png)
![EditMood_Page](https://github.com/stnmy/JamLaravelTask/blob/318f527989f61f5ce83c4b7c59e31d29fe149150/DailyMood/6.png)
![Delete_Page](https://github.com/stnmy/JamLaravelTask/blob/318f527989f61f5ce83c4b7c59e31d29fe149150/DailyMood/7.png)
![Mobile_Page](https://github.com/stnmy/JamLaravelTask/blob/318f527989f61f5ce83c4b7c59e31d29fe149150/DailyMood/8.png)
![Mobile2_Page](https://github.com/stnmy/JamLaravelTask/blob/318f527989f61f5ce83c4b7c59e31d29fe149150/DailyMood/9.png)
