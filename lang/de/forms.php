<?php

return [
    // Navigation
    'nav' => [
        'forms' => 'Formulare',
    ],

    // Index
    'index' => [
        'title' => 'Formulare',
        'description' => 'Erstellen und verwalten Sie benutzerdefinierte Formulare',
        'create' => 'Formular erstellen',
        'import_pdf' => 'PDF importieren',
        'search_placeholder' => 'Formulare durchsuchen...',
        'all_statuses' => 'Alle Status',
        'status_active' => 'Aktiv',
        'status_inactive' => 'Inaktiv',
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv',
        'columns' => [
            'name' => 'Name',
            'fields' => 'Felder',
            'responses' => 'Antworten',
            'status' => 'Status',
            'creator' => 'Erstellt von',
            'updated_at' => 'Aktualisiert',
        ],
        'empty' => [
            'title' => 'Keine Formulare',
            'description' => 'Erstellen Sie Ihr erstes Formular, um Daten von Interessenten zu sammeln.',
            'create' => 'Formular erstellen',
        ],
        'actions' => [
            'edit' => 'Bearbeiten',
            'copy_link' => 'Link kopieren',
            'duplicate' => 'Duplizieren',
            'activate' => 'Aktivieren',
            'deactivate' => 'Deaktivieren',
            'delete' => 'Löschen',
        ],
        'link_copied' => 'Link kopiert',
        'confirm_delete' => 'Möchten Sie dieses Formular wirklich löschen? Alle Antworten gehen verloren.',
        'create_modal' => [
            'title' => 'Neues Formular',
            'description' => 'Erstellen Sie ein neues Formular für Interessenten.',
            'name' => 'Name',
            'name_placeholder' => 'z.B. Interessentenformular',
            'description_label' => 'Beschreibung',
            'description_placeholder' => 'Optionale Beschreibung des Formulars...',
            'cancel' => 'Abbrechen',
            'create' => 'Erstellen',
        ],
    ],

    // Builder
    'builder' => [
        'title' => 'Formular: :form',
        'back' => 'Zurück',
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv',
        'copy_link' => 'Link kopieren',
        'preview' => 'Vorschau',
        'activate' => 'Aktivieren',
        'deactivate' => 'Deaktivieren',
        'link_copied' => 'Formular-Link kopiert',
        'field_types' => 'Feldtypen',
        'form_settings' => 'Formular-Einstellungen',
        'description' => 'Beschreibung',
        'description_placeholder' => 'Optionale Beschreibung für Benutzer...',
        'success_message' => 'Erfolgsmeldung',
        'success_message_placeholder' => 'Nachricht nach erfolgreicher Einreichung...',
        'empty' => [
            'title' => 'Keine Felder',
            'description' => 'Fügen Sie Felder hinzu, um Ihr Formular zu erstellen.',
        ],
        'add_field' => 'Feld hinzufügen',
        'required' => 'Pflichtfeld',
        'confirm_delete_field' => 'Möchten Sie dieses Feld wirklich löschen?',
        'cancel' => 'Abbrechen',

        // Field editor
        'field_label' => 'Beschriftung',
        'field_name' => 'Feldname',
        'field_name_description' => 'Technischer Name (ohne Leerzeichen)',
        'field_description' => 'Hilfetext',
        'field_placeholder' => 'Platzhalter',
        'field_required' => 'Pflichtfeld',

        // Type-specific options
        'text_options' => 'Text-Optionen',
        'min_length' => 'Mindestlänge',
        'max_length' => 'Maximallänge',

        'email_info' => 'E-Mail-Adressen werden automatisch validiert.',

        'textarea_options' => 'Textbereich-Optionen',
        'rows' => 'Zeilen',

        'select_options' => 'Dropdown-Optionen',
        'allow_multiple' => 'Mehrfachauswahl erlauben',
        'options' => 'Optionen',
        'option_label' => 'Optionstext',
        'new_option' => 'Neue Option hinzufügen...',

        'radio_options' => 'Radio-Optionen',

        'checkbox_options' => 'Checkbox-Optionen',
        'default_checked' => 'Standardmäßig aktiviert',

        'date_options' => 'Datum-Optionen',
        'min_date' => 'Frühestes Datum',
        'max_date' => 'Spätestes Datum',

        'file_options' => 'Datei-Optionen',
        'allowed_extensions' => 'Erlaubte Dateitypen',
        'allowed_extensions_description' => 'Kommagetrennte Liste (z.B. pdf, jpg, png)',
        'allowed_extensions_placeholder' => 'pdf, jpg, jpeg, png',
        'max_size_kb' => 'Maximale Dateigröße (KB)',
        'max_size_description' => 'Maximale Dateigröße in Kilobytes',

        'number_options' => 'Zahl-Optionen',
        'min_value' => 'Minimalwert',
        'max_value' => 'Maximalwert',
        'step' => 'Schrittweite',

        'phone_info' => 'Telefonnummern werden als Text gespeichert.',

        'info_options' => 'Infotext-Optionen',
        'info_style' => 'Darstellung',
        'info_style_default' => 'Standard',
        'info_style_info' => 'Information (blau)',
        'info_style_warning' => 'Warnung (gelb)',
        'info_style_success' => 'Erfolg (grün)',
        'info_content' => 'Inhalt',
        'info_content_placeholder' => 'Geben Sie hier den Infotext ein...',
        'info_description' => 'Infotext-Felder zeigen nur Text an und erfordern keine Eingabe.',

        'autofill_options' => 'Automatisch ausfüllen',
        'autofill_source' => 'Datenquelle',
        'autofill_none' => 'Keine (manuell)',
        'autofill_field' => 'Feld',
        'autofill_select_field' => 'Feld auswählen...',
        'autofill_description' => 'Felder können automatisch mit Daten aus Inseraten, Anfragen oder Wohnungen befüllt werden.',
    ],

    // Dynamic Form
    'dynamic' => [
        'success_title' => 'Vielen Dank!',
        'success_message' => 'Ihr Formular wurde erfolgreich eingereicht.',
        'submit' => 'Absenden',
        'submitting' => 'Wird gesendet...',
    ],

    // Field renderer
    'field' => [
        'select_placeholder' => 'Bitte wählen...',
        'allowed_types' => 'Erlaubte Dateitypen',
        'max_size' => 'Maximale Größe',
    ],

    // Custom form (requestee)
    'custom_form' => [
        'title' => 'Zusätzliches Formular',
        'not_available' => 'Dieses Formular ist derzeit nicht verfügbar.',
        'for_listing' => 'Für: :title',
        'already_completed' => 'Sie haben dieses Formular bereits ausgefüllt.',
    ],

    // Assignment (employee view)
    'assign_form' => 'Zusätzliches Formular',
    'no_form' => 'Kein Formular zugewiesen',
    'completed' => 'Ausgefüllt',
    'pending' => 'Ausstehend',

    // AI Import
    'ai' => [
        'modal_title' => 'Formular aus PDF erstellen',
        'modal_description' => 'Laden Sie ein PDF hoch und lassen Sie KI die Formularfelder erkennen.',
        'form_name' => 'Formularname',
        'form_name_placeholder' => 'z.B. Bewerbungsformular',
        'pdf_file' => 'PDF-Datei',
        'drop_pdf' => 'PDF hier ablegen',
        'or_click' => 'oder klicken zum Auswählen',
        'pdf_description' => 'Maximal 10 MB. Die KI analysiert den Inhalt und erstellt passende Formularfelder.',
        'additional_instructions' => 'Zusätzliche Anweisungen (optional)',
        'additional_instructions_placeholder' => 'z.B. "Ignoriere die erste Seite" oder "Füge ein Feld für Unterschrift hinzu"',
        'additional_instructions_description' => 'Geben Sie der KI zusätzliche Hinweise zur Formularerstellung.',
        'cancel' => 'Abbrechen',
        'processing' => 'Wird verarbeitet...',
        'generate' => 'Formular generieren',
        'generated_description' => 'Dieses Formular wurde automatisch aus einem PDF erstellt.',
        'no_fields_found' => 'Es konnten keine Formularfelder aus dem PDF extrahiert werden.',
        'pdf_empty' => 'Das PDF enthält keinen lesbaren Text.',
        'api_key_missing' => 'Der KI-API-Schlüssel ist nicht konfiguriert.',
        'failed' => 'Die KI-Anfrage ist fehlgeschlagen',
        'parse_failed' => 'Die KI-Antwort konnte nicht verarbeitet werden.',
    ],
];
