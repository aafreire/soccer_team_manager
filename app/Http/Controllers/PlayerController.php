<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $query = Player::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->has('is_present')) {
            $query->where('is_present', $request->input('is_present'));
        }

        if ($request->has('is_goalkeeper')) {
            $query->where('is_goalkeeper', $request->input('is_goalkeeper'));
        }

        $query->where('is_deleted', 0);
        $players = $query->paginate(5);

        return response()->json($players);
    }

    public function show($id)
    {
        $player = Player::find($id);

        if ($player) {
            return response()->json($player);
        } else {
            return response()->json(['error' => 'Player not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $player = Player::create($request->all());
        return response()->json($player, 201);
    }

    public function update(Request $request, $id)
    {
        $player = Player::findOrFail($id);
        $player->update($request->all());
        return response()->json($player, 200);
    }

    public function destroy($id)
    {
        $player = Player::findOrFail($id);
        $player->is_deleted = true;
        $player->save();
        return response()->json(null, 204);
    }

    public function sortTeams(Request $request)
    {
        $players = Player::where('is_present', true)->where('is_deleted', false)->get();

        $playersPerTeam = $request->input('players_per_team');
        $totalTeams = floor($players->count() / $playersPerTeam);

        if ($players->count() < $playersPerTeam * 2) {
            return response()->json(['error' => 'Número insuficiente de jogadores confirmados para esta configuração de times.'], 400);
        }

        $goalkeepers = $players->where('is_goalkeeper', true)->shuffle();
        $fieldPlayers = $players->where('is_goalkeeper', false)->shuffle();

        if ($goalkeepers->count() < $totalTeams) {
            return response()->json(['error' => 'Número insuficiente de goleiros para formar os times.'], 400);
        }

        $teams = [];
        $reserves = [];

        for ($i = 0; $i < $totalTeams; $i++) {
            $teams[$i] = [
                'goalkeeper' => $goalkeepers->pop(),
                'players' => collect(),
                'total_level' => 0,
            ];
        }

        while ($fieldPlayers->isNotEmpty()) {
            foreach ($teams as &$team) {
                if ($team['players']->count() < $playersPerTeam - 1) {
                    $player = $fieldPlayers->pop();
                    if ($player) {
                        $team['players']->push($player);
                        $team['total_level'] += $player->level;
                    }
                } elseif ($fieldPlayers->isNotEmpty()) {
                    $reserves[] = $fieldPlayers->pop();
                }
            }
        }

        $response = [];
        foreach ($teams as $index => &$team) {
            $teamPlayers = $team['players']->prepend($team['goalkeeper']);
            $response['teams'][] = $teamPlayers->all();
        }

        if (!empty($reserves)) {
            $response['reserves'] = $reserves;
        }

        $this->saveTeamSelection($teams, $reserves);

        return response()->json($response);
    }

    private function saveTeamSelection($teams, $reserves)
    {
        $selectionTimestamp = now();
        $gameDate = now()->toDateString();

        DB::transaction(function () use ($teams, $reserves, $selectionTimestamp, $gameDate) {
            foreach ($teams as $index => $team) {
                $teamAverageLevel = round($team['total_level'] / ($team['players']->count() + 1), 2);
                foreach ($team['players'] as $player) {
                    DB::table('team_selections')->insert([
                        'player_id' => $player->id,
                        'game_date' => $gameDate,
                        'team_average_level' => $teamAverageLevel,
                        'selection_id' => $selectionTimestamp,
                        'team_index' => $index + 1,
                        'is_reserve' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            foreach ($reserves as $reserve) {
                DB::table('team_selections')->insert([
                    'player_id' => $reserve->id,
                    'game_date' => $gameDate,
                    'team_average_level' => 0,
                    'selection_id' => $selectionTimestamp,
                    'team_index' => 0,
                    'is_reserve' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}
