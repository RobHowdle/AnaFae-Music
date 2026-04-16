<h1>New contact form enquiry</h1>

<p><strong>Name:</strong> {{ $submission['name'] }}</p>
<p><strong>Email:</strong> {{ $submission['email'] }}</p>
<p><strong>Contact number:</strong> {{ $submission['contactNumber'] ?: 'Not provided' }}</p>
<p><strong>Service:</strong> {{ $submission['service'] ?: 'General enquiry' }}</p>

<h2>Message</h2>

<p>{!! nl2br(e($submission['message'])) !!}</p>