<?php

$credentials = [
    'admin@elitesalon.com'        => 'admin123',
    'stylist@elitesalon.com'      => 'stylist123',
    'receptionist@elitesalon.com' => 'receptionist123',
    'stylist1@elitesalon.com' => 'stylist123',
    'user@elitesalon.com' => 'user123',
    
];

foreach ($credentials as $email => $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    echo "Email: $email\n";
    echo "Password: $password\n";
    echo "Hashed Password: $hash\n";
    echo "-----------------------------\n";
}
