<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New contact form enquiry</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f6f0f5; font-family: Montserrat, Arial, sans-serif; color: #3f2d44;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
        style="background: linear-gradient(180deg, #f6f0f5 0%, #eadae6 100%); margin: 0; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                    style="max-width: 680px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 18px 48px rgba(108, 74, 114, 0.16);">
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #6c4a72 0%, #a27ba0 100%); padding: 32px 36px; color: #f9f5f8;">
                            <div
                                style="font-family: 'Cormorant Garamond', Georgia, serif; font-size: 34px; line-height: 1.1; font-weight: 700; letter-spacing: 0.02em;">
                                Ana Fae Music
                            </div>
                            <div
                                style="margin-top: 10px; font-size: 13px; line-height: 1.6; text-transform: uppercase; letter-spacing: 0.16em; opacity: 0.88;">
                                New contact form enquiry
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 32px 36px 12px;">
                            <p style="margin: 0; font-size: 16px; line-height: 1.8; color: #4b3650;">
                                A new enquiry has come in through the website contact form.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 12px 36px 0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="border-collapse: separate; border-spacing: 0 12px;">
                                <tr>
                                    <td width="170" valign="top"
                                        style="padding: 14px 16px; background-color: #f7f1f6; border-radius: 14px 0 0 14px; font-size: 12px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #6c4a72;">
                                        Name
                                    </td>
                                    <td valign="top"
                                        style="padding: 14px 16px; background-color: #fcfafc; border-radius: 0 14px 14px 0; font-size: 15px; line-height: 1.6; color: #3f2d44; border-left: 4px solid #eadae6;">
                                        {{ $submission['name'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td width="170" valign="top"
                                        style="padding: 14px 16px; background-color: #f7f1f6; border-radius: 14px 0 0 14px; font-size: 12px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #6c4a72;">
                                        Email
                                    </td>
                                    <td valign="top"
                                        style="padding: 14px 16px; background-color: #fcfafc; border-radius: 0 14px 14px 0; font-size: 15px; line-height: 1.6; color: #3f2d44; border-left: 4px solid #eadae6;">
                                        <a href="mailto:{{ $submission['email'] }}"
                                            style="color: #6c4a72; text-decoration: none; font-weight: 600;">{{ $submission['email'] }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="170" valign="top"
                                        style="padding: 14px 16px; background-color: #f7f1f6; border-radius: 14px 0 0 14px; font-size: 12px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #6c4a72;">
                                        Contact number
                                    </td>
                                    <td valign="top"
                                        style="padding: 14px 16px; background-color: #fcfafc; border-radius: 0 14px 14px 0; font-size: 15px; line-height: 1.6; color: #3f2d44; border-left: 4px solid #eadae6;">
                                        {{ $submission['contactNumber'] ?: 'Not provided' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td width="170" valign="top"
                                        style="padding: 14px 16px; background-color: #f7f1f6; border-radius: 14px 0 0 14px; font-size: 12px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #6c4a72;">
                                        Service
                                    </td>
                                    <td valign="top"
                                        style="padding: 14px 16px; background-color: #fcfafc; border-radius: 0 14px 14px 0; font-size: 15px; line-height: 1.6; color: #3f2d44; border-left: 4px solid #eadae6;">
                                        {{ $submission['service'] ?: 'General enquiry' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 24px 36px 0;">
                            <div
                                style="font-size: 12px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #6c4a72; margin-bottom: 12px;">
                                Message
                            </div>
                            <div
                                style="padding: 22px 24px; background-color: #f7f1f6; border: 1px solid #eadae6; border-radius: 18px; font-size: 15px; line-height: 1.8; color: #3f2d44; white-space: normal;">
                                {!! nl2br(e($submission['message'])) !!}
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 28px 36px 36px;">
                            <div
                                style="border-top: 1px solid #eadae6; padding-top: 18px; font-size: 13px; line-height: 1.7; color: #6c4a72;">
                                Reply directly to this email to respond to {{ $submission['name'] }}.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>