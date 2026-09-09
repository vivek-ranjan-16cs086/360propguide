<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>New Lead Submission</title>
    <style>
        /* Global Styles */
        body,
        table,
        td {
            font-family: Arial, sans-serif;
            font-size: 16px;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }

        /* Outer Background */
        body {
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }

        /* Outer Wrapper */
        .email-container {
            max-width: 600px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .image {
            width: 100%;
            max-width: 600px;
        }

        img {
            display: block;
            margin: auto;
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            .email-container {
                background-color: #000000 !important;
                color: #ffffff !important;
            }

            body {
                background-color: #222222 !important;
            }
        }

        /* Table Styles */
        .lead-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .lead-table th,
        .lead-table td {
            border: 1px solid #dddddd;
            padding: 10px;
            text-align: left;
        }

        .lead-table th {
            background-color: #1b3f63;
            color: white;
        }

        /* Button */
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    @if(@$mailData['reciever'] !== 'admin')
        <img src="{{url('frontend/360-2.jpg')}}" alt="360propguide" width="100%" height="150px" srcset="">
        <p>
        <h3>Hello <span class="name">{{$mailData['name']}}</span></h3>
        We appreciate the contact with you. The inquiry submission process has been successfully submitted, and we value your interest in our company. A devoted team at our company delivers superior service to clients and will respond as soon as possible.

        </p>
        <p>
            Best regards,
            <br>
            360propguide
        </p>
    @else
        <section>
            <img src="{{url('frontend/360logo.png')}}" width="150"><br>
            <hr><br>
            <img src="{{url('frontend/360-2.jpg')}}" class="image">
        </section>
        <!-- Email Container -->
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td align="center">

                    <table role="presentation" class="email-container">
                        <tr>
                            <td align="left">

                                <h4>New Lead Submission from 360PropGuide</h4>

                                <!-- Lead Details Table -->
                                <table class="lead-table">
                                    <tr>
                                        <th>Field</th>
                                        <th>Information</th>
                                    </tr>
                                    <tr>
                                        <td>👤 <strong>Name</strong></td>
                                        <td>{{ $mailData['name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>📧 <strong>Email</strong></td>
                                        <td>{{ $mailData['email'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>📞 <strong>Phone</strong></td>
                                        <td><a href="tel:{{ $mailData['mobile'] }}">{{ $mailData['mobile'] }}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>📝 <strong>Message</strong></td>
                                        <td> {{ $mailData['message'] }}</td>
                                    </tr>
                                </table>

                                <p>📌 <strong>Action Required:</strong> Please follow up with this lead promptly.
                                </p>
                                <p>💼 <strong>Submitted via:</strong> <a
                                        href="{{$mailData['submittedVia']}}">{{$mailData['submittedVia']}}</a></p>

                                <hr>

                                <p>Best regards, <br>
                                    <strong>360 propguide LLP</strong>
                                </p>

                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>

    @endif

</body>

</html>
