# =============================================================
# AquaGold Hotspot — Walled Garden Setup
# Run this once on the MikroTik router (New Terminal in Winbox,
# or paste into /system script).
#
# WHAT THIS DOES:
# - Any device that joins the WiFi and hasn't paid gets NOTHING
#   except access to your Laravel server (the payment/login page).
# - Only once a voucher is issued (by Laravel, via the API, after
#   payment) can that specific hotspot user reach the real internet.
# =============================================================

# --- 1. Set your Laravel server's LAN IP here ---
:local laravelIp "192.168.88.10"      ;# <-- change to your server's IP
:local laravelPort "80"

# --- 2. Default hotspot user profile: NO real internet ---
# Every device is "connected to WiFi" but this profile has such a
# low shared-users/rate-limit and (critically) the firewall rules
# below block it from anything except the walled garden.
/ip hotspot user profile
add name="default-unpaid" shared-users=1 rate-limit="64k/64k"

# --- 3. Walled garden: allow ONLY the payment/login server ---
# Anything not explicitly allowed here is blocked for unauthenticated
# hotspot clients — this is the actual enforcement mechanism.
/ip hotspot walled-garden
add dst-host=$laravelIp action=allow comment="AquaGold payment/login server"
add dst-host="*.laravel-cloud-domain.com" action=allow comment="if hosted remotely, allow this domain instead/also"

# If using a real mobile-money gateway, you must also whitelist ITS
# domain here, or the payment prompt on the customer's phone (which
# usually happens over their own mobile data, not this WiFi, so this
# may not even be needed — mobile money apps typically use cellular
# data, not the WiFi being purchased).

# --- 4. Make sure the hotspot server points new users at this profile ---
/ip hotspot
set [find] profile=default-unpaid

# --- 5. Firewall safety net (belt-and-braces) ---
# Even if something is misconfigured in the walled garden, this rule
# in the hotspot chain drops anything from unauthenticated addresses
# that isn't going to the Laravel server.
/ip firewall filter
add chain=forward action=drop connection-state=new \
    src-address-list=hotspot-unauth \
    dst-address=!$laravelIp \
    comment="AquaGold: block unpaid devices from real internet"

# =============================================================
# WHAT LARAVEL DOES FROM HERE (via MikrotikService, RouterOS API):
# - On payment success: /ip/hotspot/user/add  (this file's job ends
#   here — everything after is done automatically by the app)
# - On revoke/expiry:   /ip/hotspot/user/remove
# - To disconnect now:  /ip/hotspot/active/remove
# =============================================================
