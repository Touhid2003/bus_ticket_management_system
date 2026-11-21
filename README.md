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
- **Payments:** bKash sandbox checkout is available for testing; local demo payment is also available.
- **Out of scope:** production payment processing, mobile apps, GPS tracking.

## Technology

HTML5, CSS3, JavaScript, Bootstrap · PHP · MySQL · Apache (XAMPP) · PHP cURL · three-tier architecture

## bKash Sandbox Testing

Get sandbox credentials from the bKash developer portal and configure these environment variables for the Apache/PHP process. Do not commit credentials to the repository.

- `BKASH_SANDBOX_ENABLED=true`
- `BKASH_SANDBOX_APP_KEY`
- `BKASH_SANDBOX_APP_SECRET`
- `BKASH_SANDBOX_USERNAME`
- `BKASH_SANDBOX_PASSWORD`
- `BKASH_SANDBOX_CALLBACK_URL` — the base URL of `users/payment.php`, without query parameters (for example, `http://localhost/bus_ticket_management_system/users/payment.php` for a local XAMPP project)
- `BKASH_SANDBOX_BASE_URL` — optional; defaults to `https://tokenized.sandbox.bka.sh/v1.2.0-beta`

Enable the PHP cURL extension. Once configured, select **Pay with bKash Sandbox** on a pending booking; the customer completes the sandbox checkout and is returned to the site for server-side verification. This does not process a real payment. The separate local demo button only simulates a payment and does not contact bKash.

## Database

`users` · `buses` · `routes` · `schedules` · `seats` · `bookings` · `booking_seats` · `payments`

## Method and Timeline

Waterfall model, 15 days: analysis (1–2) → design (3–4) → user and admin modules (5–8) → booking, seat and payment modules (9–11) → testing (12–13) → documentation and deployment (14–15).

## Future Work

Payment gateways, SMS and email notifications, Android and iOS apps, GPS tracking, QR-code ticket checking, coupons and ratings.

## Author

**MD. Touhidur Islam** · IsDB-BISEW  