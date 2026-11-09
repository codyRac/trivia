<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Single row holding the state of the scavenger hunt
        Schema::create('hunts', function (Blueprint $table) {
            $table->id();
            $table->text('intro')->nullable();
            // null = not started, 0 = intro screen, 1..n = that stop, n+1 = finished
            $table->unsignedInteger('position')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamps();
        });

        Schema::create('hunt_stops', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('position'); // order of the stop in the hunt
            $table->string('title');
            $table->string('extra')->nullable(); // note shown alongside the first clue
            $table->json('clues'); // 3 clues, hardest first
            $table->string('answer');
            $table->unsignedTinyInteger('clues_revealed')->default(1); // 1-3 = clues, 4 = answer
            $table->unsignedInteger('points')->nullable(); // set when the stop is completed
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        $now = now();

        DB::table('hunts')->insert([
            'intro' => "Your Birthday Scavenger Hunt starts now!\n\n"
                . "Also we will be ending at fine dining so dress up, you have an hour time is a ticking\n\n"
                . "(of course I wanted to not tell you about the fine dining but they have a like cocktail attire I called and they basically said suit or nothing for me)\n\n"
                . "(Also Also yes I know the niner game is on do not worry about that)",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $stops = [
            [
                'title' => 'Broadway Palace',
                'extra' => 'You will have a friend joining for this spot',
                'clues' => [
                    "Before we had a thousand memories, we had one address. It wasn't just where we slept — it was where two separate stories started sharing the same pages.",
                    'Forget castles and crowns. Our first little kingdom stood on a street famous for its theaters, and we were the stars of our own show.',
                    'Our first home together had a royal name, even though neither of us was royalty. Head back to our old place on Broadway.',
                ],
                'answer' => 'Broadway Palace',
            ],
            [
                'title' => 'DTLA Ramen',
                'extra' => null,
                'clues' => [
                    'Some relationships are built on candlelit dinners. Ours was apparently built on returning to the same place and ordering something we\'d already had a hundred times.',
                    'A little steam, a lot of slurping, and two people who clearly never got tired of the same meal. We probably should have bought stock in noodles.',
                    'Think bowls, broth, chopsticks, and the place downtown where we ate ramen more than anywhere else.',
                ],
                'answer' => 'DTLA Ramen',
            ],
            [
                'title' => 'Pershing Square',
                'extra' => 'You will have a friend joining for this spot as well',
                'clues' => [
                    "In a city of towers, concrete, and lights,\nThere's a place in the middle that offers a sight.\nWhere footsteps cross paths and the busy world flows,\nA little patch of calm where the city still grows.",
                    "Surrounded by buildings, but open to the sky,\nA gathering place where the crowds wander by.\nNot quite a garden, not quite a street,\nA downtown landmark where strangers all meet.",
                    "Four sides surround it, but no walls are found,\nA famous downtown park in the heart of town.\nNamed for a general from World War One,\nFind this historic square, and your next stop is done.",
                ],
                'answer' => 'Pershing Square — Downtown Los Angeles',
            ],
            [
                'title' => 'Angels Flight',
                'extra' => null,
                'clues' => [
                    "We've gone a lot of places together, but this particular adventure was almost over before it started. Sometimes the smallest journeys leave the funniest memories.",
                    'It had tracks, cars, and a destination, but you could have walked the whole journey in a couple of minutes. At least the hill made it interesting.',
                    "Two little railway cars climb Bunker Hill. It's a historic downtown funicular with a heavenly name.",
                ],
                'answer' => 'Angels Flight Railway',
            ],
            [
                'title' => 'Grand Central Market',
                'extra' => null,
                'clues' => [
                    "One evening, someone on a screen made a very convincing argument, without speaking directly to us. Suddenly, watching wasn't enough. We had to taste something.",
                    'Smoke, meat, and a television show started the craving. We followed our stomachs to a place where there were almost too many food choices.',
                    'We watched a barbecue show and went looking for BBQ at the famous food hall on Broadway, filled with vendors and delicious smells.',
                ],
                'answer' => 'Grand Central Market',
            ],
            [
                'title' => 'Alamo Drafthouse Cinema',
                'extra' => null,
                'clues' => [
                    "We've been on adventures without leaving our seats,\nThrough stories of heroes, villains, and feats.\nThe lights disappear, the real world fades away,\nAnd for a couple of hours, we both get to stay.",
                    "Dinner and a show, but all in one place,\nWith popcorn and drinks delivered right to our space.\nNo talking, no texting, just sit back and see,\nWhere the big screen brings stories to you and me.",
                    "Remember the Alamo? Well, not quite that one,\nThis downtown theater is all about fun.\nWith movies and meals delivered to your chair,\nFind the cinema where we've watched stories together there.",
                ],
                'answer' => 'Alamo Drafthouse Cinema — Downtown Los Angeles',
            ],
            [
                'title' => 'Spire 73',
                'extra' => null,
                'clues' => [
                    "Today we've followed the memories we've made. But to finish the story, we need to go somewhere that existed before we knew what our story would become.",
                    'Before the shared home, the countless bowls of noodles, and all our adventures, there was a date high above the city. We had no idea what was coming.',
                    "Go back to our very first date, at the rooftop bar on the 73rd floor of the InterContinental. I'll be waiting where it all began. ❤️",
                ],
                'answer' => 'Spire 73',
            ],
        ];

        foreach ($stops as $index => $stop) {
            DB::table('hunt_stops')->insert([
                'position' => $index + 1,
                'title' => $stop['title'],
                'extra' => $stop['extra'],
                'clues' => json_encode($stop['clues']),
                'answer' => $stop['answer'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hunt_stops');
        Schema::dropIfExists('hunts');
    }
};
