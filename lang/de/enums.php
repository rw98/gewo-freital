<?php

return [
    'outdoor_space_type' => [
        'balcony' => 'Balkon',
        'terrace' => 'Terrasse',
    ],

    'orientation' => [
        'N' => 'Nord',
        'NE' => 'Nordost',
        'E' => 'Ost',
        'SE' => 'Südost',
        'S' => 'Süd',
        'SW' => 'Südwest',
        'W' => 'West',
        'NW' => 'Nordwest',
    ],

    'image_type' => [
        'exterior' => 'Außenansicht',
        'interior' => 'Innenansicht',
        'layout' => 'Grundriss',
        'floor' => 'Etage',
        'entrance' => 'Eingang',
        'living_room' => 'Wohnzimmer',
        'bedroom' => 'Schlafzimmer',
        'kitchen' => 'Küche',
        'bathroom' => 'Badezimmer',
        'guest_bathroom' => 'Gäste-WC',
        'balcony' => 'Balkon',
        'terrace' => 'Terrasse',
        'other' => 'Sonstiges',
    ],

    'listing_status' => [
        'draft' => 'Entwurf',
        'published' => 'Veröffentlicht',
        'archived' => 'Archiviert',
    ],

    'listing_request_status' => [
        'requested' => 'Angefragt',
        'pending_email_confirmation' => 'E-Mail-Bestätigung ausstehend',
        'confirmed' => 'Bestätigt',
        'appointment_pending' => 'Termin ausstehend',
        'waiting_for_information' => 'Warten auf Unterlagen',
        'waiting_for_approval' => 'Warten auf Genehmigung',
        'approved' => 'Genehmigt',
        'waiting_for_signature' => 'Warten auf Unterschrift',
        'signed' => 'Unterschrieben',
        'closed' => 'Abgeschlossen',
        'rejected' => 'Abgelehnt',
    ],

    'request_document_type' => [
        'income_proof' => 'Einkommensnachweis',
        'id_document' => 'Ausweisdokument',
        'employment_contract' => 'Arbeitsvertrag',
        'schufa' => 'SCHUFA-Auskunft',
        'rental_history' => 'Mietschuldenfreiheitsbescheinigung',
        'other' => 'Sonstiges',
    ],

    'request_appointment_status' => [
        'pending' => 'Ausstehend',
        'confirmed' => 'Bestätigt',
        'cancelled' => 'Abgesagt',
        'completed' => 'Durchgeführt',
    ],

    'energy_efficiency_class' => [
        'A+' => 'Energieeffizienzklasse A+',
        'A' => 'Energieeffizienzklasse A',
        'B' => 'Energieeffizienzklasse B',
        'C' => 'Energieeffizienzklasse C',
        'D' => 'Energieeffizienzklasse D',
        'E' => 'Energieeffizienzklasse E',
        'F' => 'Energieeffizienzklasse F',
        'G' => 'Energieeffizienzklasse G',
        'H' => 'Energieeffizienzklasse H',
    ],

    'energy_efficiency_description' => [
        'A+' => 'Passivhaus, KfW-Effizienzhaus 40',
        'A' => 'Niedrigenergiehäuser, KfW-Effizienzhaus 55',
        'B' => 'Neubau-Standard',
        'C' => 'Mindestanforderung für Neubauten',
        'D' => 'Gut sanierte Altbauten',
        'E' => 'Sanierte Altbauten',
        'F' => 'Sanierte Altbauten',
        'G' => 'Teilweise sanierte Altbauten',
        'H' => 'Unsanierte Altbauten',
    ],

    'energy_certificate_type' => [
        'consumption' => 'Verbrauchsausweis',
        'demand' => 'Bedarfsausweis',
        'consumption_description' => 'Basiert auf dem tatsächlichen Energieverbrauch der letzten 3 Jahre',
        'demand_description' => 'Basiert auf einer technischen Analyse des Gebäudes',
    ],

    'energy_source' => [
        'gas' => 'Erdgas',
        'oil' => 'Heizöl',
        'district_heating' => 'Fernwärme',
        'electricity' => 'Strom',
        'heat_pump' => 'Wärmepumpe',
        'pellets' => 'Pellets',
        'wood' => 'Holz',
        'solar' => 'Solar',
        'geothermal' => 'Geothermie',
        'other' => 'Sonstige',
    ],
];
