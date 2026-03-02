<!DOCTYPE html>
<html lang="de" data-font-size="normal" class="light" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Willkommen Zuhause - GEWO Freital</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Force light mode --}}
    <script>
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.add('light');
    </script>
</head>
<body class="min-h-screen bg-white text-gewo-grey-800 antialiased">
    {{-- Header --}}
    <header class="sticky top-0 z-50 bg-white border-b border-gewo-grey-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                {{-- Logo --}}
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1">
                        {{-- GEWO Logo dots pattern --}}
                        <svg width="40" height="40" viewBox="0 0 40 40" class="text-accent">
                            <circle cx="5" cy="5" r="3" fill="currentColor"/>
                            <circle cx="13" cy="5" r="3" fill="currentColor"/>
                            <circle cx="21" cy="5" r="3" fill="currentColor"/>
                            <circle cx="29" cy="5" r="3" fill="currentColor"/>
                            <circle cx="5" cy="13" r="3" fill="currentColor"/>
                            <circle cx="13" cy="13" r="3" fill="currentColor"/>
                            <circle cx="21" cy="13" r="3" fill="currentColor"/>
                            <circle cx="5" cy="21" r="3" fill="currentColor"/>
                            <circle cx="13" cy="21" r="3" fill="currentColor"/>
                            <circle cx="5" cy="29" r="3" fill="currentColor"/>
                            {{-- Red accent --}}
                            <rect x="24" y="16" width="12" height="6" rx="3" fill="#c8102e"/>
                        </svg>
                    </div>
                    <div class="hidden sm:block">
                        <span class="text-accent font-bold text-lg">gewo</span>
                        <span class="text-gewo-grey-800 text-xs block leading-tight">FREITALER<br>WOHNUNGSGENOSSENSCHAFT eG</span>
                    </div>
                </div>

                {{-- Desktop Navigation --}}
                <nav class="hidden lg:flex items-center gap-1">
                    <flux:navbar>
                        <flux:navbar.item href="#wohnungen" icon="home">Mieten</flux:navbar.item>
                        <flux:navbar.item href="#service" icon="wrench-screwdriver">Service</flux:navbar.item>
                        <flux:navbar.item href="#ueber-uns" icon="building-office-2">Über Uns</flux:navbar.item>
                        <flux:navbar.item href="#kontakt" icon="phone">Kontakt</flux:navbar.item>
                    </flux:navbar>
                </nav>

                {{-- Right side: Accessibility + Auth --}}
                <div class="flex items-center gap-4">
                    {{-- Font Size Selector (Accessibility) --}}
                    <div class="hidden md:block">
                        <livewire:font-size-selector />
                    </div>

                    {{-- Auth buttons --}}
                    @if (Route::has('login'))
                        <div class="flex items-center gap-2">
                            @auth
                                <flux:button href="{{ route('dashboard') }}" variant="primary" size="sm">
                                    Mein Bereich
                                </flux:button>
                            @else
                                <flux:button href="{{ route('login') }}" variant="ghost" size="sm">
                                    Anmelden
                                </flux:button>
                                @if (Route::has('register'))
                                    <flux:button href="{{ route('register') }}" variant="primary" size="sm">
                                        Registrieren
                                    </flux:button>
                                @endif
                            @endauth
                        </div>
                    @endif

                    {{-- Mobile menu button --}}
                    <flux:button variant="ghost" size="sm" class="lg:hidden" x-data x-on:click="$dispatch('open-mobile-menu')">
                        <flux:icon name="bars-3" class="size-5" />
                    </flux:button>
                </div>
            </div>
        </div>
    </header>

    {{-- Mobile Navigation Modal --}}
    <flux:modal name="mobile-menu" class="lg:hidden">
        <div class="p-4">
            <flux:navlist>
                <flux:navlist.item href="#wohnungen" icon="home">Mieten</flux:navlist.item>
                <flux:navlist.item href="#service" icon="wrench-screwdriver">Service</flux:navlist.item>
                <flux:navlist.item href="#ueber-uns" icon="building-office-2">Über Uns</flux:navlist.item>
                <flux:navlist.item href="#kontakt" icon="phone">Kontakt</flux:navlist.item>
            </flux:navlist>
            <div class="mt-6 pt-6 border-t border-gewo-grey-200">
                <livewire:font-size-selector />
            </div>
        </div>
    </flux:modal>

    <main>
        {{-- Hero Section --}}
        <section class="relative bg-linear-to-br from-gewo-blue-50 to-white overflow-hidden">
            <div class="absolute inset-0 opacity-5">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <pattern id="dots" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                        <circle cx="2" cy="2" r="1" fill="#00a3d9"/>
                    </pattern>
                    <rect width="100" height="100" fill="url(#dots)"/>
                </svg>
            </div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <flux:badge color="sky" class="mb-4">Wohnungsgenossenschaft seit 1954</flux:badge>
                        <flux:heading size="xl" level="1" class="text-4xl lg:text-5xl mb-6">
                            Willkommen<br>
                            <span class="text-accent">Zuhause</span>
                        </flux:heading>
                        <flux:text size="lg" class="mb-8 max-w-lg">
                            Entdecken Sie moderne Wohnungen zu fairen Preisen in Freital, Bannewitz, Rabenau, Wilsdruff und Dresden. Bei uns finden Sie Ihr neues Zuhause.
                        </flux:text>
                        <div class="flex flex-wrap gap-4">
                            <flux:button variant="primary" href="#wohnungen" icon="magnifying-glass" class="px-6! py-3!">
                                Wohnung finden
                            </flux:button>
                            <flux:button variant="outline" href="#ueber-uns" icon="information-circle" class="px-6! py-3!">
                                Mehr erfahren
                            </flux:button>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gewo-grey-100">
                            <div class="aspect-video bg-linear-to-br from-gewo-blue-100 to-gewo-blue-50 rounded-lg flex items-center justify-center">
                                <flux:icon name="home-modern" class="size-24 text-accent" />
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <div>
                                    <flux:text size="sm">Durchschnittliche Kaltmiete</flux:text>
                                    <flux:heading size="lg" class="text-2xl text-accent">5,11 €/m²</flux:heading>
                                </div>
                                <flux:badge color="emerald" size="lg">Bezahlbar wohnen</flux:badge>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Quick Stats --}}
        <section class="bg-gewo-blue-800 py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center">
                        <flux:heading class="text-4xl font-bold text-white">70+</flux:heading>
                        <flux:text class="text-gewo-blue-200 mt-1">Jahre Erfahrung</flux:text>
                    </div>
                    <div class="text-center">
                        <flux:heading class="text-4xl font-bold text-white">3.500+</flux:heading>
                        <flux:text class="text-gewo-blue-200 mt-1">Wohnungen</flux:text>
                    </div>
                    <div class="text-center">
                        <flux:heading class="text-4xl font-bold text-white">5</flux:heading>
                        <flux:text class="text-gewo-blue-200 mt-1">Standorte</flux:text>
                    </div>
                    <div class="text-center">
                        <flux:heading class="text-4xl font-bold text-white">4%</flux:heading>
                        <flux:text class="text-gewo-blue-200 mt-1">Rückvergütung 2024</flux:text>
                    </div>
                </div>
            </div>
        </section>

        {{-- Apartment Search Section --}}
        <section id="wohnungen" class="py-16 lg:py-24 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <flux:heading size="xl" level="2">Wohnungssuche</flux:heading>
                    <flux:text class="mt-4 max-w-2xl mx-auto">
                        Finden Sie Ihre Traumwohnung in unseren Beständen. Nutzen Sie unsere komfortable Online-Suche oder registrieren Sie Ihren Wohnungswunsch.
                    </flux:text>
                </div>

                {{-- Search Form --}}
                <div class="bg-gewo-grey-50 rounded-2xl p-6 lg:p-8 max-w-4xl mx-auto">
                    <form class="grid md:grid-cols-4 gap-4">
                        <flux:field>
                            <flux:label>Ort</flux:label>
                            <flux:select placeholder="Alle Orte">
                                <flux:select.option>Alle Orte</flux:select.option>
                                <flux:select.option>Freital</flux:select.option>
                                <flux:select.option>Bannewitz</flux:select.option>
                                <flux:select.option>Rabenau</flux:select.option>
                                <flux:select.option>Wilsdruff</flux:select.option>
                                <flux:select.option>Dresden</flux:select.option>
                            </flux:select>
                        </flux:field>
                        <flux:field>
                            <flux:label>Zimmer</flux:label>
                            <flux:select placeholder="Beliebig">
                                <flux:select.option>Beliebig</flux:select.option>
                                <flux:select.option>1 Zimmer</flux:select.option>
                                <flux:select.option>2 Zimmer</flux:select.option>
                                <flux:select.option>3 Zimmer</flux:select.option>
                                <flux:select.option>4+ Zimmer</flux:select.option>
                            </flux:select>
                        </flux:field>
                        <flux:field>
                            <flux:label>Max. Miete</flux:label>
                            <flux:input type="number" placeholder="€/Monat" />
                        </flux:field>
                        <div class="flex items-end">
                            <flux:button variant="primary" class="w-full" icon="magnifying-glass">
                                Suchen
                            </flux:button>
                        </div>
                    </form>
                </div>

                {{-- Featured Apartments --}}
                <div class="mt-12 grid md:grid-cols-3 gap-6">
                    @foreach([
                        ['location' => 'Freital-Döhlen', 'rooms' => '2 Zimmer', 'size' => '58 m²', 'rent' => '320'],
                        ['location' => 'Freital-Potschappel', 'rooms' => '3 Zimmer', 'size' => '72 m²', 'rent' => '410'],
                        ['location' => 'Bannewitz', 'rooms' => '4 Zimmer', 'size' => '95 m²', 'rent' => '520'],
                    ] as $apartment)
                        <flux:card>
                            <div class="aspect-video bg-linear-to-br from-gewo-grey-100 to-gewo-grey-50 rounded-lg mb-4 flex items-center justify-center">
                                <flux:icon name="home" class="size-12 text-gewo-grey-400" />
                            </div>
                            <flux:heading size="lg">{{ $apartment['location'] }}</flux:heading>
                            <flux:text size="sm" class="flex items-center gap-4 mt-2">
                                <span>{{ $apartment['rooms'] }}</span>
                                <span>•</span>
                                <span>{{ $apartment['size'] }}</span>
                            </flux:text>
                            <div class="mt-4 flex items-center justify-between">
                                <flux:heading class="text-xl text-accent">{{ $apartment['rent'] }} €<flux:text inline size="sm" class="font-normal">/Monat</flux:text></flux:heading>
                                <flux:button variant="outline" size="sm">Details</flux:button>
                            </div>
                        </flux:card>
                    @endforeach
                </div>

                <div class="text-center mt-8">
                    <flux:button variant="ghost" icon-trailing="arrow-right">
                        Alle Wohnungen anzeigen
                    </flux:button>
                </div>
            </div>
        </section>

        {{-- Services Section --}}
        <section id="service" class="py-16 lg:py-24 bg-gewo-grey-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <flux:heading size="xl" level="2">Service für Mieter</flux:heading>
                    <flux:text class="mt-4 max-w-2xl mx-auto">
                        Als Genossenschaftsmitglied profitieren Sie von umfassenden Serviceleistungen und einer starken Gemeinschaft.
                    </flux:text>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <flux:card class="text-center p-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gewo-blue-100 text-accent mb-4">
                            <flux:icon name="wrench-screwdriver" class="size-8" />
                        </div>
                        <flux:heading size="lg">Reparaturservice</flux:heading>
                        <flux:text class="mt-2">
                            Schnelle Hilfe bei Reparaturen und technischen Problemen in Ihrer Wohnung.
                        </flux:text>
                    </flux:card>

                    <flux:card class="text-center p-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gewo-blue-100 text-accent mb-4">
                            <flux:icon name="banknotes" class="size-8" />
                        </div>
                        <flux:heading size="lg">Rückvergütung</flux:heading>
                        <flux:text class="mt-2">
                            Jährliche Rückvergütung für Genossenschaftsmitglieder - 2024: 4% auf Ihre Einlage.
                        </flux:text>
                    </flux:card>

                    <flux:card class="text-center p-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gewo-blue-100 text-accent mb-4">
                            <flux:icon name="user-group" class="size-8" />
                        </div>
                        <flux:heading size="lg">Gemeinschaft</flux:heading>
                        <flux:text class="mt-2">
                            Regelmäßige Veranstaltungen und ein aktives Gemeinschaftsleben in unseren Wohnanlagen.
                        </flux:text>
                    </flux:card>

                    <flux:card class="text-center p-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gewo-blue-100 text-accent mb-4">
                            <flux:icon name="clipboard-document-list" class="size-8" />
                        </div>
                        <flux:heading size="lg">Online-Portal</flux:heading>
                        <flux:text class="mt-2">
                            Verwalten Sie Ihre Angelegenheiten bequem online - Dokumente, Anfragen und mehr.
                        </flux:text>
                    </flux:card>

                    <flux:card class="text-center p-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gewo-blue-100 text-accent mb-4">
                            <flux:icon name="shield-check" class="size-8" />
                        </div>
                        <flux:heading size="lg">Sicherheit</flux:heading>
                        <flux:text class="mt-2">
                            Lebenslanges Wohnrecht und Schutz vor Eigenbedarfskündigungen.
                        </flux:text>
                    </flux:card>

                    <flux:card class="text-center p-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gewo-blue-100 text-accent mb-4">
                            <flux:icon name="arrow-trending-up" class="size-8" />
                        </div>
                        <flux:heading size="lg">Modernisierung</flux:heading>
                        <flux:text class="mt-2">
                            Kontinuierliche Investitionen in Sanierung und moderne Ausstattung.
                        </flux:text>
                    </flux:card>
                </div>
            </div>
        </section>

        {{-- News Section --}}
        <section class="py-16 lg:py-24 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <flux:heading size="xl" level="2">Aktuelles</flux:heading>
                    <flux:button variant="ghost" icon-trailing="arrow-right">
                        Alle Neuigkeiten
                    </flux:button>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <flux:card>
                        <flux:badge color="sky" class="mb-3">Karriere</flux:badge>
                        <flux:heading size="lg">Wir suchen Verstärkung</flux:heading>
                        <flux:text class="mt-2">
                            Werden Sie Teil unseres Teams! Aktuelle Stellenangebote in verschiedenen Bereichen.
                        </flux:text>
                        <flux:button variant="ghost" size="sm" class="mt-4" icon-trailing="arrow-right">
                            Mehr erfahren
                        </flux:button>
                    </flux:card>

                    <flux:card>
                        <flux:badge color="emerald" class="mb-3">Mitglieder</flux:badge>
                        <flux:heading size="lg">Rückvergütung 2024: 4%</flux:heading>
                        <flux:text class="mt-2">
                            Freuen Sie sich auf Ihre Rückvergütung! Die Auszahlung erfolgt wie gewohnt im Frühjahr.
                        </flux:text>
                        <flux:button variant="ghost" size="sm" class="mt-4" icon-trailing="arrow-right">
                            Details ansehen
                        </flux:button>
                    </flux:card>

                    <flux:card>
                        <flux:badge color="amber" class="mb-3">Bauprojekte</flux:badge>
                        <flux:heading size="lg">Modernisierung abgeschlossen</flux:heading>
                        <flux:text class="mt-2">
                            Die Sanierungsarbeiten in der Poisentalstraße wurden erfolgreich abgeschlossen.
                        </flux:text>
                        <flux:button variant="ghost" size="sm" class="mt-4" icon-trailing="arrow-right">
                            Zum Projekt
                        </flux:button>
                    </flux:card>
                </div>
            </div>
        </section>

        {{-- About Section --}}
        <section id="ueber-uns" class="py-16 lg:py-24 bg-gewo-grey-900 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <flux:heading size="xl" level="2" class="text-white">Über uns</flux:heading>
                        <flux:text size="lg" class="mt-6 text-gewo-grey-300">
                            Die Freitaler Wohnungsgenossenschaft eG wurde 1954 gegründet und ist heute eine der größten Wohnungsgenossenschaften in der Region Dresden.
                        </flux:text>
                        <flux:text class="mt-4 text-gewo-grey-300">
                            Unser Ziel ist es, unseren Mitgliedern <strong class="text-white">Wohnungen mit modernem Standard zu bezahlbaren Preisen</strong> anzubieten. Mit einer durchschnittlichen Kaltmiete von 5,11 €/m² (Stand 2024) setzen wir dieses Versprechen täglich um.
                        </flux:text>
                        <flux:text class="mt-4 text-gewo-grey-300">
                            Als Genossenschaftsmitglied sind Sie nicht nur Mieter, sondern Miteigentümer und gestalten die Zukunft Ihres Wohnumfelds aktiv mit.
                        </flux:text>
                        <div class="mt-8">
                            <flux:button variant="primary" href="#kontakt" class="px-6! py-3!">
                                Kontakt aufnehmen
                            </flux:button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gewo-grey-800 rounded-xl p-6">
                            <flux:heading class="text-3xl text-accent">1954</flux:heading>
                            <flux:text class="text-gewo-grey-300 mt-1">Gründungsjahr</flux:text>
                        </div>
                        <div class="bg-gewo-grey-800 rounded-xl p-6">
                            <flux:heading class="text-3xl text-accent">6.000+</flux:heading>
                            <flux:text class="text-gewo-grey-300 mt-1">Mitglieder</flux:text>
                        </div>
                        <div class="bg-gewo-grey-800 rounded-xl p-6">
                            <flux:heading class="text-3xl text-accent">3.500+</flux:heading>
                            <flux:text class="text-gewo-grey-300 mt-1">Wohnungen</flux:text>
                        </div>
                        <div class="bg-gewo-grey-800 rounded-xl p-6">
                            <flux:heading class="text-3xl text-accent">5,11 €</flux:heading>
                            <flux:text class="text-gewo-grey-300 mt-1">Ø Miete/m²</flux:text>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Contact Section --}}
        <section id="kontakt" class="py-16 lg:py-24 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12">
                    <div>
                        <flux:heading size="xl" level="2">Kontakt</flux:heading>
                        <flux:text class="mt-4">
                            Haben Sie Fragen oder möchten Sie sich beraten lassen? Wir sind gerne für Sie da.
                        </flux:text>

                        <div class="mt-8 space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="shrink-0 w-12 h-12 rounded-full bg-gewo-blue-100 flex items-center justify-center">
                                    <flux:icon name="map-pin" class="size-6 text-accent" />
                                </div>
                                <div>
                                    <flux:heading size="base" class="font-semibold">Adresse</flux:heading>
                                    <flux:text>Dresdner Straße 84<br>01705 Freital</flux:text>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="shrink-0 w-12 h-12 rounded-full bg-gewo-blue-100 flex items-center justify-center">
                                    <flux:icon name="phone" class="size-6 text-accent" />
                                </div>
                                <div>
                                    <flux:heading size="base" class="font-semibold">Telefon</flux:heading>
                                    <flux:text>0351 6 49 76-0</flux:text>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="shrink-0 w-12 h-12 rounded-full bg-gewo-blue-100 flex items-center justify-center">
                                    <flux:icon name="envelope" class="size-6 text-accent" />
                                </div>
                                <div>
                                    <flux:heading size="base" class="font-semibold">E-Mail</flux:heading>
                                    <flux:text>info@gewo-freital.de</flux:text>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="shrink-0 w-12 h-12 rounded-full bg-gewo-blue-100 flex items-center justify-center">
                                    <flux:icon name="clock" class="size-6 text-accent" />
                                </div>
                                <div>
                                    <flux:heading size="base" class="font-semibold">Öffnungszeiten</flux:heading>
                                    <flux:text>
                                        Mo, Di, Do: 9:00 - 12:00 & 13:00 - 18:00 Uhr<br>
                                        Mi, Fr: 9:00 - 12:00 Uhr
                                    </flux:text>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <flux:card class="p-6 lg:p-8">
                            <flux:heading size="lg">Nachricht senden</flux:heading>
                            <form class="mt-6 space-y-4">
                                <div class="grid sm:grid-cols-2 gap-4">
                                    <flux:field>
                                        <flux:label>Vorname</flux:label>
                                        <flux:input placeholder="Max" />
                                    </flux:field>
                                    <flux:field>
                                        <flux:label>Nachname</flux:label>
                                        <flux:input placeholder="Mustermann" />
                                    </flux:field>
                                </div>
                                <flux:field>
                                    <flux:label>E-Mail</flux:label>
                                    <flux:input type="email" placeholder="max@beispiel.de" />
                                </flux:field>
                                <flux:field>
                                    <flux:label>Betreff</flux:label>
                                    <flux:select>
                                        <flux:select.option>Allgemeine Anfrage</flux:select.option>
                                        <flux:select.option>Wohnungssuche</flux:select.option>
                                        <flux:select.option>Mitgliedschaft</flux:select.option>
                                        <flux:select.option>Reparaturanfrage</flux:select.option>
                                        <flux:select.option>Sonstiges</flux:select.option>
                                    </flux:select>
                                </flux:field>
                                <flux:field>
                                    <flux:label>Nachricht</flux:label>
                                    <flux:textarea rows="4" placeholder="Ihre Nachricht..." />
                                </flux:field>
                                <flux:button variant="primary" class="w-full" icon="paper-airplane">
                                    Absenden
                                </flux:button>
                            </form>
                        </flux:card>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer class="bg-gewo-grey-900 text-white py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <svg width="32" height="32" viewBox="0 0 40 40" class="text-accent">
                            <circle cx="5" cy="5" r="3" fill="currentColor"/>
                            <circle cx="13" cy="5" r="3" fill="currentColor"/>
                            <circle cx="21" cy="5" r="3" fill="currentColor"/>
                            <circle cx="29" cy="5" r="3" fill="currentColor"/>
                            <circle cx="5" cy="13" r="3" fill="currentColor"/>
                            <circle cx="13" cy="13" r="3" fill="currentColor"/>
                            <circle cx="21" cy="13" r="3" fill="currentColor"/>
                            <circle cx="5" cy="21" r="3" fill="currentColor"/>
                            <circle cx="13" cy="21" r="3" fill="currentColor"/>
                            <circle cx="5" cy="29" r="3" fill="currentColor"/>
                            <rect x="24" y="16" width="12" height="6" rx="3" fill="#c8102e"/>
                        </svg>
                        <span class="text-accent font-bold">gewo</span>
                    </div>
                    <p class="text-gewo-grey-400 text-sm">
                        Freitaler Wohnungsgenossenschaft eG<br>
                        Dresdner Straße 84<br>
                        01705 Freital
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Navigation</h4>
                    <ul class="space-y-2 text-sm text-gewo-grey-400">
                        <li><a href="#wohnungen" class="hover:text-white transition-colors">Wohnungssuche</a></li>
                        <li><a href="#service" class="hover:text-white transition-colors">Service</a></li>
                        <li><a href="#ueber-uns" class="hover:text-white transition-colors">Über uns</a></li>
                        <li><a href="#kontakt" class="hover:text-white transition-colors">Kontakt</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Service</h4>
                    <ul class="space-y-2 text-sm text-gewo-grey-400">
                        <li><a href="#" class="hover:text-white transition-colors">Mieterportal</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Schadensmeldung</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Formulare</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">FAQ</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Rechtliches</h4>
                    <ul class="space-y-2 text-sm text-gewo-grey-400">
                        <li><a href="#" class="hover:text-white transition-colors">Impressum</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Datenschutz</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">AGB</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Barrierefreiheit</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gewo-grey-800 mt-8 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gewo-grey-400">
                    © {{ date('Y') }} Freitaler Wohnungsgenossenschaft eG. Alle Rechte vorbehalten.
                </p>
                <div class="flex items-center gap-2 text-sm text-gewo-grey-400">
                    <span>Schriftgröße:</span>
                    <button onclick="document.documentElement.setAttribute('data-font-size', 'small')" class="px-2 py-1 hover:bg-gewo-grey-800 rounded text-xs">A</button>
                    <button onclick="document.documentElement.setAttribute('data-font-size', 'normal')" class="px-2 py-1 hover:bg-gewo-grey-800 rounded text-sm">A</button>
                    <button onclick="document.documentElement.setAttribute('data-font-size', 'large')" class="px-2 py-1 hover:bg-gewo-grey-800 rounded text-base">A</button>
                </div>
            </div>
        </div>
    </footer>

    @fluxScripts
</body>
</html>
