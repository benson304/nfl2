<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->boolean('is_admin')->default(false);
            $table->timestamps();
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();
            $table->string('name');
            $table->string('abbreviation');
            $table->boolean('is_playoff_team')->default(false);
            $table->timestamps();
        });

        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('team_id')->constrained();
            $table->string('position');
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('active');
            $table->string('external_id')->nullable();
            $table->timestamps();
        });

        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('entry_name');
            $table->integer('changes_remaining')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('entry_player', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->string('roster_position');
            $table->integer('wildcard_points')->nullable();
            $table->integer('divisional_points')->nullable();
            $table->integer('conference_points')->nullable();
            $table->integer('superbowl_points')->nullable();
            $table->integer('total_points')->nullable();
            $table->timestamps();
        });

//        Schema::create('entry_player_history', function (Blueprint $table) {
//            $table->id();
//            $table->foreignId('entry_id')->constrained();
//            $table->foreignId('player_id')->constrained();
//            $table->string('roster_position');
//            $table->decimal('wildcard_points', 8, 1)->default(0.0);
//            $table->decimal('divisional_points', 8, 1)->default(0.0);
//            $table->decimal('conference_points', 8, 1)->default(0.0);
//            $table->decimal('superbowl_points', 8, 1)->default(0.0);
//            $table->decimal('total_points', 8, 1)->default(0.0);
//            $table->timestamp('removed_at')->nullable();
//            $table->timestamps();
//        });
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_team_id')->constrained('teams');
            $table->foreignId('away_team_id')->constrained('teams');
            $table->datetime('kickoff');
            $table->enum('round', ['Wild Card', 'Divisional', 'Conference', 'Super Bowl']);
            $table->enum('status', ['scheduled', 'in_progress', 'finished'])->default('scheduled');
            $table->integer('home_score')->default(0);
            $table->integer('away_score')->default(0);
            $table->timestamps();
        });

        Schema::create('player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->integer('passing_yards')->default(0);
            $table->integer('passing_tds')->default(0);
            $table->integer('interceptions')->default(0);
            $table->integer('rushing_yards')->default(0);
            $table->integer('rushing_tds')->default(0);
            $table->integer('receptions')->default(0);
            $table->integer('receiving_yards')->default(0);
            $table->integer('receiving_tds')->default(0);
            $table->integer('two_point_conversions')->default(0);
            $table->integer('fumbles_lost')->default(0);
            $table->integer('offensive_fumble_return_td')->default(0);
            $table->boolean('locked')->default(false);
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained()->onDelete('cascade');
            $table->foreignId('dropped_player_id')->constrained('players');
            $table->foreignId('added_player_id')->constrained('players');
            $table->enum('roster_position', ['QB', 'RB1', 'RB2', 'WR1', 'WR2', 'WR3', 'TE', 'FLEX']);
            $table->datetime('processed_at')->nullable();
            $table->string('transaction_type')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('transaction_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained();
            $table->foreignId('dropped_player_id')->constrained('players');
            $table->foreignId('added_player_id')->constrained('players');
            $table->string('roster_position');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed']);
            $table->text('validation_data')->nullable();
            $table->text('failure_reason')->nullable();
            $table->datetime('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('point_calculation_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained();
            $table->enum('type', ['game_end', 'stat_update', 'correction']);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed']);
            $table->text('affected_entries')->nullable();
            $table->text('calculation_data')->nullable();
            $table->text('failure_reason')->nullable();
            $table->datetime('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('stat_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained();
            $table->foreignId('game_id')->constrained();
            $table->string('stat_type');
            $table->integer('old_value');
            $table->integer('new_value');
            $table->text('description');
            $table->foreignId('admin_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stat_corrections');
        Schema::dropIfExists('point_calculation_queue');
        Schema::dropIfExists('transaction_queue');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('player_stats');
        Schema::dropIfExists('games');
        Schema::dropIfExists('entry_player_history');
        Schema::dropIfExists('entry_player');
        Schema::dropIfExists('entries');
        Schema::dropIfExists('players');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('users');
    }
};
