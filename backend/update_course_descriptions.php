<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Course;

// Update the YouTube Algorithm course
$course = Course::where('slug', 'youtubealgoritmus')->first();

if ($course) {
    $course->update([
        'description' => 'Tento kurz vám odhalí tajomstvá YouTube algoritmu a naučí vás, ako ho využiť vo váš prospech. Pochopíte, ako YouTube rozhoduje o tom, ktoré videá odporúča divákom a ako môžete optimalizovať svoj obsah pre maximálny dosah. Naučíte sa analyzovať metriky, ktoré YouTube sleduje, a implementovať stratégie, ktoré zvýšia vašu viditeľnosť a engagement. Kurz je postavený na aktuálnych poznatkov a osvedčených postupoch úspešných tvorcov.',
        'short_description' => 'Odhaľte tajomstvá YouTube algoritmu a naučte sa, ako optimalizovať váš obsah pre maximálny dosah a rast kanála.',
        'what_you_will_learn' => json_encode([
            'Ako funguje YouTube algoritmus a čo ovplyvňuje odporúčania videí',
            'Stratégie na zvýšenie CTR (Click-Through Rate) a watch time',
            'Optimalizácia názvov, popisov a tagov pre lepšiu viditeľnosť',
            'Ako využiť YouTube Analytics na rast kanála',
            'Techniky na budovanie lojálneho publika',
            'Najlepšie postupy pre konzistentnú tvorbu obsahu'
        ]),
        'requirements' => json_encode([
            'Základné znalosti práce s YouTube platformou',
            'Vlastný YouTube kanál (alebo ochota si ho vytvoriť)',
            'Záujem o tvorbu video obsahu a rast kanála'
        ])
    ]);
    
    echo "✓ Updated: {$course->title}\n";
}

// Update other courses
$courses = [
    [
        'slug' => 'youtube-channel-growth-masterclass',
        'description' => 'Komplexný kurz zameraný na rast YouTube kanála od nuly po 100K+ odberateľov. Naučíte sa vytvárať virálny obsah, budovať komunitu a monetizovať váš kanál. Kurz pokrýva všetko od stratégie obsahu, cez optimalizáciu videí, až po pokročilé marketingové techniky. Získate prístup k osvedčeným postupom, ktoré používajú najúspešnejší YouTuberi.',
        'short_description' => 'Naučte sa stratégie rastu YouTube kanála používané top tvorcami.',
        'what_you_will_learn' => [
            'Stratégie pre rýchly a udržateľný rast kanála',
            'Tvorba virálneho obsahu, ktorý rezonuje s publikom',
            'Pokročilé techniky SEO pre YouTube',
            'Budovanie a zapojenie komunity',
            'Monetizačné stratégie a diverzifikácia príjmov',
            'Analýza konkurencie a trhových trendov'
        ],
        'requirements' => [
            'Základné porozumenie YouTube platformy',
            'YouTube kanál (alebo pripravenosť si ho vytvoriť)',
            'Počítač s internetovým pripojením',
            'Odhodlanie venovať čas rastu kanála'
        ]
    ],
    [
        'slug' => 'video-editing-pro-beginner-to-expert',
        'description' => 'Od základov po pokročilé techniky - tento kurz vás naučí všetko o profesionálnom strihání videa. Pracovať budete s industry-standard nástrojmi a naučíte sa vytvárať vizuálne pútavý obsah, ktorý zaujme divákov. Kurz pokrýva color grading, audio mixing, efekty, transitions a storytelling cez strih. Získate praktické zručnosti, ktoré môžete okamžite aplikovať.',
        'short_description' => 'Ovládnite profesionálny strih videa s industry-standard technikami.',
        'what_you_will_learn' => [
            'Základy a pokročilé techniky strihu videa',
            'Práca s profesionálnym strihacím softvérom',
            'Color grading a color correction',
            'Audio mixing a sound design',
            'Vizuálne efekty a transitions',
            'Storytelling cez strih a pacing'
        ],
        'requirements' => [
            'Počítač s dostatočným výkonom pre strih videa',
            'Základné počítačové zručnosti',
            'Strihací softvér (odporúčania v kurze)',
            'Chuť učiť sa a experimentovať'
        ]
    ],
    [
        'slug' => 'youtube-monetization-blueprint',
        'description' => 'Naučte sa efektívne monetizovať váš YouTube kanál a vytvoriť udržateľný príjem z tvorby obsahu. Kurz pokrýva všetky aspekty monetizácie - od YouTube Partner Program, cez sponzorstvá, až po vlastné produkty a služby. Získate praktické stratégie na maximalizáciu príjmov pri zachovaní kvality obsahu a dôvery publika.',
        'short_description' => 'Naučte sa monetizovať YouTube kanál a vytvoriť udržateľný príjem.',
        'what_you_will_learn' => [
            'Ako sa kvalifikovať a optimalizovať YouTube Partner Program',
            'Stratégie pre získavanie sponzorstiev a brand deals',
            'Tvorba a predaj vlastných produktov a služieb',
            'Affiliate marketing pre YouTube tvorcov',
            'Diverzifikácia príjmových tokov',
            'Právne a daňové aspekty monetizácie'
        ],
        'requirements' => [
            'Aktívny YouTube kanál s nejakým obsahom',
            'Základné porozumenie YouTube platformy',
            'Ochota investovať čas do rastu kanála'
        ]
    ],
    [
        'slug' => 'content-creation-for-beginners',
        'description' => 'Perfektný štartovací bod pre začínajúcich tvorcov obsahu. Tento kurz vás prevedie celým procesom tvorby obsahu - od nápadu, cez produkciu, až po publikáciu a propagáciu. Naučíte sa základy, ktoré potrebujete na vytvorenie kvalitného obsahu bez drahého vybavenia. Kurz je prakticky zameraný s množstvom príkladov a cvičení.',
        'short_description' => 'Perfektný štartovací bod pre začínajúcich tvorcov obsahu.',
        'what_you_will_learn' => [
            'Základy tvorby video obsahu',
            'Plánovanie a scripting videí',
            'Natáčanie s dostupným vybavením',
            'Základy strihu a post-produkcie',
            'Publikácia a optimalizácia obsahu',
            'Budovanie konzistentnej tvorby'
        ],
        'requirements' => [
            'Smartphone alebo kamera',
            'Základné počítačové zručnosti',
            'Kreativita a chuť tvoriť',
            'Žiadne predchádzajúce skúsenosti nie sú potrebné'
        ]
    ]
];

foreach ($courses as $courseData) {
    $course = Course::where('slug', $courseData['slug'])->first();
    if ($course) {
        $course->update([
            'description' => $courseData['description'],
            'short_description' => $courseData['short_description'],
            'what_you_will_learn' => json_encode($courseData['what_you_will_learn']),
            'requirements' => json_encode($courseData['requirements'])
        ]);
        echo "✓ Updated: {$course->title}\n";
    }
}

echo "\n✅ All courses updated successfully!\n";
