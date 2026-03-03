<?php

namespace App\Immonet\Immowelt;

use App\Models\Listing;
use App\Models\User;
use DOMDocument;
use DOMElement;

class OpenImmoGenerator
{
    protected DOMDocument $doc;

    protected string $softwareName = 'GEWO';

    protected string $softwareVersion = '1.0';

    public function __construct()
    {
        $this->doc = new DOMDocument('1.0', 'UTF-8');
        $this->doc->formatOutput = true;
    }

    /**
     * Generate OpenImmo XML for a listing.
     */
    public function generate(Listing $listing, ?User $contact = null, string $action = 'CHANGE'): string
    {
        $root = $this->doc->createElement('openimmo');
        $root->setAttribute('xmlns', 'http://www.openimmo.de');
        $this->doc->appendChild($root);

        // Add uebertragung (transfer info)
        $uebertragung = $this->createUebertragung($action);
        $root->appendChild($uebertragung);

        // Add anbieter (provider)
        $anbieter = $this->createAnbieter($listing, $contact);
        $root->appendChild($anbieter);

        return $this->doc->saveXML();
    }

    /**
     * Generate OpenImmo XML for deleting a listing.
     */
    public function generateDelete(string $externalId, string $providerId): string
    {
        $root = $this->doc->createElement('openimmo');
        $root->setAttribute('xmlns', 'http://www.openimmo.de');
        $this->doc->appendChild($root);

        // Add uebertragung with DELETE action
        $uebertragung = $this->createUebertragung('DELETE');
        $root->appendChild($uebertragung);

        // Add minimal anbieter with immobilie for deletion
        $anbieter = $this->doc->createElement('anbieter');
        $this->addTextElement($anbieter, 'anbieternr', $providerId);

        $immobilie = $this->doc->createElement('immobilie');
        $verwaltung = $this->doc->createElement('verwaltung_techn');
        $this->addTextElement($verwaltung, 'objektnr_extern', $externalId);
        $aktion = $this->doc->createElement('aktion');
        $aktion->setAttribute('aktionart', 'DELETE');
        $verwaltung->appendChild($aktion);
        $immobilie->appendChild($verwaltung);

        $anbieter->appendChild($immobilie);
        $root->appendChild($anbieter);

        return $this->doc->saveXML();
    }

    /**
     * Create uebertragung element.
     */
    protected function createUebertragung(string $action): DOMElement
    {
        $uebertragung = $this->doc->createElement('uebertragung');
        $uebertragung->setAttribute('art', $action);
        $uebertragung->setAttribute('modus', 'CHANGE');
        $uebertragung->setAttribute('version', '1.2.7');
        $uebertragung->setAttribute('sendersoftware', $this->softwareName);
        $uebertragung->setAttribute('senderversion', $this->softwareVersion);
        $uebertragung->setAttribute('tecession', date('Y-m-d\TH:i:s'));

        return $uebertragung;
    }

    /**
     * Create anbieter element with immobilie.
     */
    protected function createAnbieter(Listing $listing, ?User $contact): DOMElement
    {
        $anbieter = $this->doc->createElement('anbieter');

        // Provider ID (use config or default)
        $this->addTextElement($anbieter, 'anbieternr', config('services.immowelt.provider_id', 'GEWO'));

        // Company info
        $this->addTextElement($anbieter, 'firma', config('app.name', 'GEWO'));

        // Add immobilie (property)
        $immobilie = $this->createImmobilie($listing, $contact);
        $anbieter->appendChild($immobilie);

        return $anbieter;
    }

    /**
     * Create immobilie element.
     */
    protected function createImmobilie(Listing $listing, ?User $contact): DOMElement
    {
        $immobilie = $this->doc->createElement('immobilie');

        // Objektkategorie (object category)
        $immobilie->appendChild($this->createObjektkategorie());

        // Geo (location)
        $immobilie->appendChild($this->createGeo($listing));

        // Kontaktperson (contact)
        if ($contact) {
            $immobilie->appendChild($this->createKontaktperson($contact));
        }

        // Preise (prices)
        $immobilie->appendChild($this->createPreise($listing));

        // Flaechen (areas)
        $immobilie->appendChild($this->createFlaechen($listing));

        // Ausstattung (equipment/features)
        $immobilie->appendChild($this->createAusstattung($listing));

        // Zustand/Angaben (condition)
        $immobilie->appendChild($this->createZustandAngaben($listing));

        // Freitexte (descriptions)
        $immobilie->appendChild($this->createFreitexte($listing));

        // Anhaenge (attachments)
        if ($listing->images->isNotEmpty()) {
            $immobilie->appendChild($this->createAnhaenge($listing));
        }

        // Verwaltung_techn (technical management)
        $immobilie->appendChild($this->createVerwaltungTechn($listing));

        return $immobilie;
    }

