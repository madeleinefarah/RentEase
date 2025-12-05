* RentEase – Rental Property Platform

RentEase is a full-stack rental booking platform built using PHP, MySQL, HTML/CSS, and some JavaScript.
Users can browse properties, authenticate (email/password + Google Sign-In), book rentals, manage profiles, and save favorites.
The system includes a custom backend (no frameworks) and Firebase integration for Google authentication.

* Features
User Features

User registration & login

Google login via Firebase Authentication

Browse available rental properties

View property details

Book properties

Manage personal bookings

Add/remove favorites

Edit profile

Change password

Secure logout

Property Management Features

Property listing feed

Detailed property view

Booking functionality

Favorite system (toggle add/remove)

Property review support

Backend Features

Custom PHP backend (procedural + modular includes)

MySQL database integration

Secure password hashing

Firebase token validation for Google login

Organized backend routes for CRUD-style actions

Reusable UI components (header.php, navbar.php, footer.php)

* Project Structure
StayEase/
│
├── assets/
│   ├── css/
│   │   ├── login.css
│   │   ├── style.css
│   ├── img/
│   └── js/
│       ├── app.js
│       ├── google-login.js
│       └── firebase-config.js
│
├── backend/
│   ├── add_property.php
│   ├── edit_property.php
│   ├── delete_property.php
│   ├── add_review.php
│   ├── book_property.php
│   ├── google_auth.php
│   ├── toggle_favorite.php
│   ├── update_profile.php
│   └── update_password.php
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── navbar.php
│   ├── db_connect.php
│   └── auth_check.php
│
├── database/
│   └── database.sql
│
├── index.php
├── login.php
├── register.php
├── listings.php
├── property.php
├── favorites.php
├── dashboard.php
├── my_bookings.php
└── logout.php

Installation & Setup
1️- Clone the repository
git clone https://github.com/YOUR_USERNAME/RentEase.git
cd RentEase


Or place the project inside:

C:\wamp64\www\StayEase

2️- Create the MySQL database

Open phpMyAdmin

Create a database (example: stayease)

Import the SQL file:

database/database.sql

3️- Configure database connection

Edit:

includes/db_connect.php


Set your credentials:

$host = "localhost";
$user = "root";
$password = "";
$db   = "stayease";

* Authentication Setup
Email + Password Login

Uses MySQL users table

Passwords hashed using password_hash()

Handled by login.php and register.php

Google Login (Firebase)

Create a Firebase project

Enable Google Sign-In

Replace placeholders in:

assets/js/firebase-config.js


Example:

const firebaseConfig = {
    apiKey: "",
    authDomain: "",
    projectId: "",
    storageBucket: "",
    messagingSenderId: "",
    appId: ""
};


Google login triggers a backend call to:

backend/google_auth.php


which:

Validates Firebase user

Inserts user into MySQL if new

Starts PHP session

* Running the Project

Start WAMP or XAMPP, then open:

http://localhost/StayEase


You can now browse listings, log in, book properties, etc.

* Core Modules Explained
1- backend/

Contains all server-side logic (CRUD actions):

add_property.php – add a new rental property

edit_property.php – edit an existing listing

delete_property.php – remove property

toggle_favorite.php – add/remove favorites

book_property.php – booking creation

google_auth.php – Google login backend

update_profile.php – profile editing

update_password.php – password update

1- includes/

Reusable components:

header.php – <head> + global assets

navbar.php – site navigation

footer.php – closing layout

auth_check.php – session validation

db_connect.php – database connection

1- Security

Password hashing using password_hash()

Prepared statements to prevent SQL injection

Session-based authentication after login

Auth-required pages use auth_check.php

Firebase ID token verification ensures Google users are real

1- Additional Notes

Images can be replaced or extended with a full upload system

You can expand features (booking calendars, map search, host dashboard)

Works entirely with plain PHP + JavaScript (no frameworks needed)

Firebase config must be set for Google login to work

