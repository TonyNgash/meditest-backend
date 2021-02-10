<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Welcome To MEDITEST Diagnostics Services</h1>
    <p>We are pleased to inform you that you have created an account from the mobile application.</p>
    <br>
    <br>
    <br>
    <h3>Your Details Are As Follows</h3>
    <ul>
        <li>First Name: {{ $details['first_name'] }}</li>
        <li>Sirname: {{ $details['sirname'] }}</li>
        <li>Last Name: {{ $details['last_name'] }}</li>
        <li>Gender: {{ $details['gender'] }}</li>
        <li>Email: {{ $details['email'] }}</li>
        <li>Phone: {{ $details['phone'] }}</li>
        <li>Address: {{ $details['address'] }}</li>
        <li>Date Of Birth: {{ $details['dob'] }}</li>
    </ul>
    <br>
    <br>
    <br>
    <h3>Please Verify Your Account By Clicking Link Below</h3>

</body>
</html>
