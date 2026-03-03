<?php

namespace Database\Seeders;

use App\Enums\BlockType;
use App\Enums\TemplateCategory;
use App\Models\PageTemplate;
use Illuminate\Database\Seeder;

class PageTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Landing Page',
                'slug' => 'landing-page',
                'description' => 'Eine moderne Landing Page mit Hero, Features und Call-to-Action',
                'category' => TemplateCategory::Landing,
                'structure' => [
                    [
                        'type' => BlockType::Hero->value,
                        'content' => [
                            'heading' => 'Willkommen bei uns',
                            'subheading' => 'Entdecken Sie unsere Dienstleistungen und erfahren Sie, wie wir Ihnen helfen können.',
                            'image' => '',
                            'cta_text' => 'Mehr erfahren',
                            'cta_url' => '#',
                        ],
                    ],
                    [
                        'type' => BlockType::Spacer->value,
                        'content' => ['height' => 'lg'],
                    ],
                    [
                        'type' => BlockType::Heading->value,
                        'content' => ['text' => 'Unsere Leistungen', 'level' => 2],
                        'settings' => ['text_align' => 'center'],
                    ],
                    [
                        'type' => BlockType::FeatureGrid->value,
                        'content' => [
                            'features' => [
                                ['icon' => 'home', 'title' => 'Wohnungsvermietung', 'description' => 'Finden Sie Ihr neues Zuhause'],
                                ['icon' => 'key', 'title' => 'Schlüsselfertig', 'description' => 'Alles aus einer Hand'],
                                ['icon' => 'shield-check', 'title' => 'Sicherheit', 'description' => 'Geprüfte Qualität'],
                            ],
                        ],
                    ],
                    [
                        'type' => BlockType::Spacer->value,
                        'content' => ['height' => 'lg'],
                    ],
                    [
                        'type' => BlockType::Testimonials->value,
                        'content' => [
                            'testimonials' => [
                                [
                                    'quote' => 'Hervorragender Service! Wir sind sehr zufrieden.',
                                    'name' => 'Max Mustermann',
                                    'role' => 'Mieter seit 2022',
                                    'avatar' => '',
                                ],
                                [
                                    'quote' => 'Die Zusammenarbeit war unkompliziert und professionell.',
                                    'name' => 'Maria Musterfrau',
                                    'role' => 'Mieterin seit 2021',
                                    'avatar' => '',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => BlockType::Spacer->value,
                        'content' => ['height' => 'lg'],
                    ],
                    [
                        'type' => BlockType::Cta->value,
                        'content' => [
                            'heading' => 'Bereit loszulegen?',
                            'content' => 'Kontaktieren Sie uns noch heute für ein unverbindliches Beratungsgespräch.',
                            'button_text' => 'Kontakt aufnehmen',
                            'button_url' => '/kontakt',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Über uns',
                'slug' => 'ueber-uns',
                'description' => 'Eine Seite zur Vorstellung Ihres Unternehmens',
                'category' => TemplateCategory::About,
                'structure' => [
                    [
                        'type' => BlockType::Hero->value,
                        'content' => [
                            'heading' => 'Über uns',
                            'subheading' => 'Lernen Sie unser Team und unsere Geschichte kennen',
                            'image' => '',
                            'cta_text' => '',
                            'cta_url' => '',
                        ],
                    ],
                    [
                        'type' => BlockType::Spacer->value,
                        'content' => ['height' => 'md'],
                    ],
                    [
                        'type' => BlockType::Heading->value,
                        'content' => ['text' => 'Unsere Geschichte', 'level' => 2],
                    ],
                    [
                        'type' => BlockType::RichText->value,
                        'content' => [
                            'html' => '<p>Hier können Sie die Geschichte Ihres Unternehmens erzählen. Was hat Sie motiviert? Was sind Ihre Werte? Was macht Sie einzigartig?</p><p>Teilen Sie Ihre Vision und Mission mit Ihren Besuchern.</p>',
                        ],
                    ],
                    [
                        'type' => BlockType::Spacer->value,
                        'content' => ['height' => 'md'],
                    ],
                    [
                        'type' => BlockType::Heading->value,
                        'content' => ['text' => 'Unsere Werte', 'level' => 2],
                    ],
                    [
                        'type' => BlockType::FeatureGrid->value,
                        'content' => [
                            'features' => [
                                ['icon' => 'heart', 'title' => 'Kundenorientierung', 'description' => 'Der Kunde steht bei uns im Mittelpunkt'],
                                ['icon' => 'sparkles', 'title' => 'Qualität', 'description' => 'Wir setzen auf höchste Standards'],
                                ['icon' => 'users', 'title' => 'Teamarbeit', 'description' => 'Gemeinsam sind wir stark'],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Kontakt',
                'slug' => 'kontakt',
                'description' => 'Eine Kontaktseite mit Formular',
                'category' => TemplateCategory::Contact,
                'structure' => [
                    [
                        'type' => BlockType::Heading->value,
                        'content' => ['text' => 'Kontakt', 'level' => 1],
                        'settings' => ['text_align' => 'center'],
                    ],
                    [
                        'type' => BlockType::Paragraph->value,
                        'content' => ['text' => 'Haben Sie Fragen? Wir freuen uns auf Ihre Nachricht!'],
                        'settings' => ['text_align' => 'center'],
                    ],
                    [
                        'type' => BlockType::Spacer->value,
                        'content' => ['height' => 'md'],
                    ],
                    [
                        'type' => BlockType::ContactForm->value,
                        'content' => [
                            'recipient_email' => '',
                            'success_message' => 'Vielen Dank für Ihre Nachricht! Wir werden uns schnellstmöglich bei Ihnen melden.',
                        ],
                    ],
                    [
                        'type' => BlockType::Spacer->value,
                        'content' => ['height' => 'lg'],
                    ],
                    [
                        'type' => BlockType::Card->value,
                        'content' => [
                            'title' => 'Unsere Adresse',
                            'content' => "Musterstraße 123\n12345 Musterstadt\n\nTelefon: +49 123 456789\nE-Mail: info@example.de",
                            'image' => '',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'FAQ',
                'slug' => 'faq',
                'description' => 'Häufig gestellte Fragen',
                'category' => TemplateCategory::Content,
                'structure' => [
                    [
                        'type' => BlockType::Heading->value,
                        'content' => ['text' => 'Häufig gestellte Fragen', 'level' => 1],
                        'settings' => ['text_align' => 'center'],
                    ],
                    [
                        'type' => BlockType::Paragraph->value,
                        'content' => ['text' => 'Hier finden Sie Antworten auf die häufigsten Fragen.'],
                        'settings' => ['text_align' => 'center'],
                    ],
                    [
                        'type' => BlockType::Spacer->value,
                        'content' => ['height' => 'md'],
                    ],
                    [
                        'type' => BlockType::Faq->value,
                        'content' => [
                            'items' => [
                                ['question' => 'Wie kann ich eine Wohnung mieten?', 'answer' => 'Sie können sich direkt über unsere Website auf verfügbare Wohnungen bewerben. Klicken Sie einfach auf die gewünschte Wohnung und füllen Sie das Bewerbungsformular aus.'],
                                ['question' => 'Welche Unterlagen werden benötigt?', 'answer' => 'In der Regel benötigen wir einen Personalausweis, Einkommensnachweise der letzten drei Monate und eine SCHUFA-Auskunft.'],
                                ['question' => 'Wie lange dauert der Bewerbungsprozess?', 'answer' => 'Nach Eingang Ihrer Unterlagen erhalten Sie in der Regel innerhalb von 5-7 Werktagen eine Rückmeldung.'],
                                ['question' => 'Sind Haustiere erlaubt?', 'answer' => 'Dies variiert je nach Wohnung. Die entsprechenden Informationen finden Sie in der jeweiligen Wohnungsanzeige.'],
                            ],
                        ],
                    ],
                    [
                        'type' => BlockType::Spacer->value,
                        'content' => ['height' => 'lg'],
                    ],
                    [
                        'type' => BlockType::Callout->value,
                        'content' => [
                            'type' => 'info',
                            'title' => 'Weitere Fragen?',
                            'content' => 'Falls Ihre Frage hier nicht beantwortet wurde, kontaktieren Sie uns gerne direkt.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Impressum',
                'slug' => 'impressum',
                'description' => 'Rechtliche Informationen und Impressum',
                'category' => TemplateCategory::Legal,
                'structure' => [
                    [
                        'type' => BlockType::Heading->value,
                        'content' => ['text' => 'Impressum', 'level' => 1],
                    ],
                    [
                        'type' => BlockType::Heading->value,
                        'content' => ['text' => 'Angaben gemäß § 5 TMG', 'level' => 2],
                    ],
                    [
                        'type' => BlockType::RichText->value,
                        'content' => [
                            'html' => '<p><strong>Firmenname GmbH</strong><br>Musterstraße 123<br>12345 Musterstadt</p><p><strong>Vertreten durch:</strong><br>Max Mustermann (Geschäftsführer)</p><p><strong>Kontakt:</strong><br>Telefon: +49 123 456789<br>E-Mail: info@example.de</p>',
                        ],
                    ],
                    [
                        'type' => BlockType::Heading->value,
                        'content' => ['text' => 'Registereintrag', 'level' => 2],
                    ],
                    [
                        'type' => BlockType::RichText->value,
                        'content' => [
                            'html' => '<p>Eintragung im Handelsregister.<br>Registergericht: Amtsgericht Musterstadt<br>Registernummer: HRB 12345</p>',
                        ],
                    ],
                    [
                        'type' => BlockType::Heading->value,
                        'content' => ['text' => 'Umsatzsteuer-ID', 'level' => 2],
                    ],
                    [
                        'type' => BlockType::Paragraph->value,
                        'content' => ['text' => 'Umsatzsteuer-Identifikationsnummer gemäß §27 a Umsatzsteuergesetz: DE123456789'],
                    ],
                    [
                        'type' => BlockType::Heading->value,
                        'content' => ['text' => 'Haftungsausschluss', 'level' => 2],
                    ],
                    [
                        'type' => BlockType::RichText->value,
                        'content' => [
                            'html' => '<p><strong>Haftung für Inhalte:</strong></p><p>Die Inhalte unserer Seiten wurden mit größter Sorgfalt erstellt. Für die Richtigkeit, Vollständigkeit und Aktualität der Inhalte können wir jedoch keine Gewähr übernehmen.</p>',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($templates as $template) {
            PageTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
