<?php

return [
    // General
    'title' => 'Wohnungsanfragen',
    'request' => 'Anfrage',
    'requests' => 'Anfragen',
    'no_requests' => 'Keine Anfragen vorhanden',

    // Request form
    'create_title' => 'Wohnung anfragen',
    'create_description' => 'Füllen Sie das Formular aus, um diese Wohnung anzufragen.',
    'submit_request' => 'Anfrage senden',
    'request_submitted' => 'Ihre Anfrage wurde erfolgreich gesendet.',
    'request_submitted_description' => 'Sie erhalten in Kürze eine E-Mail zur Bestätigung Ihrer E-Mail-Adresse.',

    // Form fields
    'email' => 'E-Mail-Adresse',
    'phone' => 'Telefonnummer',
    'first_name' => 'Vorname',
    'middle_name' => 'Zweiter Vorname',
    'last_name' => 'Nachname',
    'message' => 'Nachricht',
    'message_placeholder' => 'Erzählen Sie uns etwas über sich und warum Sie an dieser Wohnung interessiert sind...',

    // Email confirmation
    'confirm_email_title' => 'E-Mail-Adresse bestätigen',
    'confirm_email_description' => 'Bitte klicken Sie auf den Link in der E-Mail, die wir Ihnen gesendet haben.',
    'email_confirmed' => 'Ihre E-Mail-Adresse wurde erfolgreich bestätigt.',
    'email_confirmed_description' => 'Ihre Anfrage wird nun bearbeitet. Sie erhalten weitere Informationen per E-Mail.',
    'resend_confirmation' => 'Bestätigungslink erneut senden',
    'confirmation_resent' => 'Bestätigungslink wurde erneut gesendet.',

    // Requestee portal
    'portal_title' => 'Ihre Anfrage',
    'portal_description' => 'Hier können Sie den Status Ihrer Anfrage einsehen und Dokumente hochladen.',
    'current_status' => 'Aktueller Status',
    'requested_at' => 'Angefragt am',
    'assigned_employee' => 'Zuständiger Mitarbeiter',
    'not_assigned' => 'Noch nicht zugewiesen',

    // Documents
    'documents' => 'Dokumente',
    'upload_document' => 'Dokument hochladen',
    'document_type' => 'Dokumenttyp',
    'document_uploaded' => 'Dokument erfolgreich hochgeladen.',
    'no_documents' => 'Noch keine Dokumente hochgeladen.',
    'download_document' => 'Herunterladen',
    'delete_document' => 'Löschen',
    'document_deleted' => 'Dokument erfolgreich gelöscht.',
    'required_documents' => 'Benötigte Unterlagen',
    'required_documents_description' => 'Bitte laden Sie folgende Unterlagen hoch:',

    // Appointments
    'appointments' => 'Termine',
    'book_appointment' => 'Termin buchen',
    'available_timeslots' => 'Verfügbare Termine',
    'no_timeslots' => 'Derzeit sind keine Termine verfügbar.',
    'appointment_booked' => 'Termin erfolgreich gebucht.',
    'appointment_cancelled' => 'Termin erfolgreich abgesagt.',
    'cancel_appointment' => 'Termin absagen',
    'your_appointment' => 'Ihr Termin',
    'appointment_on' => 'Termin am',
    'remaining_slots' => ':count Plätze verfügbar',
    'no_slots_available' => 'Keine Plätze verfügbar',

    // Messages
    'messages' => 'Nachrichten',
    'send_message' => 'Nachricht senden',
    'message_sent' => 'Nachricht erfolgreich gesendet.',
    'no_messages' => 'Noch keine Nachrichten.',
    'new_message' => 'Neue Nachricht',
    'message_content' => 'Nachricht',
    'sent_by_you' => 'Sie',
    'sent_by_employee' => 'Mitarbeiter',

    // Employee management
    'manage_requests' => 'Anfragen verwalten',
    'all_requests' => 'Alle Anfragen',
    'filter_by_status' => 'Nach Status filtern',
    'filter_by_listing' => 'Nach Inserat filtern',
    'assign_to' => 'Zuweisen an',
    'assigned_to' => 'Zugewiesen an',
    'change_status' => 'Status ändern',
    'status_changed' => 'Status erfolgreich geändert.',
    'reject_request' => 'Anfrage ablehnen',
    'rejection_reason' => 'Ablehnungsgrund',
    'approve_request' => 'Anfrage genehmigen',

    // Timeslots management
    'manage_timeslots' => 'Termine verwalten',
    'create_timeslot' => 'Termin erstellen',
    'timeslot_created' => 'Termin erfolgreich erstellt.',
    'timeslot_deleted' => 'Termin erfolgreich gelöscht.',
    'starts_at' => 'Beginn',
    'ends_at' => 'Ende',
    'max_attendees' => 'Max. Teilnehmer',
    'location' => 'Ort',
    'notes' => 'Notizen',
    'active' => 'Aktiv',
    'inactive' => 'Inaktiv',
    'booked' => 'Gebucht',

    // Status transitions
    'transition_to' => 'Status ändern zu',
    'confirm_transition' => 'Möchten Sie den Status wirklich ändern?',
    'transition_not_allowed' => 'Diese Statusänderung ist nicht erlaubt.',

    // Notifications
    'notification_request_received' => 'Ihre Anfrage wurde erhalten',
    'notification_email_verification' => 'Bitte bestätigen Sie Ihre E-Mail-Adresse',
    'notification_status_changed' => 'Der Status Ihrer Anfrage hat sich geändert',
    'notification_information_requested' => 'Unterlagen werden benötigt',
    'notification_appointment_confirmed' => 'Ihr Termin wurde bestätigt',
    'notification_appointment_reminder' => 'Erinnerung: Ihr Termin findet morgen statt',
    'notification_new_message' => 'Sie haben eine neue Nachricht erhalten',
    'notification_listing_closed' => 'Das Inserat wurde geschlossen',

    // Email content
    'emails' => [
        'subject_confirmed' => 'Ihre Anfrage wurde bestätigt',
        'subject_appointment_pending' => 'Bitte vereinbaren Sie einen Termin',
        'subject_waiting_for_information' => 'Unterlagen werden benötigt',
        'subject_waiting_for_signature' => 'Ihr Mietvertrag ist bereit',
        'subject_signed' => 'Willkommen in Ihrer neuen Wohnung!',
        'subject_rejected' => 'Ihre Anfrage wurde leider abgelehnt',
        'subject_closed' => 'Ihre Anfrage wurde abgeschlossen',
        'subject_new_message' => 'Neue Nachricht zu Ihrer Wohnungsanfrage',

        // Confirmed
        'confirmed_line1' => 'Ihre E-Mail-Adresse wurde erfolgreich bestätigt. Ihre Anfrage für die Wohnung ":title" wird nun bearbeitet.',
        'confirmed_line2' => 'Ein Mitarbeiter wird sich in Kürze bei Ihnen melden.',

        // Appointment pending
        'appointment_pending_line1' => 'Sie können nun einen Besichtigungstermin für die Wohnung ":title" vereinbaren.',
        'appointment_pending_line2' => 'Bitte wählen Sie einen der verfügbaren Termine aus.',

        // Waiting for information
        'waiting_for_information_line1' => 'Für Ihre Anfrage zur Wohnung ":title" werden noch Unterlagen benötigt.',
        'waiting_for_information_line2' => 'Bitte laden Sie die angeforderten Dokumente in Ihrem Portal hoch.',

        // Waiting for signature
        'waiting_for_signature_line1' => 'Gute Neuigkeiten! Ihr Mietvertrag für die Wohnung ":title" ist bereit zur Unterschrift.',
        'waiting_for_signature_line2' => 'Bitte kontaktieren Sie uns, um einen Termin zur Vertragsunterzeichnung zu vereinbaren.',

        // Signed
        'signed_line1' => 'Herzlichen Glückwunsch! Der Mietvertrag für die Wohnung ":title" wurde unterschrieben.',
        'signed_line2' => 'Wir heißen Sie herzlich willkommen und freuen uns auf eine gute Zusammenarbeit.',

        // Rejected
        'rejected_line1' => 'Leider müssen wir Ihnen mitteilen, dass Ihre Anfrage für die Wohnung ":title" abgelehnt wurde.',
        'rejected_reason' => 'Grund: :reason',

        // Closed
        'closed_line1' => 'Ihre Anfrage für die Wohnung ":title" wurde abgeschlossen.',
        'closed_line2' => 'Vielen Dank für Ihr Interesse.',

        // New message
        'new_message_line1' => 'Sie haben eine neue Nachricht zu Ihrer Anfrage für die Wohnung ":title" erhalten:',
        'view_message' => 'Nachricht ansehen',
    ],
];
