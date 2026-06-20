<x-mail::message>
# Subscription Expiring Soon

Your **{{ $planName }}** subscription is expiring in **{{ $daysLeft }} days** ({{ $expiryDate }}).

## What happens next?

- If you have auto-renewal enabled, we'll automatically charge your saved payment method
- If auto-renewal is disabled, your access will expire on {{ $expiryDate }}
- You can renew manually at any time from your subscription settings

<x-mail::button :url="route('subscriptions.index')">
Manage Subscription
</x-mail::button>

Need help? Contact our support team.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>