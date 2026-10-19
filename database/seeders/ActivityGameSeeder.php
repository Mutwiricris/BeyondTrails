<?php

namespace Database\Seeders;

use App\Models\ActivityGameCard;
use App\Models\ActivityGamePack;
use Illuminate\Database\Seeder;

class ActivityGameSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SPLIT-SCREEN TAP DUEL (2-Player Face-to-Face Arcade)
        $splitDuel = ActivityGamePack::updateOrCreate(
            ['slug' => 'split-screen-duel'],
            [
                'title'            => 'Split-Screen Tap Duel',
                'subtitle'         => 'Face-to-Face Simultaneous Arcade Battle',
                'description'      => 'Place the phone flat between two players! The top player is inverted. Fast-paced reflex cues appear—first to hit their buzzer scores. Avoid trap items or get frozen!',
                'game_type'        => 'split_duel',
                'occasion'         => 'bbq',
                'min_players'      => 2,
                'max_players'      => 2,
                'duration_minutes' => 5,
                'difficulty'       => 'arcade',
                'icon_name'        => 'touch_app',
                'color_hex'        => '#EF4444',
                'is_featured'      => true,
                'tags'             => ['arcade', '2_player', 'duel', 'reflex', 'face_to_face'],
                'rules'            => 'Both players rest their fingers on their respective buzzer pad. When a green valid target appears, tap instantly! If a red hazard/trap appears, stay frozen. First to 7 points claims victory.',
                'metadata'         => ['play_style' => 'split_duel', 'target_score' => 7],
            ]
        );

        $splitCards = [
            ['category' => 'Animal Kingdom', 'prompt' => 'Apex Predator: Lion / Leopard', 'answer' => 'TAP!', 'points' => 1, 'timer_seconds' => 2, 'metadata' => ['is_target' => true]],
            ['category' => 'Hazard Trap', 'prompt' => 'DANGER: Sleeping Honey Badger!', 'answer' => 'FREEZE!', 'points' => 1, 'timer_seconds' => 2, 'metadata' => ['is_target' => false]],
            ['category' => 'Trail Cuisine', 'prompt' => 'Campfire Sizzle: Nyama Choma', 'answer' => 'TAP!', 'points' => 1, 'timer_seconds' => 2, 'metadata' => ['is_target' => true]],
            ['category' => 'Hazard Trap', 'prompt' => 'TRAIL HAZARD: Loose Rolling Shale', 'answer' => 'FREEZE!', 'points' => 1, 'timer_seconds' => 2, 'metadata' => ['is_target' => false]],
            ['category' => 'Mountain Summit', 'prompt' => 'Summit Marker: Nelion Peak', 'answer' => 'TAP!', 'points' => 1, 'timer_seconds' => 2, 'metadata' => ['is_target' => true]],
            ['category' => 'Hazard Trap', 'prompt' => 'SUDDEN STORM: High Altitude Lightning', 'answer' => 'FREEZE!', 'points' => 1, 'timer_seconds' => 2, 'metadata' => ['is_target' => false]],
        ];

        foreach ($splitCards as $idx => $card) {
            ActivityGameCard::updateOrCreate(
                ['pack_id' => $splitDuel->id, 'prompt' => $card['prompt']],
                array_merge($card, ['sort_order' => $idx])
            );
        }

        // 2. WILDLIFE SHUTTER SNAPSHOT (Target Timing & Camera Lens)
        $shutterGame = ActivityGamePack::updateOrCreate(
            ['slug' => 'wildlife-shutter-snapshot'],
            [
                'title'            => 'Wildlife Shutter Reticle',
                'subtitle'         => 'Telephoto Timing & Shutter Speed Challenge',
                'description'      => 'A simulated DSLR camera viewfinder with moving wildlife targets, exposure reticle, and focus ring. Hit the shutter when the animal enters the focal crosshair for a Golden Shot!',
                'game_type'        => 'shutter_snapshot',
                'occasion'         => 'outdoor',
                'min_players'      => 1,
                'max_players'      => 8,
                'duration_minutes' => 10,
                'difficulty'       => 'precision',
                'icon_name'        => 'camera',
                'color_hex'        => '#06B6D4',
                'is_featured'      => true,
                'tags'             => ['camera', 'shutter', 'timing', 'safari', 'precision'],
                'rules'            => 'Track the moving target across the viewfinder frame. Press the red shutter trigger at the exact moment the subject aligns with the center reticle. Golden shots score 100 points.',
                'metadata'         => ['play_style' => 'shutter_snapshot', 'shutter_speed_ms' => 1200],
            ]
        );

        $shutterCards = [
            ['category' => 'Serengeti Sprinter', 'prompt' => 'Cheetah Burst (110 km/h)', 'hint' => 'Fast erratic diagonal speed', 'points' => 100, 'timer_seconds' => 3, 'metadata' => ['speed' => 2.2, 'size' => 45]],
            ['category' => 'Canopy Glider', 'prompt' => 'Crowned Hornbill Dive', 'hint' => 'Swooping arc across the upper third', 'points' => 80, 'timer_seconds' => 4, 'metadata' => ['speed' => 1.8, 'size' => 40]],
            ['category' => 'Savannah Giant', 'prompt' => 'Matriarch Elephant Crossing', 'hint' => 'Slow steady stroll across horizon', 'points' => 50, 'timer_seconds' => 5, 'metadata' => ['speed' => 0.9, 'size' => 70]],
            ['category' => 'Night Stalker', 'prompt' => 'Leopard on an Acacia Bough', 'hint' => 'Subtle sway in dusk lighting', 'points' => 120, 'timer_seconds' => 3, 'metadata' => ['speed' => 2.5, 'size' => 35]],
        ];

        foreach ($shutterCards as $idx => $card) {
            ActivityGameCard::updateOrCreate(
                ['pack_id' => $shutterGame->id, 'prompt' => $card['prompt']],
                array_merge($card, ['sort_order' => $idx])
            );
        }

        // 3. HIGHWAY 24: MATH BLITZ (Tactile Keypad Equation Puzzle)
        $mathBlitz = ActivityGamePack::updateOrCreate(
            ['slug' => 'highway-24-math-blitz'],
            [
                'title'            => 'Highway 24 Math Blitz',
                'subtitle'         => 'Tactile Road Trip Arithmetic Keypad',
                'description'      => 'Inspired by the highway plate game! 4 glowing number chips are dealt. Tap operators (+, -, ×, ÷) and digits to construct an equation equal to 24 before the combo timer drains.',
                'game_type'        => 'math_blitz',
                'occasion'         => 'roadtrip',
                'min_players'      => 1,
                'max_players'      => 6,
                'duration_minutes' => 12,
                'difficulty'       => 'puzzle',
                'icon_name'        => 'calculate',
                'color_hex'        => '#F59E0B',
                'is_featured'      => true,
                'tags'             => ['math', 'puzzle', 'roadtrip', 'brain', 'equations'],
                'rules'            => 'Use all 4 numbers exactly once. Build an equation that evaluates to 24. Chain rapid solves for a 3x combo multiplier.',
                'metadata'         => ['play_style' => 'math_blitz', 'target' => 24],
            ]
        );

        $mathCards = [
            ['category' => 'Highway Standard', 'prompt' => 'Numbers: 3, 8, 3, 8', 'answer' => '8 / (3 - 8/3) = 24', 'points' => 50, 'timer_seconds' => 60, 'metadata' => ['digits' => [3, 8, 3, 8]]],
            ['category' => 'Highway Rapid', 'prompt' => 'Numbers: 4, 6, 2, 7', 'answer' => '(7 - 4) * (6 + 2) = 24', 'points' => 30, 'timer_seconds' => 45, 'metadata' => ['digits' => [4, 6, 2, 7]]],
            ['category' => 'Highway Master', 'prompt' => 'Numbers: 1, 5, 5, 5', 'answer' => '(5 - 1/5) * 5 = 24', 'points' => 60, 'timer_seconds' => 60, 'metadata' => ['digits' => [1, 5, 5, 5]]],
            ['category' => 'Highway Classic', 'prompt' => 'Numbers: 2, 3, 4, 6', 'answer' => '(2 + 4) * (6 - 3) = 24', 'points' => 20, 'timer_seconds' => 30, 'metadata' => ['digits' => [2, 3, 4, 6]]],
        ];

        foreach ($mathCards as $idx => $card) {
            ActivityGameCard::updateOrCreate(
                ['pack_id' => $mathBlitz->id, 'prompt' => $card['prompt']],
                array_merge($card, ['sort_order' => $idx])
            );
        }

        // 4. ALPINE SURVIVAL SIMULATOR (Expedition Branching Story)
        $survivalGame = ActivityGamePack::updateOrCreate(
            ['slug' => 'alpine-survival-simulator'],
            [
                'title'            => 'Alpine Survival Simulator',
                'subtitle'         => 'Tactical Decision Engine & Health/Morale Gauges',
                'description'      => 'Face brutal high-altitude expedition dilemmas. Track Health (HP), Stamina, and Morale. Every tactical choice triggers consequences—will your team reach the summit camp?',
                'game_type'        => 'survival_quest',
                'occasion'         => 'outdoor',
                'min_players'      => 1,
                'max_players'      => 10,
                'duration_minutes' => 15,
                'difficulty'       => 'hard',
                'icon_name'        => 'health_and_safety',
                'color_hex'        => '#10B981',
                'is_featured'      => true,
                'tags'             => ['survival', 'simulation', 'gauges', 'tactics', 'expedition'],
                'rules'            => 'Monitor your 3 vital meters: Health, Stamina, and Morale. Choose your strategy when crises emerge. Keep all meters above zero to survive.',
                'metadata'         => ['play_style' => 'survival_quest', 'initial_hp' => 100, 'initial_stamina' => 100, 'initial_morale' => 100],
            ]
        );

        $survivalCards = [
            [
                'category' => 'Sudden Blizzard at 4,200m',
                'prompt'   => 'Visibility drops to 3 meters. Sleet is icing over your shell jackets. A cliff edge is near.',
                'hint'     => 'Option A: Dig snow bivouac (-15 Stamina, +10 Morale). Option B: Push through dark (-35 HP, -20 Morale). Option C: Anchor rope line (-10 Stamina, +5 HP).',
                'points'   => 40,
                'timer_seconds' => 45,
                'metadata' => [
                    'options' => [
                        ['label' => 'Dig emergency snow trench and deploy bivouac shelter', 'hp' => 0, 'stamina' => -15, 'morale' => 10, 'outcome' => 'Team stays protected from hypothermia.'],
                        ['label' => 'Rely on headlamps and push blindly along the ridge', 'hp' => -35, 'stamina' => -25, 'morale' => -20, 'outcome' => 'Two hikers slip on icy rocks, suffering sprains.'],
                        ['label' => 'Anchor a fixed rope guide-line and hold position', 'hp' => 5, 'stamina' => -10, 'morale' => 5, 'outcome' => 'Safe anchor established until wind subsides.'],
                    ]
                ]
            ],
            [
                'category' => 'Altitude Sickness on High Ridge',
                'prompt'   => 'Your navigator is showing signs of pulmonary edema: coughing, confusion, and stumbling.',
                'hint'     => 'Descent is the only true medicine for severe altitude sickness.',
                'points'   => 50,
                'timer_seconds' => 45,
                'metadata' => [
                    'options' => [
                        ['label' => 'Immediate staged descent of 600m down to valley', 'hp' => 20, 'stamina' => -20, 'morale' => 15, 'outcome' => 'Navigator recovers rapidly at lower altitude.'],
                        ['label' => 'Administer high-flow oxygen and wait at high camp', 'hp' => -15, 'stamina' => -10, 'morale' => -10, 'outcome' => 'Oxygen tanks deplete before storm clears.'],
                        ['label' => 'Split group: send 2 runners down for stretcher', 'hp' => -5, 'stamina' => -30, 'morale' => 0, 'outcome' => 'Rescue crew coordinates extraction.'],
                    ]
                ]
            ],
            [
                'category' => 'Glacial Crevasse Crossing',
                'prompt'   => 'A fragile snow bridge spans a 40-foot glacial fissure. The detour adds 4 hours.',
                'hint'     => 'Assess the risk vs time before twilight.',
                'points'   => 40,
                'timer_seconds' => 45,
                'metadata' => [
                    'options' => [
                        ['label' => 'Probe snow bridge with ice axe and belay across one by one', 'hp' => -5, 'stamina' => -10, 'morale' => 20, 'outcome' => 'Bridge holds safely under belay tension.'],
                        ['label' => 'Take the 4-hour rocky detour around the glacier toe', 'hp' => 0, 'stamina' => -30, 'morale' => -15, 'outcome' => 'Exhausting hike, but zero fall risk.'],
                    ]
                ]
            ]
        ];

        foreach ($survivalCards as $idx => $card) {
            ActivityGameCard::updateOrCreate(
                ['pack_id' => $survivalGame->id, 'prompt' => $card['prompt']],
                array_merge($card, ['sort_order' => $idx])
            );
        }

        // 5. CAMPFIRE SECRET ROLES (Biometric Fingerprint Terminal)
        $secretRoles = ActivityGamePack::updateOrCreate(
            ['slug' => 'campfire-secret-roles'],
            [
                'title'            => 'Campfire Secret Roles',
                'subtitle'         => 'Biometric Fingerprint Social Deduction Terminal',
                'description'      => 'A high-tech pass-the-phone party game. Each player presses and holds their thumb on the scanner to privately inspect their secret identity (Rangers, Rogue Poacher, Shaman). Then, the night tribunal begins!',
                'game_type'        => 'secret_roles',
                'occasion'         => 'party',
                'min_players'      => 5,
                'max_players'      => 15,
                'duration_minutes' => 20,
                'difficulty'       => 'social_deduction',
                'icon_name'        => 'fingerprint',
                'color_hex'        => '#8B5CF6',
                'is_featured'      => true,
                'tags'             => ['mafia', 'werewolf', 'roles', 'secret_identity', 'campfire'],
                'rules'            => 'Pass phone secretly. Hold thumb on sensor for 2 seconds to view role. Do not speak during the Night Phase. Debate and vote off suspicious players during the Day Tribunal.',
                'metadata'         => ['play_style' => 'secret_roles', 'scan_time_ms' => 1500],
            ]
        );

        $roleCards = [
            ['category' => 'Guardian', 'prompt' => 'Chief Trail Ranger', 'hint' => 'Wakes during Night Phase to inspect one player’s true alignment.', 'points' => 1, 'metadata' => ['team' => 'Rangers', 'ability' => 'Inspect Alignment']],
            ['category' => 'Infiltrator', 'prompt' => 'Rogue Poacher', 'hint' => 'Wakes during Night Phase with your partner to eliminate one camper.', 'points' => 1, 'metadata' => ['team' => 'Poachers', 'ability' => 'Night Strike']],
            ['category' => 'Medic', 'prompt' => 'Expedition Doctor', 'hint' => 'Protects one chosen player each night from being eliminated.', 'points' => 1, 'metadata' => ['team' => 'Rangers', 'ability' => 'Heal Shield']],
            ['category' => 'Wilderness Scout', 'prompt' => 'Silent Tracker', 'hint' => 'Wins if the Rangers identify and vote out all Rogue Poachers.', 'points' => 1, 'metadata' => ['team' => 'Rangers', 'ability' => 'Observation']],
        ];

        foreach ($roleCards as $idx => $card) {
            ActivityGameCard::updateOrCreate(
                ['pack_id' => $secretRoles->id, 'prompt' => $card['prompt']],
                array_merge($card, ['sort_order' => $idx])
            );
        }

        // 6. GEO-SCRAMBLE ANAGRAM RUSH (Tile Drag & Bomb Fuse)
        $scrambleGame = ActivityGamePack::updateOrCreate(
            ['slug' => 'geo-scramble-rush'],
            [
                'title'            => 'Geo-Scramble Anagram Rush',
                'subtitle'         => 'Tactile Letter Tiles & Fuse Countdown',
                'description'      => 'A ticking fuse timer threatens to detonate! Drag and tap scrambled letter tiles into the correct slots to spell iconic world expeditions and natural wonders before the blast.',
                'game_type'        => 'geo_scramble',
                'occasion'         => 'party',
                'min_players'      => 1,
                'max_players'      => 8,
                'duration_minutes' => 10,
                'difficulty'       => 'speed_puzzle',
                'icon_name'        => 'extension',
                'color_hex'        => '#EC4899',
                'is_featured'      => false,
                'tags'             => ['anagram', 'tiles', 'word_rush', 'countdown', 'puzzle'],
                'rules'            => 'Solve the jumbled word before the burning fuse reaches the end. Tap letter tiles to position them into slots.',
                'metadata'         => ['play_style' => 'geo_scramble', 'fuse_seconds' => 40],
            ]
        );

        $scrambleCards = [
            ['category' => 'Great Rift Wonder', 'prompt' => 'G O N O T L O N', 'answer' => 'LONGONOT', 'hint' => 'Crater volcano with a forested rim trail.', 'points' => 30, 'timer_seconds' => 40, 'metadata' => ['scrambled' => ['G','O','N','O','T','L','O','N'], 'solution' => 'LONGONOT']],
            ['category' => 'High Peaks', 'prompt' => 'M A K I J A L I N O R', 'answer' => 'KILIMANJARO', 'hint' => 'Rooftop of Africa, snowy dormant volcano.', 'points' => 40, 'timer_seconds' => 45, 'metadata' => ['scrambled' => ['M','A','K','I','J','A','L','I','N','O','R'], 'solution' => 'KILIMANJARO']],
            ['category' => 'Rift Escarpment', 'prompt' => 'V A E R H L E N L E T S G', 'answer' => 'HELLS GATE', 'hint' => 'Geothermal gorge with soaring basalt towers.', 'points' => 35, 'timer_seconds' => 45, 'metadata' => ['scrambled' => ['L','E','S','L','H','T','G','E','A'], 'solution' => 'HELLS GATE']],
        ];

        foreach ($scrambleCards as $idx => $card) {
            ActivityGameCard::updateOrCreate(
                ['pack_id' => $scrambleGame->id, 'prompt' => $card['prompt']],
                array_merge($card, ['sort_order' => $idx])
            );
        }

        // 7. SPOTTING MATRIX 3X3 (Tactile Observation Grid)
        $bingoGame = ActivityGamePack::updateOrCreate(
            ['slug' => 'savannah-safari-bingo'],
            [
                'title'            => 'Spotting Matrix 3x3',
                'subtitle'         => 'Tactile Sighting Grid with Laser Line Detection',
                'description'      => 'A 9-cell tactile observation grid for wildlife drives and trail hikes. Tapping checks off sightings with mechanical spring animations and triggers laser line-completion effects.',
                'game_type'        => 'bingo',
                'occasion'         => 'outdoor',
                'min_players'      => 1,
                'max_players'      => 10,
                'duration_minutes' => 45,
                'difficulty'       => 'all_levels',
                'icon_name'        => 'grid_view',
                'color_hex'        => '#10B981',
                'is_featured'      => false,
                'tags'             => ['observation', 'matrix', '3x3', 'safari', 'hike'],
                'rules'            => 'Tap items as they appear in the wild. Complete any horizontal, vertical, or diagonal line to claim the Explorer Badge.',
                'metadata'         => ['play_style' => 'bingo', 'grid_size' => 3],
            ]
        );

        $bingoCards = [
            ['prompt' => 'Acacia Umbrella Canopy', 'category' => 'Flora', 'points' => 10],
            ['prompt' => 'Crowned Crane Pair', 'category' => 'Birds', 'points' => 15],
            ['prompt' => 'Troop of Olive Baboons', 'category' => 'Wildlife', 'points' => 10],
            ['prompt' => 'Zebra Band Crossing Dirt Track', 'category' => 'Wildlife', 'points' => 15],
            ['prompt' => 'Swirling Red Soil Dust Devil', 'category' => 'Weather', 'points' => 20],
            ['prompt' => 'Termite Spire Taller Than a Car', 'category' => 'Architecture', 'points' => 15],
            ['prompt' => 'Tower of Giraffes Browsing Thorns', 'category' => 'Wildlife', 'points' => 25],
            ['prompt' => 'Rift Valley Escarpment Viewpoint', 'category' => 'Landmark', 'points' => 20],
            ['prompt' => 'Weaver Bird Colony in Thorns', 'category' => 'Birds', 'points' => 15],
        ];

        foreach ($bingoCards as $idx => $card) {
            ActivityGameCard::updateOrCreate(
                ['pack_id' => $bingoGame->id, 'prompt' => $card['prompt']],
                array_merge($card, ['sort_order' => $idx])
            );
        }

        // 8. PASS & PLAY CONSOLE DUEL (Taboo & Acting Console)
        $consoleDuel = ActivityGamePack::updateOrCreate(
            ['slug' => 'pass-play-console-duel'],
            [
                'title'            => 'Pass & Play Console Duel',
                'subtitle'         => '3D Flip Console with Taboo Filter & Team HUD',
                'description'      => 'A gamified console showdown! Team A vs Team B. Describe world landmarks, cultural slang, and epic adventures without uttering the 4 forbidden words.',
                'game_type'        => 'trivia_taboo',
                'occasion'         => 'party',
                'min_players'      => 4,
                'max_players'      => 20,
                'duration_minutes' => 15,
                'difficulty'       => 'medium',
                'icon_name'        => 'style',
                'color_hex'        => '#3B82F6',
                'is_featured'      => false,
                'tags'             => ['taboo', 'console', 'acting', 'team_battle', 'party'],
                'rules'            => 'One team describer has 60s on the console timer to get their team to guess the subject without saying any forbidden words.',
                'metadata'         => ['play_style' => 'trivia_taboo', 'timer_default' => 60],
            ]
        );

        $consoleCards = [
            ['category' => 'Great Summit', 'prompt' => 'Mount Kenya', 'answer' => 'Mount Kenya', 'forbidden_words' => ['Nelion', 'Batian', 'Peak', 'Snow'], 'hint' => 'Sacred peak in the heart of East Africa.', 'points' => 1, 'timer_seconds' => 60],
            ['category' => 'World Wonder', 'prompt' => 'Taj Mahal', 'answer' => 'Taj Mahal', 'forbidden_words' => ['India', 'Marble', 'Agra', 'Tomb'], 'hint' => 'Ivory-white marble mausoleum on the Yamuna river.', 'points' => 1, 'timer_seconds' => 60],
            ['category' => 'Engineering', 'prompt' => 'Eiffel Tower', 'answer' => 'Eiffel Tower', 'forbidden_words' => ['Paris', 'France', 'Iron', 'Lattice'], 'hint' => 'Monument on the Champ de Mars.', 'points' => 1, 'timer_seconds' => 60],
            ['category' => 'Urban Vibe', 'prompt' => 'Nganya Culture', 'answer' => 'Nganya / Matatu', 'forbidden_words' => ['Bus', 'Route', 'Music', 'Conductor'], 'hint' => 'Souped-up public transit with artwork and bass.', 'points' => 2, 'timer_seconds' => 60],
        ];

        foreach ($consoleCards as $idx => $card) {
            ActivityGameCard::updateOrCreate(
                ['pack_id' => $consoleDuel->id, 'prompt' => $card['prompt']],
                array_merge($card, ['sort_order' => $idx])
            );
        }
    }
}