    /**
     * Create objektkategorie element.
     */
    protected function createObjektkategorie(): DOMElement
    {
        $kategorie = $this->doc->createElement('objektkategorie');

        // Nutzungsart (usage type)
        $nutzungsart = $this->doc->createElement('nutzungsart');
        $nutzungsart->setAttribute('WOHNEN', 'true');
        $kategorie->appendChild($nutzungsart);

        // Vermarktungsart (marketing type)
        $vermarktungsart = $this->doc->createElement('vermarktungsart');
        $vermarktungsart->setAttribute('MIETE_PACHT', 'true');
        $kategorie->appendChild($vermarktungsart);

        // Objektart (object type)
        $objektart = $this->doc->createElement('objektart');
        $wohnung = $this->doc->createElement('wohnung');
        $wohnung->setAttribute('wohnungtyp', 'ETAGE');
        $objektart->appendChild($wohnung);
        $kategorie->appendChild($objektart);

        return $kategorie;
    }

    /**
     * Create geo element.
     */
    protected function createGeo(Listing $listing): DOMElement
    {
        $geo = $this->doc->createElement('geo');

        $this->addTextElement($geo, 'plz', $listing->postal_code);
        $this->addTextElement($geo, 'ort', $listing->city);
        $this->addTextElement($geo, 'strasse', $listing->street);
        $this->addTextElement($geo, 'hausnummer', $listing->street_number);
        $this->addTextElement($geo, 'land', ['iso_land' => 'DEU']);

        if ($listing->floor !== null) {
            $this->addTextElement($geo, 'etage', (string) $listing->floor);
        }

        return $geo;
    }

    /**
     * Create kontaktperson element.
     */
    protected function createKontaktperson(User $contact): DOMElement
    {
        $kontakt = $this->doc->createElement('kontaktperson');

        $this->addTextElement($kontakt, 'email_zentrale', $contact->email);
        $this->addTextElement($kontakt, 'vorname', $contact->first_name);
        $this->addTextElement($kontakt, 'name', $contact->last_name);

        return $kontakt;
    }

    /**
     * Create preise element.
     */
    protected function createPreise(Listing $listing): DOMElement
    {
        $preise = $this->doc->createElement('preise');

        // Kaltmiete (cold rent)
        $kaltmiete = $this->doc->createElement('kaltmiete');
        $kaltmiete->nodeValue = number_format((float) $listing->rent_cold, 2, '.', '');
        $preise->appendChild($kaltmiete);

        // Nebenkosten (utility costs)
        if ($listing->utility_cost > 0) {
            $nebenkosten = $this->doc->createElement('nebenkosten');
            $nebenkosten->nodeValue = number_format((float) $listing->utility_cost, 2, '.', '');
            $preise->appendChild($nebenkosten);
        }

        // Warmmiete (warm rent)
        $warmmiete = $this->doc->createElement('warmmiete');
        $warmmiete->nodeValue = number_format($listing->totalRent(), 2, '.', '');
        $preise->appendChild($warmmiete);

        // Currency
        $waehrung = $this->doc->createElement('waehrung');
        $waehrung->setAttribute('iso_waehrung', 'EUR');
        $preise->appendChild($waehrung);

        return $preise;
    }

    /**
     * Create flaechen element.
     */
    protected function createFlaechen(Listing $listing): DOMElement
    {
        $flaechen = $this->doc->createElement('flaechen');

        $this->addTextElement($flaechen, 'wohnflaeche', number_format((float) $listing->size_sqm, 2, '.', ''));
        $this->addTextElement($flaechen, 'anzahl_zimmer', (string) $listing->rooms);

        return $flaechen;
    }

