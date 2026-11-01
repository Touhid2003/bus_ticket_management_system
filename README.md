# 🚌 Online Bus Ticket Management System

A web-based application built with **PHP** and **MySQL** where passengers search buses, check live seat availability and book tickets online, and admins manage buses, routes, schedules and reports.

## Summary

Manual ticket counters cause long queues, double bookings and lost paper records. This system moves the process online so booking is fast, accurate and available 24/7, and operators get automatic records and reports.

## Main Points

- **Users:** Passenger, Admin and optional Counter Staff, each with their own permissions.
- **Passenger features:** register and log in, search buses by route and date, choose seats from a visual layout, book and cancel tickets, view booking history, print the ticket.
- **Admin features:** manage buses, routes, schedules, fares, users and bookings; dashboard with bookings, income and active buses; daily, weekly and monthly reports.
- **No double booking:** a seat is locked as soon as it is booked.
- **Security:** password hashing, prepared statements, input validation and output escaping, role-based access control.
- **Out of scope:** real payment gateway (simulated payment record is used), mobile apps, GPS tracking.

## Technology

HTML5, CSS3, JavaScript, Bootstrap · PHP · MySQL · Apache (XAMPP) · three-tier architecture

## Database

`users` · `buses` · `routes` · `schedules` · `seats` · `bookings` · `booking_seats` · `payments`

## Method and Timeline

Waterfall model, 15 days: analysis (1–2) → design (3–4) → user and admin modules (5–8) → booking, seat and payment modules (9–11) → testing (12–13) → documentation and deployment (14–15).

## Future Work

Payment gateways, SMS and email notifications, Android and iOS apps, GPS tracking, QR-code ticket checking, coupons and ratings.

## Author

**MD. Touhidur Islam** · IsDB-BISEW  