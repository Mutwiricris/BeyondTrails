<?php

use App\Models\ActivityGamePack;
use App\Models\ActivityGameCard;
use App\Models\ActivityGameSession;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it can fetch activity game metadata with play styles and occasions', function () {
    $response = $this->getJson('/api/v1/discover/activities/games/metadata');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ])
        ->assertJsonStructure([
            'success',
            'occasions',
            'game_types',
            'play_styles',
        ]);

    $gameTypes = collect($response->json('game_types'))->pluck('id')->all();
    expect($gameTypes)->toContain('split_duel', 'shutter_snapshot', 'math_blitz', 'survival_quest', 'secret_roles');
});

test('it can list activity game packs and filter by play style and occasion', function () {
    $packDuel = ActivityGamePack::create([
        'slug'             => 'split-duel-test',
        'title'            => 'Split Duel Test',
        'subtitle'         => 'Test Subtitle',
        'description'      => 'Test description',
        'game_type'        => 'split_duel',
        'occasion'         => 'bbq',
        'tags'             => ['arcade', '2_player'],
        'duration_minutes' => 5,
    ]);

    $packSurvival = ActivityGamePack::create([
        'slug'             => 'survival-test',
        'title'            => 'Survival Test',
        'subtitle'         => 'Alpine test',
        'description'      => 'Test description',
        'game_type'        => 'survival_quest',
        'occasion'         => 'outdoor',
        'tags'             => ['survival', 'expedition'],
        'duration_minutes' => 15,
    ]);

    // All packs
    $resAll = $this->getJson('/api/v1/discover/activities/games/packs');
    $resAll->assertStatus(200)->assertJson(['success' => true]);
    expect(count($resAll->json('data')))->toBeGreaterThanOrEqual(2);

    // Filter by game_type=split_duel
    $resDuel = $this->getJson('/api/v1/discover/activities/games/packs?game_type=split_duel');
    $resDuel->assertStatus(200);
    $slugs = collect($resDuel->json('data'))->pluck('slug')->all();
    expect($slugs)->toContain('split-duel-test');
    expect($slugs)->not->toContain('survival-test');

    // Filter by occasion=outdoor
    $resOutdoor = $this->getJson('/api/v1/discover/activities/games/packs?occasion=outdoor');
    $resOutdoor->assertStatus(200);
    $outdoorSlugs = collect($resOutdoor->json('data'))->pluck('slug')->all();
    expect($outdoorSlugs)->toContain('survival-test');
    expect($outdoorSlugs)->not->toContain('split-duel-test');
});

test('it can fetch pack details with cards by slug', function () {
    $pack = ActivityGamePack::create([
        'slug'             => 'euro-pub-quiz-test',
        'title'            => 'Euro Pub Quiz Test',
        'game_type'        => 'trivia_taboo',
        'occasion'         => 'party',
        'tags'             => ['europe'],
        'metadata'         => ['region' => 'europe'],
        'duration_minutes' => 25,
    ]);

    ActivityGameCard::create([
        'pack_id'         => $pack->id,
        'category'        => 'Geography',
        'prompt'          => 'Capital of Hungary?',
        'answer'          => 'Budapest',
        'forbidden_words' => ['Danube', 'Pest'],
        'points'          => 10,
        'timer_seconds'   => 45,
    ]);

    $response = $this->getJson('/api/v1/discover/activities/games/packs/euro-pub-quiz-test');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data'    => [
                'slug'  => 'euro-pub-quiz-test',
                'title' => 'Euro Pub Quiz Test',
            ],
        ]);

    $cards = $response->json('data.cards');
    expect($cards)->toHaveCount(1);
    expect($cards[0]['answer'])->toBe('Budapest');
    expect($cards[0]['forbidden_words'])->toContain('Danube');
});

test('it records a completed game session', function () {
    $pack = ActivityGamePack::create([
        'slug'             => 'session-test-pack',
        'title'            => 'Session Test Pack',
        'game_type'        => 'reflex',
        'occasion'         => 'bbq',
        'duration_minutes' => 10,
    ]);

    $payload = [
        'pack_id'          => $pack->slug,
        'team_names'       => ['Team Safari', 'Team Serengeti'],
        'scores'           => ['Team Safari' => 14, 'Team Serengeti' => 11],
        'cards_played'     => 8,
        'duration_seconds' => 340,
        'metadata'         => ['winner' => 'Team Safari'],
    ];

    $response = $this->postJson('/api/v1/discover/activities/games/sessions', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Game session recorded successfully.',
        ]);

    expect(ActivityGameSession::where('pack_id', $pack->id)->count())->toBe(1);
});