    /**
     * Create ausstattung element.
     */
    protected function createAusstattung(Listing $listing): DOMElement
    {
        $ausstattung = $this->doc->createElement('ausstattung');

        // Balkon/Terrasse
        if ($listing->has_balcony) {
            $ausstattung->appendChild($this->doc->createElement('balkon'));
        }

        if ($listing->has_terrace) {
            $ausstattung->appendChild($this->doc->createElement('terrasse'));
        }

        // Aufzug (elevator)
        if ($listing->has_elevator) {
            $fahrstuhl = $this->doc->createElement('fahrstuhl');
            $fahrstuhl->setAttribute('PERSONEN', 'true');
            $ausstattung->appendChild($fahrstuhl);
        }

        // Barrierefrei (wheelchair accessible)
        if ($listing->is_wheelchair_accessible) {
            $ausstattung->appendChild($this->doc->createElement('barrierefrei'));
        }

        // Haustiere (pets)
        if ($listing->pets_allowed !== null) {
            $haustiere = $this->doc->createElement('haustiere');
            $haustiere->nodeValue = $listing->pets_allowed ? 'JA' : 'NEIN';
            $ausstattung->appendChild($haustiere);
        }

        return $ausstattung;
    }

    /**
     * Create zustand_angaben element.
     */
    protected function createZustandAngaben(Listing $listing): DOMElement
    {
        $zustand = $this->doc->createElement('zustand_angaben');

        if ($listing->year_built) {
            $this->addTextElement($zustand, 'baujahr', (string) $listing->year_built);
        }

        return $zustand;
    }

    /**
     * Create freitexte element.
     */
    protected function createFreitexte(Listing $listing): DOMElement
    {
        $freitexte = $this->doc->createElement('freitexte');

        $this->addTextElement($freitexte, 'objekttitel', mb_substr($listing->title, 0, 100));

        if ($listing->description) {
            $this->addTextElement($freitexte, 'objektbeschreibung', $listing->description);
        }

        return $freitexte;
    }

    /**
     * Create anhaenge element.
     */
    protected function createAnhaenge(Listing $listing): DOMElement
    {
        $anhaenge = $this->doc->createElement('anhaenge');

        foreach ($listing->images as $index => $image) {
            $anhang = $this->doc->createElement('anhang');
            $anhang->setAttribute('location', 'EXTERN');
            $anhang->setAttribute('gruppe', 'BILD');

            // Title
            $this->addTextElement($anhang, 'anhangtitel', $image->alt ?? 'Bild '.($index + 1));

            // Format
            $format = $this->doc->createElement('format');
            $format->nodeValue = strtoupper(pathinfo($image->path, PATHINFO_EXTENSION)) ?: 'JPG';
            $anhang->appendChild($format);

            // Daten (data reference)
            $daten = $this->doc->createElement('daten');
            $this->addTextElement($daten, 'pfad', basename($image->path));
            $anhang->appendChild($daten);

            $anhaenge->appendChild($anhang);
        }

        return $anhaenge;
    }

    /**
     * Create verwaltung_techn element.
     */
    protected function createVerwaltungTechn(Listing $listing): DOMElement
    {
        $verwaltung = $this->doc->createElement('verwaltung_techn');

        // External object number (our listing ID)
        $this->addTextElement($verwaltung, 'objektnr_extern', $listing->id);

        // Action type
        $aktion = $this->doc->createElement('aktion');
        $aktion->setAttribute('aktionart', 'CHANGE');
        $verwaltung->appendChild($aktion);

        // Active until (optional - set to 30 days from now)
        $aktivBis = $this->doc->createElement('aktiv_bis');
        $aktivBis->nodeValue = now()->addDays(30)->format('Y-m-d');
        $verwaltung->appendChild($aktivBis);

        // Available from
        if ($listing->available_from) {
            $this->addTextElement($verwaltung, 'verfuegbar_ab', $listing->available_from->format('Y-m-d'));
        }

        return $verwaltung;
    }

    /**
     * Add a text element to a parent.
     *
     * @param  string|array<string, string>  $value
     */
    protected function addTextElement(DOMElement $parent, string $name, string|array $value): void
    {
        $element = $this->doc->createElement($name);

        if (is_array($value)) {
            foreach ($value as $attr => $attrValue) {
                $element->setAttribute($attr, $attrValue);
            }
        } else {
            $element->nodeValue = htmlspecialchars($value, ENT_XML1);
        }

        $parent->appendChild($element);
    }

    /**
     * Set the software name for the transfer header.
     */
    public function setSoftwareName(string $name): self
    {
        $this->softwareName = $name;

        return $this;
    }

    /**
     * Set the software version for the transfer header.
     */
    public function setSoftwareVersion(string $version): self
    {
        $this->softwareVersion = $version;

        return $this;
    }
}
