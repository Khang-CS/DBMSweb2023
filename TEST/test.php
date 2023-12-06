<?php

$state = 'All';
if(isset)
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}Default Title{% endblock %}</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Additional meta tags, styles, scripts, etc. -->
</head>

<body>
    <header>
        <h1>{% block header %}Header{% endblock %}</h1>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
                <!-- Add more menu items as needed -->
            </ul>
        </nav>
    </header>

    <main>
        {% block content %}{% endblock %}
    </main>

    <footer>
        <p>&copy; 2023 Your Company</p>
    </footer>
</body>

</html>