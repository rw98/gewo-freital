<?php

return [
    'auth' => [
        'login' => [
            'title' => 'Anmelden',
            'heading' => 'Willkommen zurück',
            'description' => 'Geben Sie Ihre Zugangsdaten ein, um sich anzumelden',
            'email' => 'E-Mail-Adresse',
            'email_placeholder' => 'ihre@email.de',
            'password' => 'Passwort',
            'password_placeholder' => 'Passwort eingeben',
            'forgot_password' => 'Passwort vergessen?',
            'remember_me' => '30 Tage angemeldet bleiben',
            'submit' => 'Anmelden',
            'no_account' => 'Noch kein Konto?',
            'sign_up' => 'Kostenlos registrieren',
            'security_note' => 'Sichere Anmeldung durch Enterprise-Verschlüsselung geschützt',
        ],

        'register' => [
            'title' => 'Registrieren',
            'heading' => 'Konto erstellen',
            'description' => 'Registrieren Sie sich, um Ihre Mietobjekte zu verwalten',
            'name' => 'Vollständiger Name',
            'name_placeholder' => 'Ihren Namen eingeben',
            'email' => 'E-Mail-Adresse',
            'email_placeholder' => 'ihre@email.de',
            'password' => 'Passwort',
            'password_placeholder' => 'Sicheres Passwort erstellen',
            'password_confirm' => 'Passwort bestätigen',
            'password_confirm_placeholder' => 'Passwort wiederholen',
            'submit' => 'Konto erstellen',
            'has_account' => 'Bereits ein Konto?',
            'sign_in' => 'Anmelden',
            'terms_note' => 'Mit der Registrierung akzeptieren Sie unsere Nutzungsbedingungen und Datenschutzrichtlinie',
        ],

        'forgot_password' => [
            'title' => 'Passwort vergessen',
            'heading' => 'Passwort vergessen?',
            'description' => 'Kein Problem, wir senden Ihnen eine Anleitung zum Zurücksetzen.',
            'email' => 'E-Mail-Adresse',
            'email_placeholder' => 'ihre@email.de',
            'submit' => 'Link senden',
            'back_to_login' => 'Zurück zur Anmeldung',
        ],

        'reset_password' => [
            'title' => 'Passwort zurücksetzen',
            'heading' => 'Neues Passwort festlegen',
            'description' => 'Ihr neues Passwort muss sich von zuvor verwendeten Passwörtern unterscheiden.',
            'email' => 'E-Mail-Adresse',
            'password' => 'Neues Passwort',
            'password_placeholder' => 'Neues Passwort eingeben',
            'password_confirm' => 'Neues Passwort bestätigen',
            'password_confirm_placeholder' => 'Neues Passwort wiederholen',
            'submit' => 'Passwort zurücksetzen',
            'back_to_login' => 'Zurück zur Anmeldung',
        ],

        'verify_email' => [
            'title' => 'E-Mail-Verifizierung',
            'heading' => 'E-Mail prüfen',
            'description' => 'Wir haben einen Verifizierungslink an Ihre E-Mail-Adresse gesendet. Bitte klicken Sie auf den Link, um Ihr Konto zu bestätigen.',
            'resent' => 'Ein neuer Verifizierungslink wurde an Ihre E-Mail-Adresse gesendet.',
            'resend' => 'Verifizierungs-E-Mail erneut senden',
            'logout' => 'Abmelden',
            'help_note' => 'Keine E-Mail erhalten? Prüfen Sie Ihren Spam-Ordner oder senden Sie die E-Mail erneut.',
        ],

        'confirm_password' => [
            'title' => 'Passwort bestätigen',
            'heading' => 'Passwort bestätigen',
            'description' => 'Dies ist ein geschützter Bereich. Bitte bestätigen Sie Ihr Passwort, um fortzufahren.',
            'password' => 'Passwort',
            'password_placeholder' => 'Ihr Passwort eingeben',
            'submit' => 'Bestätigen',
        ],

        'two_factor' => [
            'title' => 'Zwei-Faktor-Authentifizierung',
            'code_heading' => 'Authentifizierungscode',
            'code_description' => 'Geben Sie den 6-stelligen Code aus Ihrer Authenticator-App ein.',
            'recovery_heading' => 'Wiederherstellungscode',
            'recovery_description' => 'Geben Sie einen Ihrer Notfall-Wiederherstellungscodes ein.',
            'recovery_label' => 'Wiederherstellungscode',
            'submit' => 'Verifizieren',
            'use_recovery' => 'Stattdessen Wiederherstellungscode verwenden',
            'use_code' => 'Stattdessen Authentifizierungscode verwenden',
            'help_note' => 'Keinen Zugriff auf Ihre Authenticator-App? Kontaktieren Sie den Support.',
        ],

        'common' => [
            'privacy' => 'Datenschutz',
            'terms' => 'AGB',
            'contact' => 'Kontakt',
        ],
    ],

    'layout' => [
        'company_name' => 'FREITALER WOHNUNGSGENOSSENSCHAFT eG',
        'menu_open' => 'Menü öffnen',

        'auth' => [
            'dashboard' => 'Mein Bereich',
            'login' => 'Anmelden',
            'register' => 'Registrieren',
        ],

        'footer' => [
            'company' => [
                'name' => 'Freitaler Wohnungsgenossenschaft eG',
                'address' => "Dresdner Straße 84\n01705 Freital",
            ],
            'nav' => [
                'title' => 'Navigation',
                'links' => [
                    'search' => 'Wohnungssuche',
                    'service' => 'Service',
                    'about' => 'Über uns',
                    'contact' => 'Kontakt',
                ],
            ],
            'service' => [
                'title' => 'Service',
                'links' => [
                    'portal' => 'Mieterportal',
                    'damage' => 'Schadensmeldung',
                    'forms' => 'Formulare',
                    'faq' => 'FAQ',
                ],
            ],
            'legal' => [
                'title' => 'Rechtliches',
                'links' => [
                    'imprint' => 'Impressum',
                    'privacy' => 'Datenschutz',
                    'terms' => 'AGB',
                    'accessibility' => 'Barrierefreiheit',
                ],
            ],
            'copyright' => '© :year Freitaler Wohnungsgenossenschaft eG. Alle Rechte vorbehalten.',
            'font_size' => 'Schriftgröße:',
            'font_size_small' => 'Kleine Schriftgröße',
            'font_size_normal' => 'Normale Schriftgröße',
            'font_size_large' => 'Große Schriftgröße',
        ],
    ],

    'landing' => [
        'meta' => [
            'title' => 'Willkommen Zuhause - GEWO Freital',
        ],

        'header' => [
            'company_name' => 'FREITALER WOHNUNGSGENOSSENSCHAFT eG',
            'nav' => [
                'rent' => 'Mieten',
                'service' => 'Service',
                'about' => 'Über Uns',
                'contact' => 'Kontakt',
            ],
            'auth' => [
                'dashboard' => 'Mein Bereich',
                'login' => 'Anmelden',
                'register' => 'Registrieren',
            ],
        ],

        'hero' => [
            'badge' => 'Wohnungsgenossenschaft seit 1954',
            'title' => 'Willkommen',
            'title_highlight' => 'Zuhause',
            'description' => 'Entdecken Sie moderne Wohnungen zu fairen Preisen in Freital, Bannewitz, Rabenau, Wilsdruff und Dresden. Bei uns finden Sie Ihr neues Zuhause.',
            'cta_primary' => 'Wohnung finden',
            'cta_secondary' => 'Mehr erfahren',
            'rent_label' => 'Durchschnittliche Kaltmiete',
            'rent_value' => '5,11 €/m²',
            'rent_badge' => 'Bezahlbar wohnen',
        ],

        'stats' => [
            'experience' => [
                'value' => '70+',
                'label' => 'Jahre Erfahrung',
            ],
            'apartments' => [
                'value' => '3.500+',
                'label' => 'Wohnungen',
            ],
            'locations' => [
                'value' => '5',
                'label' => 'Standorte',
            ],
            'dividend' => [
                'value' => '4%',
                'label' => 'Rückvergütung 2024',
            ],
        ],

        'search' => [
            'title' => 'Wohnungssuche',
            'description' => 'Finden Sie Ihre Traumwohnung in unseren Beständen. Nutzen Sie unsere komfortable Online-Suche oder registrieren Sie Ihren Wohnungswunsch.',
            'form' => [
                'location' => 'Ort',
                'location_placeholder' => 'Alle Orte',
                'rooms' => 'Zimmer',
                'rooms_placeholder' => 'Beliebig',
                'rooms_options' => [
                    '1' => '1 Zimmer',
                    '2' => '2 Zimmer',
                    '3' => '3 Zimmer',
                    '4+' => '4+ Zimmer',
                ],
                'max_rent' => 'Max. Miete',
                'max_rent_placeholder' => '€/Monat',
                'submit' => 'Suchen',
            ],
            'locations' => [
                'freital' => 'Freital',
                'bannewitz' => 'Bannewitz',
                'rabenau' => 'Rabenau',
                'wilsdruff' => 'Wilsdruff',
                'dresden' => 'Dresden',
            ],
            'per_month' => '/Monat',
            'details' => 'Details',
            'show_all' => 'Alle Wohnungen anzeigen',
            'no_listings' => 'Derzeit sind keine Wohnungen verfügbar. Schauen Sie bald wieder vorbei!',
        ],

        'services' => [
            'title' => 'Service für Mieter',
            'description' => 'Als Genossenschaftsmitglied profitieren Sie von umfassenden Serviceleistungen und einer starken Gemeinschaft.',
            'items' => [
                'repair' => [
                    'title' => 'Reparaturservice',
                    'description' => 'Schnelle Hilfe bei Reparaturen und technischen Problemen in Ihrer Wohnung.',
                ],
                'dividend' => [
                    'title' => 'Rückvergütung',
                    'description' => 'Jährliche Rückvergütung für Genossenschaftsmitglieder - 2024: 4% auf Ihre Einlage.',
                ],
                'community' => [
                    'title' => 'Gemeinschaft',
                    'description' => 'Regelmäßige Veranstaltungen und ein aktives Gemeinschaftsleben in unseren Wohnanlagen.',
                ],
                'portal' => [
                    'title' => 'Online-Portal',
                    'description' => 'Verwalten Sie Ihre Angelegenheiten bequem online - Dokumente, Anfragen und mehr.',
                ],
                'security' => [
                    'title' => 'Sicherheit',
                    'description' => 'Lebenslanges Wohnrecht und Schutz vor Eigenbedarfskündigungen.',
                ],
                'modernization' => [
                    'title' => 'Modernisierung',
                    'description' => 'Kontinuierliche Investitionen in Sanierung und moderne Ausstattung.',
                ],
            ],
        ],

        'news' => [
            'title' => 'Aktuelles',
            'show_all' => 'Alle Neuigkeiten',
            'read_more' => 'Mehr erfahren',
            'view_details' => 'Details ansehen',
            'view_project' => 'Zum Projekt',
            'items' => [
                'careers' => [
                    'badge' => 'Karriere',
                    'title' => 'Wir suchen Verstärkung',
                    'description' => 'Werden Sie Teil unseres Teams! Aktuelle Stellenangebote in verschiedenen Bereichen.',
                ],
                'dividend' => [
                    'badge' => 'Mitglieder',
                    'title' => 'Rückvergütung 2024: 4%',
                    'description' => 'Freuen Sie sich auf Ihre Rückvergütung! Die Auszahlung erfolgt wie gewohnt im Frühjahr.',
                ],
                'construction' => [
                    'badge' => 'Bauprojekte',
                    'title' => 'Modernisierung abgeschlossen',
                    'description' => 'Die Sanierungsarbeiten in der Poisentalstraße wurden erfolgreich abgeschlossen.',
                ],
            ],
        ],

        'about' => [
            'title' => 'Über uns',
            'paragraphs' => [
                'intro' => 'Die Freitaler Wohnungsgenossenschaft eG wurde 1954 gegründet und ist heute eine der größten Wohnungsgenossenschaften in der Region Dresden.',
                'mission' => 'Unser Ziel ist es, unseren Mitgliedern Wohnungen mit modernem Standard zu bezahlbaren Preisen anzubieten. Mit einer durchschnittlichen Kaltmiete von 5,11 €/m² (Stand 2024) setzen wir dieses Versprechen täglich um.',
                'mission_highlight' => 'Wohnungen mit modernem Standard zu bezahlbaren Preisen',
                'membership' => 'Als Genossenschaftsmitglied sind Sie nicht nur Mieter, sondern Miteigentümer und gestalten die Zukunft Ihres Wohnumfelds aktiv mit.',
            ],
            'cta' => 'Kontakt aufnehmen',
            'stats' => [
                'founded' => [
                    'value' => '1954',
                    'label' => 'Gründungsjahr',
                ],
                'members' => [
                    'value' => '6.000+',
                    'label' => 'Mitglieder',
                ],
                'apartments' => [
                    'value' => '3.500+',
                    'label' => 'Wohnungen',
                ],
                'rent' => [
                    'value' => '5,11 €',
                    'label' => 'Ø Miete/m²',
                ],
            ],
        ],

        'contact' => [
            'title' => 'Kontakt',
            'description' => 'Haben Sie Fragen oder möchten Sie sich beraten lassen? Wir sind gerne für Sie da.',
            'info' => [
                'address' => [
                    'label' => 'Adresse',
                    'value' => "Dresdner Straße 84\n01705 Freital",
                ],
                'phone' => [
                    'label' => 'Telefon',
                    'value' => '0351 6 49 76-0',
                ],
                'email' => [
                    'label' => 'E-Mail',
                    'value' => 'info@gewo-freital.de',
                ],
                'hours' => [
                    'label' => 'Öffnungszeiten',
                    'value' => "Mo, Di, Do: 9:00 - 12:00 & 13:00 - 18:00 Uhr\nMi, Fr: 9:00 - 12:00 Uhr",
                ],
            ],
            'form' => [
                'title' => 'Nachricht senden',
                'first_name' => 'Vorname',
                'first_name_placeholder' => 'Max',
                'last_name' => 'Nachname',
                'last_name_placeholder' => 'Mustermann',
                'email' => 'E-Mail',
                'email_placeholder' => 'max@beispiel.de',
                'subject' => 'Betreff',
                'subject_options' => [
                    'general' => 'Allgemeine Anfrage',
                    'apartment' => 'Wohnungssuche',
                    'membership' => 'Mitgliedschaft',
                    'repair' => 'Reparaturanfrage',
                    'other' => 'Sonstiges',
                ],
                'message' => 'Nachricht',
                'message_placeholder' => 'Ihre Nachricht...',
                'submit' => 'Absenden',
            ],
        ],

        'footer' => [
            'company' => [
                'name' => 'Freitaler Wohnungsgenossenschaft eG',
                'address' => "Dresdner Straße 84\n01705 Freital",
            ],
            'nav' => [
                'title' => 'Navigation',
                'links' => [
                    'search' => 'Wohnungssuche',
                    'service' => 'Service',
                    'about' => 'Über uns',
                    'contact' => 'Kontakt',
                ],
            ],
            'service' => [
                'title' => 'Service',
                'links' => [
                    'portal' => 'Mieterportal',
                    'damage' => 'Schadensmeldung',
                    'forms' => 'Formulare',
                    'faq' => 'FAQ',
                ],
            ],
            'legal' => [
                'title' => 'Rechtliches',
                'links' => [
                    'imprint' => 'Impressum',
                    'privacy' => 'Datenschutz',
                    'terms' => 'AGB',
                    'accessibility' => 'Barrierefreiheit',
                ],
            ],
            'copyright' => '© :year Freitaler Wohnungsgenossenschaft eG. Alle Rechte vorbehalten.',
            'font_size' => 'Schriftgröße:',
        ],
    ],
];
