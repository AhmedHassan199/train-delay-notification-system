# Train Station — Delay Notification System

When a train is delayed, passengers still show up at the original time and crowd the platform.
This app fixes that: a trackside **sensor** streams the train's **remaining distance + speed**; the
backend computes the ETA (`time = distance ÷ speed`), decides whether the train is late, and if so
**sends an SMS to every passenger who booked that trip** with the new time — so they arrive at the
new time instead of crowding early.

Built with **Laravel 12 + Blade** (no Filament). MySQL database. SMS runs in a safe **demo mode** by
default, behind a real multi-provider **failover** gateway you can point at Twilio/Vonage later.

---

## What you need
- PHP 8.2 or newer
- Composer
- MySQL (XAMPP, WAMP, or a standalone MySQL all work)

## Install
```bash
# 1. install PHP dependencies
composer install

# 2. create your env file and app key
cp .env.example .env
php artisan key:generate

# 3. create the database, then set your DB credentials in .env
#    (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
#    example — create the database from any MySQL client:
#      CREATE DATABASE train_station;

# 4. create the tables and demo data
php artisan migrate --seed
```

## Run
Open **two** terminals:

```bash
# Terminal 1 — the web app
php artisan serve
```
```bash
# Terminal 2 — the queue worker (REQUIRED: SMS are sent in the background)
php artisan queue:work
```
Then open **http://127.0.0.1:8000**

> If port 8000 is busy, run `php artisan serve --port=9000` and open that port instead.

### Demo accounts (created by the seeder)
| Role      | Email                | Password |
|-----------|----------------------|----------|
| Admin     | admin@station.test   | password |
| Passenger | omar@example.test    | password |
| Passenger | sara@example.test    | password |

---

## Try it: make a train late
**Option 1 — from the app (easiest).** Log in as the admin, open **🛰 Sensor Simulator** (on the
dashboard / top nav), pick a trip, type a distance and speed, and press send. The backend computes
the delay and notifies the passengers. (A trip page also has a **Report delay** button for a manual
delay.)

**Option 2 — the sensor API** (what a real trackside device would call every few seconds):
```bash
curl -X POST http://127.0.0.1:8000/api/trips/1/reading \
  -H "X-Sensor-Token: dev-sensor-token-12345" \
  -H "Content-Type: application/json" \
  -d '{"distance_remaining_km":250,"current_speed_kmh":50}'
```
Response: `{ delay_minutes, computed_eta, notified, passengers_notified }`.

**Then check the result:**
- Passenger → **My Bookings** shows the old vs new time.
- Passenger → **My Messages** shows the SMS they received.
- Admin → **SMS Outbox** shows every message and which provider delivered it.

---

## How it works
```
Sensor / Admin
   → Controller (validates)
   → TripDelayService        (computes distance ÷ speed, the delay, decides who to notify)
   → NotifyTripPassengers    (one background job: streams bookings in chunks, fans out)
   → TripDelayedNotification (one queued SMS per passenger)
   → SmsChannel → SmsGateway (failover between providers) → Driver (log / Twilio / Vonage)
```

- **The backend does the math.** The sensor only reports raw distance + speed; the app computes the
  arrival time and the delay (`app/Services/TripDelayService.php`).
- **No SMS spam.** The sensor streams continuously, but passengers are only messaged when the trip
  first becomes delayed, or the delay grows past `SMS_DELAY_STEP_MINUTES`.
- **Scales to big trips.** The sensor request dispatches a single job and returns instantly; that
  job walks the confirmed bookings in **chunks** and queues one independent SMS per passenger, so
  memory and response time stay flat whether a trip has 10 or 1,000,000 passengers.
- **Sending never stops on one provider.** `SmsGateway` tries the providers in `SMS_PROVIDERS`
  order; if one fails it **fails over** to the next, a repeatedly-failing provider is skipped by a
  **circuit breaker**, and if all fail the queued job **retries** (final failures are saved in the
  SMS Outbox as `failed`).

## SMS: demo vs real
- **Demo (default):** `SMS_PROVIDERS=log` — nothing is actually sent; every message is recorded in
  the SMS Outbox so you can see exactly what each passenger would receive. No account or cost.
- **Real:** set `SMS_PROVIDERS=twilio,vonage,log` and fill the provider credentials in `.env`.

## Key settings (`.env`)
```
SENSOR_API_TOKEN=dev-sensor-token-12345   # the sensor must send this in the X-Sensor-Token header
SMS_PROVIDERS=log                         # ordered failover chain (e.g. twilio,vonage,log)
SMS_DELAY_STEP_MINUTES=10                 # re-notify passengers only when the delay grows this much
```
