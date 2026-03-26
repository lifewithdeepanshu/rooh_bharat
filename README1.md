# Rooh Bharat

Rooh Bharat is a civic action social media web application. It transitions traditional social media networking into civic engagement by allowing citizens to report local issues, upvote them for better visibility, and collaborate with government officials and youth volunteers to resolve them.

## Features

*   **Role-Based Access Control**: Different dashboards tailored for Citizens, Government Officials, and Youth/Volunteers.
*   **Aadhaar-Mocked Authentication**: Simple and unified registration and login portal supporting mocked Aadhaar verification via mobile number.
*   **Citizen Feed & Issue Reporting**: 
    *   Citizens can view a feed of local issues sorted dynamically by community upvotes and recency.
    *   Citizens can report new issues, attaching photographic proof and their exact GPS location.
    *   AJAX-powered upvoting system to escalate critical issues seamlessly.
*   **Government Official Dashboard**: A centralized hub for authorities to view all open issues in their jurisdiction and update their resolution status (Open, In Progress, Resolved).
*   **Youth Civic Gigs Board**: A gamified platform where youth can undertake civic micro-tasks (like surveying or volunteering) to earn points and monetary rewards.
*   **Gamification**: Real-time points tracking and badge assignments (e.g., "Civic Champion") to reward active community participation.
*   **Modern Aesthetics**: Built with Bootstrap 5 and custom CSS for a premium, responsive, and accessible user experience.

## Tech Stack

*   **Frontend**: HTML5, CSS3, JavaScript (Vanilla JS & AJAX), Bootstrap 5
*   **Backend**: PHP
*   **Database**: MySQL

## Prerequisites

To run this project locally, you will need a local server environment capable of running PHP and MySQL, such as:
*   [XAMPP](https://www.apachefriends.org/index.html) (Recommended for Windows)
*   WAMP, MAMP, or a similar LAMP stack.

## Installation & Setup

1.  **Clone or Move the Project**
    Place the `rooh_bharat` project folder into your local server's public document root directory. 
    *   If using XAMPP on Windows, move it to: `C:\xampp\htdocs\rooh_bharat`

2.  **Start Local Server Environment**
    Open your XAMPP Control Panel and start the following modules:
    *   **Apache** (Web Server)
    *   **MySQL** (Database Server)

3.  **Database Setup**
    *   Open your web browser and navigate to `http://localhost/phpmyadmin/`
    *   Go to the **Import** tab.
    *   Choose the file `database.sql` located in the root of the `rooh_bharat` directory and click **Import** (or **Go**).
    *   *Alternatively*, if you prefer configuring via CLI, you can run: `mysql -u root < C:\xampp\htdocs\rooh_bharat\database.sql`
    *   **Mock Data (Optional)**: Open `http://localhost/rooh_bharat/mock_data.php` in your browser to quickly pre-populate the database with example youth gigs.

4.  **Configuration**
    If your MySQL installation uses a different username or password (default is `root` with no password), update the `config/db.php` file:
    ```php
    $username = "your_username";
    $password = "your_password";
    ```

## Usage Guide

1.  **Access the Application**
    Open your web browser and navigate to: [`http://localhost/rooh_bharat/`](http://localhost/rooh_bharat/)

2.  **Creating Accounts (Mock Authentication)**
    The landing page handles both Login and Registration. You manage role assignments during registration.
    *   **To Test as a Citizen**: Switch to the *Register* tab. Enter a name, a 10-digit mobile number, a password, and select "Citizen" as the role. Click **Register**.
    *   **To Test as an Official**: Register a new account using a different mobile number and select "Government Official" as the role.
    *   **To Test as a Youth**: Register a new account and select "Youth / Volunteer" as the role.

3.  **Using the Citizen Dashboard**
    *   Upon logging in as a Citizen, you will see the **Local Civic Feed**.
    *   Click **New Issue** to report a problem in your area.
    *   Fill out the title and description, choose a photo to upload, and click **Attach My Location** (ensure your browser allows location access when prompted). Finally, click **Post Issue**.
    *   Your new issue will automatically appear on the feed. Click the **Upvote** arrow on any issue to increase its priority. 
    *   Notice your points and civic badge in the top right.

4.  **Using the Official Dashboard**
    *   Log out and log back in using the credentials of the "Government Official" account you created.
    *   You will automatically be redirected to the **Issues Hub**.
    *   Here you will see a tabular list of all reported issues.
    *   Use the dropdown menu under the **Action** column to update an issue's status from *Open* to *In Progress* or *Resolved*. Click **Update**. 
    *   If you log back in as a Citizen, you will see the status badge on the issue has been updated in real-time.

5.  **Using the Youth Dashboard**
    *   Log out and log back in using the "Youth / Volunteer" account.
    *   You will see the **Active Civic Gigs** board.
    *   Browse the available gigs and click **Apply Now** to volunteer for a task.
    *   Your total points earned through civic engagement are tracked securely at the top of the dashboard.
